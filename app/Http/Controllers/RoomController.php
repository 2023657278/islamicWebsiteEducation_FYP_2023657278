<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizRoom;
use App\Models\RoomParticipant;
use App\Models\Subject;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RoomController extends Controller
{
    /**
     * Display a list of public lobbies for a subject.
     */
    public function browse($subject_id)
    {
        $rooms = QuizRoom::where('status', 'waiting')
            ->where('is_public', true)
            ->whereHas('quiz', function($q) use ($subject_id) {
                $q->where('subject_id', $subject_id);
            })
            ->withCount('participants')
            ->get();

        $subject = Subject::findOrFail($subject_id);
        return view('users.quizzes.lobby_browser', compact('rooms', 'subject'));
    }

    /**
     * Create a new PvP Mission from the difficulty selection.
     */
   public function createFromDifficulty(Request $request, $subject_id, $difficulty)
{
    // 1. Fetch questions first to make sure they exist
    // Use trim() to ensure there are no hidden spaces in the difficulty name
    $questions = Question::where('subject_id', $subject_id)
        ->where('difficulty', trim($difficulty))
        ->inRandomOrder()
        ->limit(10)
        ->get();

    // 🛑 If this triggers, it means your SQLyog search has 0 results.
    // Check if Al-Quran ID is really $subject_id and difficulty is exactly $difficulty
    if ($questions->isEmpty()) {
        return back()->with('error', "Database Error: No questions found for $difficulty level in this subject.");
    }

    // 2. Create the Quiz Session
    $quiz = Quiz::create([
        'title' => "PVP: " . strtoupper($difficulty),
        'subject_id' => $subject_id,
        'difficulty' => $difficulty,
        'topic' => 'PVP_ARENA_BATTLE',
        'teacher_id' => Auth::id(), 
    ]);

    // 3. Link Questions (Using your 'Working' loop method)
    foreach ($questions as $q) {
        $quiz->questions()->attach($q->id);
    }

    // 4. Create the Room
    // Ensure 'current_question_index' is in your $fillable in QuizRoom model!
    $room = QuizRoom::create([
        'quiz_id' => $quiz->id,
        'host_id' => Auth::id(),
        'room_code' => strtoupper(Str::random(6)),
        'is_public' => $request->query('is_public', 1),
        'status' => 'waiting',
        'current_question_index' => -1, 
    ]);

    RoomParticipant::create([
        'room_id' => $room->id, 
        'user_id' => Auth::id(), 
        'hp' => 100, 
        'mp' => 50,
        'status' => 'waiting' // 🟢 Set initial status
    ]);

    return redirect()->route('student.quizzes.lobby', $room->room_code);
}

    /**
     * Handle joining a room via code.
     */
    public function join(Request $request)
    {
        $room = QuizRoom::where('room_code', $request->room_code)
                        ->where('status', 'waiting')
                        ->first();

        if (!$room) {
            return back()->with('error', 'Mission not found or already deployed!');
        }
        $userPts = Auth::user()->pvp_points;
        $diff = strtolower($room->quiz->difficulty);

    // 🔒 BACKEND GATE
    if ($diff === 'medium' && $userPts < 100) {
        return back()->with('error', 'You need Silver Rank (100 PTS) to join Medium missions.');
    }
    if ($diff === 'hard' && $userPts < 300) {
        return back()->with('error', 'You need Gold Rank (300 PTS) to join Hard missions.');
    }

        RoomParticipant::firstOrCreate(
            ['room_id' => $room->id, 'user_id' => Auth::id()],
            ['hp' => 100, 'mp' => 50, 'status' => 'waiting']
        );

        return redirect()->route('student.quizzes.lobby', $room->room_code);
    }

    /**
     * The Lobby View.
     */
    public function lobby($code)
    {
        $room = QuizRoom::where('room_code', $code)->firstOrFail();
        $participants = RoomParticipant::where('room_id', $room->id)->with('user')->get();
        return view('users.quizzes.lobby', compact('room', 'participants'));
    }

    /**
     * Polling endpoint to update participants list and game status.
     */
    public function getParticipants($code)
    {
        $room = QuizRoom::where('room_code', $code)->first();
        if (!$room) return response()->json(['status' => 'dismissed']);

        $participants = RoomParticipant::where('room_id', $room->id)->with('user')->get();
        return response()->json(['participants' => $participants, 'status' => $room->status]);
    }

    /**
     * Host starts the mission.
     */
    public function start($code)
{
    $room = QuizRoom::where('room_code', $code)->first();
    if ($room && (int)$room->host_id === (int)Auth::id()) {
        $participants = RoomParticipant::where('room_id', $room->id)->get();
        $count = $participants->count();

        // 🛑 NEW: BLOCK START IF ALONE
        if ($count < 2) {
            return response()->json([
                'error' => 'Waiting for more warriors... You need at least 2 players to start a battle!'
            ], 400); // Return 400 Bad Request
        }
        
        // 🟢 HP SCALING: 50 HP per warrior (Min 100)
        $startHp = $count * 50;

        foreach ($participants as $p) {
            $p->update([
                'status' => 'active',
                'hp' => $startHp,
                'mp' => 50,
                'skills_locked_turns' => 0, // Reset drawbacks
                'is_shielded' => false,
                'active_boost' => false
            ]);
        }

        $room->update(['status' => 'active', 'updated_at' => now()]);
        return response()->json(['success' => true]);
    }
    return response()->json(['error' => 'Unauthorized'], 403);
}

    /**
     * Host dismisses the lobby before starting.
     */
    public function dismiss($code)
    {
        $room = QuizRoom::where('room_code', $code)->first();
        if ($room && (int)$room->host_id === (int)Auth::id()) {
            $room->delete(); // Deleting ensures polling sees 'dismissed'
            return response()->json(['success' => true]);
        }
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    /**
     * Player leaves the lobby or surrenders during battle.
     */
    public function leaveLobby($code)
    {
        $room = QuizRoom::where('room_code', $code)->first();
        if ($room) {
            RoomParticipant::where('room_id', $room->id)->where('user_id', Auth::id())->delete();
            if ((int)$room->host_id === (int)Auth::id()) {
                $room->delete();
            }
        }
        return redirect()->route('student.quizzes.index');
    }

    /**
     * The Battle Arena View.
     */
   public function battleArena($code)
{
    // 🟢 EAGER LOAD the questions and options
    $room = QuizRoom::with(['quiz.questions.options', 'participants.user'])
                    ->where('room_code', $code)
                    ->firstOrFail();
    
    $me = RoomParticipant::where('room_id', $room->id)
                         ->where('user_id', Auth::id())
                         ->firstOrFail();

    return view('users.quizzes.pvp_arena', compact('room', 'me'));
}
}