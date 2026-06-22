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
        $questions = Question::where('subject_id', $subject_id)
            ->where(DB::raw('LOWER(difficulty)'), strtolower(trim($difficulty))) // 🟢 Forces case-insensitive matching
            ->inRandomOrder()
            ->limit(10)
            ->get();

        // 🔴 CHANGE THIS BLOCK:
    if ($questions->count() < 2) { // Ensure there are enough questions to form a PvP arena match
        return redirect()->route('student.quizzes.difficulties', $subject_id)
                         ->with('error', "No questions found for " . ucfirst($difficulty) . " level in Akhlak. Please populate the question pool first!");
    }

        $quiz = Quiz::create([
            'title' => "PVP: " . strtoupper($difficulty),
            'subject_id' => $subject_id,
            'difficulty' => $difficulty,
            'topic' => 'PVP_ARENA_BATTLE',
            'teacher_id' => Auth::id(), 
        ]);

        foreach ($questions as $q) {
            $quiz->questions()->attach($q->id);
        }

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
            'status' => 'waiting'
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

            if ($count < 2) {
                return response()->json([
                    'error' => 'Waiting for more warriors... You need at least 2 players to start a battle!'
                ], 400); 
            }
            
            // 🟢 DYNAMIC HP SCALING: 100 HP per participant (e.g., 3 players = 300 HP starting pool)
            $startHp = $count * 100;

            foreach ($participants as $p) {
                $p->update([
                    'status' => 'active',
                    'hp' => $startHp,
                    'mp' => 50,
                    'skills_locked_turns' => 0, 
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
            $room->delete(); 
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
        $room = QuizRoom::with(['quiz.questions.options', 'participants.user'])
                        ->where('room_code', $code)
                        ->firstOrFail();
        
        $me = RoomParticipant::where('room_id', $room->id)
                             ->where('user_id', Auth::id())
                             ->firstOrFail();

        return view('users.quizzes.pvp_arena', compact('room', 'me'));
    }
}