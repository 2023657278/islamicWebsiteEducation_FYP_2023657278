<?php

namespace App\Http\Controllers;

use App\Models\QuizRoom;
use App\Models\RoomParticipant;
use App\Models\Question;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PvpController extends Controller
{
    /**
     * HEARTBEAT: Syncs HP/MP, handles eliminations, and manages Freeze timers.
     */
    public function getStatus($code)
    {
        try {
            $room = QuizRoom::where('room_code', $code)->first();
            if (!$room) return response()->json(['status' => 'dismissed']);

            $participants = RoomParticipant::where('room_id', $room->id)->with('user')->get();

            // 1. ❄️ FREEZE TIMER LOGIC: Auto-Thaw players
            foreach ($participants as $p) {
                if ($p->is_frozen && $p->frozen_until && now()->greaterThan($p->frozen_until)) {
                    $p->update(['is_frozen' => false, 'frozen_until' => null]);
                }
            }

            // 2. 💀 ELIMINATION & RANKING LOGIC
            $totalPlayers = $participants->count();
            foreach ($participants as $p) {
                if ($p->hp <= 0 && $p->status === 'active') {
                    $defeatedCount = $participants->where('status', 'defeated')->count();
                    $myRank = $totalPlayers - $defeatedCount;

                    $p->update([
                        'status' => 'defeated',
                        'rank' => $myRank,
                        'hp' => 0
                    ]);
                }
            }

            // 3. 🏁 VICTORY DETECTION (Triggers automatic redirect for everyone when match ends)
            $aliveAndActive = $participants->where('status', 'active')->where('hp', '>', 0);
            
            if ($room->status === 'active' && $aliveAndActive->count() <= 1) {
                $winner = $aliveAndActive->first();
                if ($winner) {
                    $winner->update(['rank' => 1]);
                }

                $room->update(['status' => 'finished']);
                $this->awardFinalPoints($participants, $room->quiz->difficulty);
            }

            return response()->json([
                'status' => $room->status,
                'participants' => $participants->map(function($p) {
                    return [
                        'user_id' => $p->user_id,
                        'name' => $p->user->name ?? 'Warrior',
                        'hp' => $p->hp,
                        'mp' => $p->mp,
                        'status' => $p->status,
                        'rank' => $p->rank,
                        'is_frozen' => (bool)$p->is_frozen,
                        'is_shielded' => (bool)$p->is_shielded,
                        'active_boost' => (bool)$p->active_boost,
                        'frozen_until' => $p->frozen_until ? Carbon::parse($p->frozen_until)->toIso8601String() : null,
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
     * COMBAT ENGINE: Handles damage and drawback recovery.
     */
    public function submitStrike(Request $request, $code)
    {
        // 🟢 ADD THIS TEMPORARY LINE HERE
        \Log::info('Strike Received Payload:', $request->all());

        $room = QuizRoom::where('room_code', $code)->first();
        $participants = RoomParticipant::where('room_id', $room->id)->get();
        $me = $participants->where('user_id', Auth::id())->first();
        
        if ($me->status !== 'active') return response()->json(['error' => 'Defeated.'], 403);

        if ($me->strike_locked_until && now()->lessThan($me->strike_locked_until)) {
            return response()->json(['error' => 'RECOVERING...'], 403);
        }

        $isCorrect = $this->checkAnswer($request->question_id, $request->answer, $request->question_type);
        $timeLeft = (int)$request->time_left;

        if ($isCorrect) {
            // 🟢 FIXED: Base damage goes straight into the 100 base HP pool cleanly without fractional loss
            $normalizedDmg = ($timeLeft >= 30) ? 20 : 10;

            if ($me->active_boost) { 
                $normalizedDmg *= 2; 
                $me->update(['active_boost' => false]); 
            }

            $opponents = RoomParticipant::where('room_id', $room->id)->where('user_id', '!=', Auth::id())->where('status', 'active')->get();
            foreach ($opponents as $opp) {
                if ($opp->is_shielded) {
                    $opp->update(['is_shielded' => false]);
                } else {
                    $opp->decrement('hp', $normalizedDmg);
                    if ($opp->hp <= 0) $opp->update(['hp' => 0, 'status' => 'defeated']); 
                }
            }
            $me->increment('mp', 15);
            if ($me->mp > 100) $me->update(['mp' => 100]);
        } else {
            // 🟢 FIXED: Base penalty handles cleanly without dividing down to zero fraction elements
            $normalizedPenalty = $me->active_boost ? 20 : 10;

            $me->decrement('hp', $normalizedPenalty);
            if ($me->hp <= 0) $me->update(['hp' => 0, 'status' => 'defeated']);
            $me->update(['active_boost' => false]);
        }

        if ($me->skills_locked_turns > 0) $me->decrement('skills_locked_turns');

        return response()->json(['is_correct' => $isCorrect]);
    }

    /**
     * POWER SYSTEM: Handles dynamic spell actions.
     */
    public function usePower(Request $request, $code)
    {
        $room = QuizRoom::where('room_code', $code)->first();
        $participants = RoomParticipant::where('room_id', $room->id)->get();
        $me = $participants->where('user_id', Auth::id())->first();
        
        if ($me->status !== 'active') return response()->json(['error' => 'Disabled.'], 403);

        $costs = ['heal' => 40, 'shield' => 40, 'freeze' => 40, 'boost' => 40];
        $type = $request->power_type;

        if ($me->skills_locked_turns > 0) return response()->json(['error' => 'Skills Locked!'], 403);
        if ($me->mp < $costs[$type]) return response()->json(['error' => 'No Mana'], 403);
        
        $me->decrement('mp', $costs[$type]);

        if ($type === 'heal') {
            $totalPlayers = $participants->count();
            $normalizedHeal = (int)round((40 / ($totalPlayers * 100)) * 100);
            if ($normalizedHeal < 1) $normalizedHeal = 5;

            $me->increment('hp', $normalizedHeal);
            if ($me->hp > 100) $me->update(['hp' => 100]); 
            
            $me->update(['strike_locked_until' => now()->addSeconds(3)]);
        } elseif ($type === 'freeze') {
            RoomParticipant::where('room_id', $room->id)->where('user_id', '!=', Auth::id())
                ->where('status', 'active')
                ->update(['is_frozen' => true, 'frozen_until' => now()->addSeconds(10)]);
        } elseif ($type === 'shield') {
            $me->update(['is_shielded' => true, 'skills_locked_turns' => 1]);
        } elseif ($type === 'boost') {
            $me->update(['active_boost' => true]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * RESULTS: Sorted leaderboard.
     */
    public function results($code)
    {
        $room = QuizRoom::where('room_code', $code)->firstOrFail();

        $participants = RoomParticipant::where('room_id', $room->id)
            ->with('user')
            ->orderBy('rank', 'asc')
            ->get();

        return view('users.quizzes.pvp_results', compact('room', 'participants'));
    }

    private function awardFinalPoints($participants, $difficulty)
    {
        foreach ($participants as $p) {
            $change = $this->calculatePointChange($p, $p->rank, $participants->count(), $difficulty);
            $user = User::find($p->user_id);
            if ($user) {
                $newTotal = $user->pvp_points + $change;
                $user->update(['pvp_points' => max(0, $newTotal)]);
            }
        }
    }

    private function calculatePointChange($participant, $rank, $playerCount, $difficulty)
    {
        $change = 0;
        $difficulty = strtolower($difficulty);
        $isWinner = ($rank === 1);

        if ($difficulty === 'easy') {
            $change = ($isWinner) ? 15 : -5;
        } elseif ($difficulty === 'medium') {
            $change = ($isWinner) ? 30 : -15;
        } elseif ($difficulty === 'hard') {
            $change = ($isWinner) ? 70 : -50;
        }
        return $change;
    }

    private function checkAnswer($qId, $submitted, $type) {
        $question = Question::find($qId);
        if ($type === 'text') return strtolower(trim($submitted ?? '')) === strtolower(trim($question->correct_answer_text));
        
        // Handle array conversion for multiple choice matching setups safely
        $correct = DB::table('options')->where('question_id', $qId)->where('is_correct', 1)->pluck('id')->toArray();
        $sub = is_array($submitted) ? $submitted : [$submitted];
        
        // Map elements to integers to bypass loose string comparison filters inside array evaluations
        $correct = array_map('intval', $correct);
        $sub = array_map('intval', array_filter($sub));
        
        sort($correct); sort($sub);
        return (!empty($sub) && $correct === $sub);
    }
}