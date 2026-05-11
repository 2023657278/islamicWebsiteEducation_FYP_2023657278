<?php

namespace App\Http\Controllers;

use App\Models\QuizRoom;
use App\Models\RoomParticipant;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PvpController extends Controller
{
    /**
     * HEARTBEAT: Syncs HP/MP and triggers Victory Redirect.
     */
    public function getStatus($code)
{
    try {
        $room = QuizRoom::where('room_code', $code)->first();
        if (!$room) return response()->json(['status' => 'dismissed']);

        $participants = RoomParticipant::where('room_id', $room->id)->with('user')->get();

        // ❄️ Auto-Thaw players
        foreach ($participants as $p) {
            if ($p->is_frozen && $p->frozen_until && now()->greaterThan($p->frozen_until)) {
                $p->update(['is_frozen' => false, 'frozen_until' => null]);
            }
        }

        // 🏁 VICTORY DETECTION FIX
        // We count players who are 'active' (not surrendered) and have HP > 0
        $aliveAndActive = $participants->where('status', 'active')->where('hp', '>', 0);
        
        // If the battle is currently "active" but only 1 warrior is left standing
        // We set the room to "finished" so the page can redirect.
        if ($room->status === 'active' && $aliveAndActive->count() <= 1) {
            // 🟢 STEP 1: MARK ROOM AS FINISHED
            $room->update(['status' => 'finished']);
            $room->refresh(); 

            // 🏆 STEP 2: AWARD RANKING POINTS (ADD THIS HERE)
            $sortedParticipants = $participants->sortByDesc('hp')->values();
            $playerCount = $participants->count();
            $difficulty = strtolower($room->quiz->difficulty);

            foreach ($sortedParticipants as $index => $p) {
    $pointsChange = $this->calculatePointChange($p, $index + 1, $playerCount, $difficulty);
    
    $user = $p->user;
    
    // Calculate new total
    $newTotal = $user->pvp_points + $pointsChange;

    // 🟢 Apply Floor Zero: Ensure it never goes below 0
    $user->update([
        'pvp_points' => ($newTotal < 0) ? 0 : $newTotal
    ]);
}
        }

       return response()->json([
                'status' => $room->status,
                'participants' => $participants->map(function($p) {
                    return [
                        'user_id' => $p->user_id,
                        'name' => $p->user->name ?? 'Unknown',
                        'hp' => $p->hp,
                        'mp' => $p->mp,
                        'status' => $p->status,
                        'is_frozen' => (bool)$p->is_frozen,
                        'is_shielded' => (bool)$p->is_shielded,
                        'active_boost' => (bool)$p->active_boost,
                        'frozen_until' => $p->frozen_until ? Carbon::parse($p->frozen_until)->toIso8601String() : null,
                        // 🔒 Drawback flags for UI
                        'strike_locked' => $p->strike_locked_until && now()->lessThan($p->strike_locked_until),
                        'abilities_locked' => $p->skills_locked_turns > 0
                    ];
                })
            ]);
    } catch (\Exception $e) { 
        return response()->json(['error' => 'Sync Error'], 500); 
    }
}

    /**
     * COMBAT ENGINE: Handles damage and Drawback turn-counting.
     */
    public function submitStrike(Request $request, $code)
    {
        $room = QuizRoom::where('room_code', $code)->first();
        $me = RoomParticipant::where('room_id', $room->id)->where('user_id', Auth::id())->first();
        
        // 🔒 HEAL DRAWBACK CHECK: Hard block strike if locked
        if ($me->strike_locked_until && now()->lessThan($me->strike_locked_until)) {
            return response()->json(['error' => 'RECOVERING... Strike disabled.'], 403);
        }

        $isCorrect = $this->checkAnswer($request->question_id, $request->answer, $request->question_type);
        $timeLeft = (int)$request->time_left;

        if ($isCorrect) {
            // Damage scaling: 20 base, 10 if late
            $dmg = ($timeLeft < 30) ? 10 : 20;

            // 🔴 BOOST: Double Damage
            if ($me->active_boost) { 
                $dmg *= 2; 
                $me->update(['active_boost' => false]); 
            }

            // 🔵 FREEZE: Half dmg while anyone is frozen
            if (RoomParticipant::where('room_id', $room->id)->where('is_frozen', true)->exists()) {
                $dmg = floor($dmg / 2);
            }

            $opponents = RoomParticipant::where('room_id', $room->id)->where('user_id', '!=', Auth::id())->where('status', 'active')->get();
            foreach ($opponents as $opp) {
                if ($opp->is_shielded) {
                    $opp->update(['is_shielded' => false]); // Shield breaks
                } else {
                    $opp->decrement('hp', $dmg);
                    if ($opp->hp < 0) $opp->update(['hp' => 0]);
                }
            }
            $me->increment('mp', 15);
        } else {
            // 🔴 MISS PENALTY: 2x Self-Damage if boost was on
            $penalty = $me->active_boost ? 20 : 10;
            $me->decrement('hp', $penalty);
            if ($me->hp < 0) $me->update(['hp' => 0]);
            $me->update(['active_boost' => false]);
        }

        // 🟢 SHIELD DRAWBACK: Reduce turn counter after strike
        if ($me->skills_locked_turns > 0) {
            $me->decrement('skills_locked_turns');
        }

        return response()->json(['is_correct' => $isCorrect]);
    }

    /**
     * POWER SYSTEM: Mana logic and applying Drawbacks.
     */
    public function usePower(Request $request, $code)
    {
        $room = QuizRoom::where('room_code', $code)->first();
        $me = RoomParticipant::where('room_id', $room->id)->where('user_id', Auth::id())->first();
        
        $costs = ['heal' => 80, 'shield' => 60, 'freeze' => 40, 'boost' => 20];
        $type = $request->power_type;

        // ❌ LOCK CHECK: Cannot use skills if shield drawback is active
        if ($me->skills_locked_turns > 0) return response()->json(['error' => 'Skills Locked!'], 403);
        if ($me->mp < $costs[$type]) return response()->json(['error' => 'No Mana'], 403);
        
        $me->decrement('mp', $costs[$type]);

        if ($type === 'heal') {
            $me->increment('hp', 15);
            // 🟡 HEAL DRAWBACK: Lock Strike for 5 seconds
            $me->update(['strike_locked_until' => now()->addSeconds(5)]);
        } elseif ($type === 'freeze') {
            RoomParticipant::where('room_id', $room->id)->where('user_id', '!=', Auth::id())
                ->update(['is_frozen' => true, 'frozen_until' => now()->addSeconds(10)]);
        } elseif ($type === 'shield') {
            // 🟢 SHIELD DRAWBACK: Set counter to 2 questions
            $me->update(['is_shielded' => true, 'skills_locked_turns' => 2]);
        } elseif ($type === 'boost') {
            $me->update(['active_boost' => true]);
        }

        return response()->json(['success' => true]);
    }

    public function results($code)
    {
        $room = QuizRoom::with(['participants.user'])->where('room_code', $code)->firstOrFail();
        $participants = $room->participants->sortByDesc(fn($p) => ($p->status === 'surrendered') ? -100 : $p->hp);
        return view('users.quizzes.pvp_results', compact('room', 'participants'));
    }

    private function calculatePointChange($participant, $rank, $playerCount, $difficulty)
{
    $change = 0;
    $difficulty = strtolower($difficulty);
    $isWinner = ($rank === 1 && $participant->hp > 0);
    $isLoser = ($participant->hp <= 0 || $participant->status === 'surrendered');

    if ($difficulty === 'easy') {
        if ($playerCount >= 5) {
            if ($rank === 1) $change = 15;
            elseif ($rank === 2) $change = 10;
            elseif ($rank === 3) $change = 5;
        } else {
            if ($isWinner) $change = 15;
            if ($isLoser) $change = -5;
        }
    } 
    elseif ($difficulty === 'medium') {
        if ($playerCount >= 5) {
            if ($rank === 1) $change = 30;
            elseif ($rank === 2) $change = 20;
            elseif ($rank === 3) $change = 15;
        } else {
            if ($isWinner) $change = 20;
            if ($isLoser) $change = -15;
        }
    } 
    elseif ($difficulty === 'hard') {
        if ($playerCount >= 5) {
            if ($rank === 1) $change = 70;
            elseif ($rank === 2) $change = 60;
            elseif ($rank === 3) $change = 50;
        } else {
            if ($isWinner) $change = 50;
            if ($isLoser) $change = -50;
        }
    }

    return $change;
}

    private function checkAnswer($qId, $submitted, $type) {
        $question = Question::find($qId);
        if ($type === 'text') return strtolower(trim($submitted ?? '')) === strtolower(trim($question->correct_answer_text));
        $correct = DB::table('options')->where('question_id', $qId)->where('is_correct', 1)->pluck('id')->toArray();
        $sub = (array)$submitted; sort($correct); sort($sub);
        return (!empty($sub) && $correct === $sub);
    }
}