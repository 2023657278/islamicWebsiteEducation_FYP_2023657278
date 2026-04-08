<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Message;
use App\Models\Timetable;

class StudentMessageController extends Controller
{
    // 1. List Contacts (Teachers, Classmates, Broadcasts)
    public function index()
    {
        $student = Auth::user();

        // A. GET INVOLVED TEACHERS (From Timetable)
        $teacherIds = Timetable::where('group_id', $student->group_id)
                        ->pluck('teacher_id')
                        ->unique();
        
        $teachers = User::whereIn('id', $teacherIds)->get();
        $this->attachLastMessage($teachers, $student);

        // B. GET CLASSMATES (Same Group)
        $classmates = User::where('group_id', $student->group_id)
                        ->where('role', 'student')
                        ->where('id', '!=', $student->id) // Exclude self
                        ->get();
        $this->attachLastMessage($classmates, $student);

        // C. GET BROADCASTS (Announcements)
        $broadcastChannel = new User([
            'id' => 0, 
            'name' => 'Class Announcements',
            'role' => 'system',
            'profile_image' => null
        ]);
        
        $lastBroadcast = Message::where('type', 'broadcast')
                            ->where('target_id', $student->group_id)
                            ->latest()
                            ->first();
        $broadcastChannel->last_message = $lastBroadcast;

        return view('users.messages.index', compact('teachers', 'classmates', 'broadcastChannel'));
    }

    // Helper to attach last message
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

    // 2. Show Chat
    public function show($id)
    {
        $student = Auth::user();

        // HANDLE BROADCAST CHANNEL (ID = 0)
        if ($id == 0) {
            $teacher = new User([
                'id' => 0,
                'name' => 'Class Announcements',
                'role' => 'system'
            ]);
            
            $messages = Message::where('type', 'broadcast')
                            ->where('target_id', $student->group_id)
                            ->orderBy('created_at', 'asc')
                            ->get();
            
            $isBroadcast = true;
            
            return view('users.messages.show', compact('teacher', 'messages', 'isBroadcast'));
        }

        // HANDLE PRIVATE CHAT
        $teacher = User::findOrFail($id);

        $isTeacher = Timetable::where('group_id', $student->group_id)->where('teacher_id', $id)->exists();
        $isClassmate = ($teacher->group_id == $student->group_id && $teacher->role == 'student');

        if (!$isTeacher && !$isClassmate) {
            return redirect()->route('student.messages.index')->with('error', 'You cannot message this user.');
        }

        $messages = Message::where(function($q) use ($student, $teacher) {
                            $q->where('sender_id', $student->id)->where('target_id', $teacher->id);
                        })->orWhere(function($q) use ($student, $teacher) {
                            $q->where('sender_id', $teacher->id)->where('target_id', $student->id);
                        })
                        ->orderBy('created_at', 'asc')
                        ->get();

        $isBroadcast = false;

        return view('users.messages.show', compact('teacher', 'messages', 'isBroadcast'));
    }

    // 3. Send Message (FIXED)
    public function store(Request $request, $id)
    {
        if ($id == 0) {
            return back()->with('error', 'You cannot reply to announcements.');
        }

        $request->validate(['message' => 'required|string']);

        Message::create([
            'sender_id' => Auth::id(),
            'target_id' => $id,
            'type'      => 'private',
            'subject'   => 'Private Chat', // ✅ ADDED THIS LINE TO FIX ERROR 1364
            'message'   => $request->message,
        ]);

        return back();
    }
}