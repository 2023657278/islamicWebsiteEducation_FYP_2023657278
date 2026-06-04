<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Message;
use App\Models\Timetable;

class StudentMessageController extends Controller
{
    /**
     * 1. List Contacts & Load Active Chats Dynamically
     */
    public function index(Request $request)
    {
        $student = Auth::user();
        
        // Extract optional routing type parameters from active background stream
        $type = $request->query('type', 'global');
        $id = $request->query('id', 0);

        // A. GET TEACHERS
        $teacherIds = Timetable::where('group_id', $student->group_id)->pluck('teacher_id')->unique();
        $teachers = User::whereIn('id', $teacherIds)->get();
        $this->attachLastMessage($teachers, $student);

        // B. GET CLASSMATES
        $classmates = User::where('group_id', $student->group_id)
                            ->where('role', 'student')
                            ->where('id', '!=', $student->id)
                            ->get();
        $this->attachLastMessage($classmates, $student);

        // C. CHANNEL 1: SCHOOL ANNOUNCEMENTS (Global)
        $globalChannel = new User(['id' => 0, 'name' => 'School Announcements', 'role' => 'system']);
        $globalChannel->last_message = Message::where('type', 'global')->latest()->first();

        // D. CHANNEL 2: CLASS ANNOUNCEMENTS (Group)
        $groupChannel = new User(['id' => 0, 'name' => 'Class Announcements', 'role' => 'system']);
        $groupChannel->last_message = Message::where('type', 'group')
                                            ->where('target_id', $student->group_id)
                                            ->latest()->first();

        // E. EVALUATE RESOLUTION OF ACTIVE SELECTED CHAT STREAM
        $activeChat = null;
        $messages = collect();

        if ($type === 'global') {
            $activeChat = (object)['id' => 'global', 'name' => 'School Announcements', 'role' => 'system', 'email' => 'All Students Broadcast'];
            $messages = Message::where('type', 'global')->with('sender')->orderBy('created_at', 'asc')->get();
        } elseif ($type === 'group') {
            $activeChat = \App\Models\Group::find($id);
            if ($activeChat) {
                $messages = Message::where('type', 'group')
                                   ->where('target_id', $id)
                                   ->with('sender')
                                   ->orderBy('created_at', 'asc')
                                   ->get();
            }
        } elseif ($type === 'private') {
            $activeChat = User::find($id);
            if ($activeChat) {
                $messages = Message::where(function($q) use ($student, $id) {
                                    $q->where('sender_id', $student->id)->where('target_id', $id);
                                })->orWhere(function($q) use ($student, $id) {
                                    $q->where('sender_id', $id)->where('target_id', $student->id);
                                })
                                ->with('sender')
                                ->orderBy('created_at', 'asc')
                                ->get();
            }
        }

        // Build list collection array parameters for search filtering mapping 
        $contacts = $teachers->concat($classmates);
        $groups = \App\Models\Group::where('id', $student->group_id)->get();

        // 🟢 THE CRITICAL FIX: Detect background AJAX requests
        // If request is AJAX, return only the chat window layout contents to avoid broken duplicates
        if ($request->ajax() || $request->hasHeader('X-Requested-With')) {
            return view('users.messages.index', compact(
                'teachers', 'classmates', 'globalChannel', 'groupChannel', 
                'activeChat', 'messages', 'type', 'id', 'contacts', 'groups'
            ))->fragment('chatArea-content');
        }

        return view('users.messages.index', compact(
            'teachers', 'classmates', 'globalChannel', 'groupChannel', 
            'activeChat', 'messages', 'type', 'id', 'contacts', 'groups'
        ));
    }

    private function attachLastMessage($users, $student)
    {
        foreach ($users as $user) {
            $user->last_message = Message::where(function($q) use ($student, $user) {
                $q->where('sender_id', $student->id)->where('target_id', $user->id);
            })->orWhere(function($q) use ($student, $user) {
                $q->where('sender_id', $user->id)->where('target_id', $student->id);
            })->latest()->first();
        }
    }

    /**
     * 2. Send Message
     */
    public function store(Request $request)
    {
        $student = Auth::user();
        $request->validate([
            'message' => 'required|string',
            'type' => 'required|string',
            'target_id' => 'required'
        ]);

        $type = $request->input('type');
        $targetId = $request->input('target_id');

        if ($type === 'global') {
            return response()->json(['status' => 'error', 'message' => 'You cannot reply to school-wide announcements.'], 403);
        }

        // Save Group Chat
        if ($type === 'group') {
            Message::create([
                'sender_id' => $student->id,
                'target_id' => $student->group_id,
                'type'      => 'group',
                'subject'   => 'Group Chat',
                'message'   => $request->message,
            ]);
            return response()->json(['status' => 'success']);
        }

        // Save Private Message
        Message::create([
            'sender_id' => $student->id,
            'target_id' => $targetId,
            'type'      => 'private',
            'subject'   => 'Private Chat',
            'message'   => $request->message,
        ]);

        return response()->json(['status' => 'success']);
    }
}