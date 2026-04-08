<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Group;
use App\Models\User;
use App\Models\Timetable;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request, $type = null, $id = null)
    {
        $user = Auth::user();
        $role = $user->role;
        $search = $request->input('search');

        // --- 1. PREPARE SIDEBAR CONTACTS ---
        $groups = collect();
        $contacts = collect();
        $teachers = collect(); // For Teacher-to-Teacher chat

        // A. GROUPS LOGIC
        if ($role === 'teacher') {
            $groupIds = Timetable::where('teacher_id', $user->id)->pluck('group_id')->unique();
            $groups = Group::whereIn('id', $groupIds)->get();
        } elseif ($role === 'student' && $user->group_id) {
            $groups = Group::where('id', $user->group_id)->get();
        }

        // B. CONTACTS LOGIC (Students & Teachers)
        $query = User::query();

        if ($role === 'teacher') {
            // Teachers see: 1. Their Students 2. Other Teachers
            $myGroupIds = Timetable::where('teacher_id', $user->id)->pluck('group_id');
            
            $query->where(function($q) use ($myGroupIds, $user) {
                // Students in my groups
                $q->where('role', 'student')->whereIn('group_id', $myGroupIds)
                // OR other teachers (exclude self)
                  ->orWhere(function($t) use ($user) {
                      $t->where('role', 'teacher')->where('id', '!=', $user->id);
                  });
            });
        } elseif ($role === 'student') {
            // Students see: Teachers teaching their group
            $teacherIds = Timetable::where('group_id', $user->group_id)->pluck('teacher_id');
            $query->whereIn('id', $teacherIds);
        }

        // C. APPLY SEARCH (Name, Phone, Email)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%")
                  ->orWhere('phone_number', 'LIKE', "%$search%")
                  ->orWhere('email', 'LIKE', "%$search%");
            });
        }

        $contacts = $query->orderBy('name')->get();

        // --- 2. LOAD ACTIVE CHAT MESSAGES ---
        $activeChat = null;
        $messages = collect();

        if ($type && $id !== null) {
            if ($type === 'global') {
                $activeChat = (object)['name' => 'Global Announcement', 'type' => 'global'];
                $messages = Message::where('type', 'global')
                            ->with('sender')
                            ->orderBy('created_at', 'asc')
                            ->get();
            }
            elseif ($type === 'group') {
                $activeChat = Group::find($id);
                if($activeChat) {
                    $messages = Message::where('type', 'group')
                                ->where('target_id', $id)
                                ->with('sender')
                                ->orderBy('created_at', 'asc')
                                ->get();
                }
            } 
            elseif ($type === 'private') {
                $activeChat = User::find($id);
                if($activeChat) {
                    $messages = Message::where('type', 'private')
                                ->where(function($q) use ($user, $id) {
                                    $q->where('sender_id', $user->id)->where('target_id', $id);
                                })
                                ->orWhere(function($q) use ($user, $id) {
                                    $q->where('sender_id', $id)->where('target_id', $user->id);
                                })
                                ->with('sender')
                                ->orderBy('created_at', 'asc')
                                ->get();
                }
            }
        }

        return view('messages.index', compact('groups', 'contacts', 'activeChat', 'messages', 'type', 'id', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'message'   => 'required|string',
            'type'      => 'required|in:group,private,global',
            'target_id' => 'required|integer', // 0 for global
        ]);

        Message::create([
            'sender_id' => Auth::id(),
            'type'      => $request->type,
            'target_id' => $request->target_id,
            'subject'   => 'Chat',
            'message'   => $request->message,
        ]);

        return redirect()->route('messages.index', ['type' => $request->type, 'id' => $request->target_id]);
    }
}