<?php

namespace App\Http\Controllers;

use App\Models\QuizRoom;
use App\Models\RoomParticipant;
use App\Models\Question;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PvpController extends Controller
{
    /**
     * 1. THE ARENA LOADER
     * Shows the battle screen to the student.
     */
    public function arena($code)
{
    $room = QuizRoom::with(['quiz.questions.options', 'participants.user'])
        ->where('room_code', $code)
        ->firstOrFail();

    // This is vital so the UI knows which HP bar belongs to "YOU"
    $me = RoomParticipant::where('room_id', $room->id)
        ->where('user_id', Auth::id())
        ->first();

    return view('users.quizzes.pvp_arena', compact('room', 'me'));
}

    /**
     * 2. THE HEARTBEAT (SYNC)
     * Every 2 seconds, every student's browser calls this.
     */
    public function getStatus($code)
    {
        $room = QuizRoom::where('room_code', $code)->first();
        
        // Get all participants sorted by HP to show ranking
        $participants = RoomParticipant::where('room_id', $room->id)
            ->with('user')
            ->orderBy('hp', 'desc')
            ->get();

        return response()->json([
            'status' => $room->status,
            'current_index' => $room->current_question_index,
            'participants' => $participants
        ]);
    }

    /**
     * 3. THE STRIKE (SUBMIT ANSWER)
     * Calculates damage based on how fast the student answered.
     */
    public function submitStrike(Request $request, $code)
{
    $room = QuizRoom::with('quiz')->where('room_code', $code)->first();
    $isCorrect = DB::table('options')->where('id', $request->answer)->where('is_correct', 1)->exists();

    if ($isCorrect) {
        // 🟢 AOE Damage: The faster you are, the more damage you deal!
        $damage = 10; 
        RoomParticipant::where('room_id', $room->id)
            ->where('user_id', '!=', Auth::id())
            ->decrement('hp', $damage);

        // Give Mana to the attacker
        RoomParticipant::where('room_id', $room->id)
            ->where('user_id', Auth::id())
            ->increment('mp', 5);
    } else {
        // 🔴 Backfire: Wrong answer hurts YOU
        RoomParticipant::where('room_id', $room->id)
            ->where('user_id', Auth::id())
            ->decrement('hp', 5);
    }

    return response()->json(['is_correct' => $isCorrect]);
}

    /**
     * 4. NEXT ROUND (HOST ONLY)
     */
    public function nextRound($code)
    {
        $room = QuizRoom::where('room_code', $code)->first();
        if ($room->host_id == Auth::id()) {
            $room->increment('current_question_index');
            $room->touch(); // Updates 'updated_at' to reset the round timer
            return response()->json(['success' => true]);
        }
        return response()->json(['error' => 'Unauthorized'], 403);
    }
}