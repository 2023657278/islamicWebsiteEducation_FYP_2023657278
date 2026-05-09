<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizRoom;
use App\Models\RoomParticipant;
use App\Models\Subject;
use App\Models\Question; // Add this import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;



class RoomController extends Controller
{
    // Teacher creates a room
    public function create($quiz_id)
    {
        $room = QuizRoom::create([
            'quiz_id' => $quiz_id,
            'room_code' => strtoupper(Str::random(6)), // e.g., BT-X921
            'status' => 'waiting'
        ]);

        return redirect()->route('student.quizzes.lobby', $room->room_code);
    }

    // Student joins a room
    public function join(Request $request)
    {
        $room = QuizRoom::where('room_code', $request->room_code)
                        ->where('status', 'waiting')
                        ->first();

        if (!$room) {
            return back()->with('error', 'Room not found or already started!');
        }

        // Add student to the room
        RoomParticipant::firstOrCreate([
            'room_id' => $room->id,
            'user_id' => Auth::id()
        ]);

        return redirect()->route('student.quizzes.lobby', $room->room_code);
    }

    // The Lobby View
    public function lobby($code)
    {
        $room = QuizRoom::where('room_code', $code)->firstOrFail();
        $participants = RoomParticipant::where('room_id', $room->id)->with('user')->get();
        
        return view('users.quizzes.lobby', compact('room', 'participants'));
    }

    public function browse($subject_id)
{
    $rooms = QuizRoom::where('status', 'waiting')
        ->where('is_public', true) // 🟢 Only show public rooms
        ->whereHas('quiz', function($q) use ($subject_id) {
            $q->where('subject_id', $subject_id);
        })
        ->withCount('participants')
        ->get();

    $subject = Subject::findOrFail($subject_id);
    return view('users.quizzes.lobby_browser', compact('rooms', 'subject'));
}

public function createFromDifficulty(Request $request, $subject_id, $difficulty)
{
    // 1. Create a "Template" Quiz for this Battle
    $quiz = Quiz::create([
        'title' => "PVP Arena: $difficulty",
        'subject_id' => $subject_id,
        'difficulty' => $difficulty,
        'topic' => 'PVP_ARENA_BATTLE', // Keep this tag for isolation
        'teacher_id' => Auth::id(), 
    ]);

    // 🟢 2. THE FIX: Attach 10 random questions to this quiz
    $questions = Question::where('subject_id', $subject_id)
        ->where('difficulty', $difficulty)
        ->inRandomOrder()
        ->limit(10)
        ->get();

    // Attach them to the pivot table (standard Laravel many-to-many)
    foreach ($questions as $q) {
        $quiz->questions()->attach($q->id);
    }

    // 3. Create the Room with visibility
    $room = QuizRoom::create([
        'quiz_id' => $quiz->id,
        'host_id' => Auth::id(),
        'room_code' => strtoupper(Str::random(6)),
        'is_public' => $request->query('is_public', 1), // 1 = Public, 0 = Private
        'status' => 'waiting',
    ]);

    // 4. Host joins as first participant
    RoomParticipant::create([
        'room_id' => $room->id,
        'user_id' => Auth::id(),
        'hp' => 100,
        'mp' => 50
    ]);

    return redirect()->route('student.quizzes.lobby', $room->room_code);
}

// 🟢 START MISSION: Host only
public function start($code)
{
    $room = QuizRoom::where('room_code', $code)->first();
    if ($room && $room->host_id == Auth::id()) {
        $room->update(['status' => 'active']); // 👈 MUST be 'active'
        return response()->json(['success' => true]);
    }
    return response()->json(['error' => 'Unauthorized'], 403);
}

// 🟢 DISMISS LOBBY: Host only
public function dismiss($code)
{
    $room = QuizRoom::where('room_code', $code)->first();

    if (!$room) {
        return response()->json(['message' => 'Lobby not found'], 404);
    }

    // Ensure only the host can pull the plug
    if ((int)$room->host_id === (int)Auth::id()) {
        
        // 🟢 HARD DELETE: Removes the room and its participants completely from DB
        $room->delete(); 
        
        return response()->json(['success' => true]);
    }

    return response()->json(['message' => 'Unauthorized Access'], 403);
}

public function battleArena($code)
{
    $room = QuizRoom::with(['quiz.questions.options', 'participants.user'])->where('room_code', $code)->firstOrFail();
    $me = RoomParticipant::where('room_id', $room->id)->where('user_id', auth()->id())->first();

    return view('users.quizzes.pvp_arena', compact('room', 'me'));
}

// AJAX: Checks if all 20 players are done or if time is up
public function checkRoundStatus($code)
{
    $room = QuizRoom::where('room_code', $code)->first();
    $participants = RoomParticipant::where('room_id', $room->id)->with('user')->get();
    
    // We will build the logic here to see who has submitted for the current question
    return response()->json([
        'status' => $room->status,
        'current_index' => $room->current_question_index,
        'participants' => $participants
    ]);
}

    // AJAX Endpoint: Get list of participants
    public function getParticipants($code)
    {
        $room = QuizRoom::where('room_code', $code)->first();
        $participants = RoomParticipant::where('room_id', $room->id)
                                        ->with('user')
                                        ->get();

        return response()->json([
            'participants' => $participants,
            'status' => $room->status
        ]);
    }

}