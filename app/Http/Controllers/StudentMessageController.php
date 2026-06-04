<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Message;
use App\Models\Timetable;

class StudentMessageController extends Controller
{
    // 1. List Contacts (Teachers, Classmates, Announcements)
    public function index()
    {
        $student = Auth::user();

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

        return view('users.messages.index', compact('teachers', 'classmates', 'globalChannel', 'groupChannel'));
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

    // 2. Show Chat (FIXED: Added 'id' to dummy objects)
    public function show($id)
    {
        $student = Auth::user();

        // SCHOOL ANNOUNCEMENTS (Read-Only)
        if ($id === 'global') {
            $teacher = (object)[
                'id' => 'global', // Added ID to fix the error
                'name' => 'School Announcements', 
                'role' => 'system'
            ];
            $messages = Message::where('type', 'global')->with('sender')->orderBy('created_at', 'asc')->get();
            $isBroadcast = true;
            return view('users.messages.show', compact('teacher', 'messages', 'isBroadcast'));
        }

        // CLASS ANNOUNCEMENTS (Class Chat)
        if ($id === 'group') {
            $teacher = (object)[
                'id' => 'group', // Added ID to fix the error
                'name' => 'Class Announcements', 
                'role' => 'system'
            ];
            $messages = Message::where('type', 'group')
                               ->where('target_id', $student->group_id)
                               ->with('sender')
                               ->orderBy('created_at', 'asc')
                               ->get();
            
            // Set to false to show the chat input box
            $isBroadcast = false; 
            
            return view('users.messages.show', compact('teacher', 'messages', 'isBroadcast'));
        }

        // PRIVATE CHAT
        $teacher = User::findOrFail($id);
        $messages = Message::where(function($q) use ($student, $teacher) {
                        $q->where('sender_id', $student->id)->where('target_id', $teacher->id);
                    })->orWhere(function($q) use ($student, $teacher) {
                        $q->where('sender_id', $teacher->id)->where('target_id', $student->id);
                    })
                    ->with('sender')
                    ->orderBy('created_at', 'asc')
                    ->get();

        $isBroadcast = false;
        return view('users.messages.show', compact('teacher', 'messages', 'isBroadcast'));
    }

    // 3. Send Message
    public function store(Request $request, $id)
    {
        $student = Auth::user();
        $request->validate(['message' => 'required|string']);

        if ($id === 'global') {
            return back()->with('error', 'You cannot reply to school-wide announcements.');
        }

        // Save to Group Chat
        if ($id === 'group') {
            Message::create([
                'sender_id' => $student->id,
                'target_id' => $student->group_id,
                'type'      => 'group',
                'subject'   => 'Group Chat',
                'message'   => $request->message,
            ]);
            return back();
        }

        // Save Private Message
        Message::create([
            'sender_id' => $student->id,
            'target_id' => $id,
            'type'      => 'private',
            'subject'   => 'Private Chat',
            'message'   => $request->message,
        ]);

        return back();
    }
}