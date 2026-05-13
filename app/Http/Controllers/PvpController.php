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
                    // Calculate rank based on how many are already dead
                    $defeatedCount = $participants->where('status', 'defeated')->count();
                    $myRank = $totalPlayers - $defeatedCount;

                    $p->update([
                        'status' => 'defeated',
                        'rank' => $myRank,
                        'hp' => 0
                    ]);
                }
            }

            // 3. 🏁 VICTORY DETECTION
            $aliveAndActive = $participants->where('status', 'active')->where('hp', '>', 0);
            
            if ($room->status === 'active' && $aliveAndActive->count() <= 1) {
                // Set the survivor as Rank #1
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
        $room = QuizRoom::where('room_code', $code)->first();
        $me = RoomParticipant::where('room_id', $room->id)->where('user_id', Auth::id())->first();
        
        // 🛑 Block dead players
        if ($me->status !== 'active') return response()->json(['error' => 'Defeated.'], 403);

        if ($me->strike_locked_until && now()->lessThan($me->strike_locked_until)) {
            return response()->json(['error' => 'RECOVERING...'], 403);
        }

        $isCorrect = $this->checkAnswer($request->question_id, $request->answer, $request->question_type);
        $timeLeft = (int)$request->time_left;

        if ($isCorrect) {
            $dmg = ($timeLeft < 30) ? 10 : 20;
            if ($me->active_boost) { $dmg *= 2; $me->update(['active_boost' => false]); }

            $opponents = RoomParticipant::where('room_id', $room->id)->where('user_id', '!=', Auth::id())->where('status', 'active')->get();
            foreach ($opponents as $opp) {
                if ($opp->is_shielded) {
                    $opp->update(['is_shielded' => false]);
                } else {
                    $opp->decrement('hp', $dmg);
                    if ($opp->hp < 0) $opp->update(['hp' => 0]);
                }
            }
            $me->increment('mp', 15);
        } else {
            $penalty = $me->active_boost ? 20 : 10;
            $me->decrement('hp', $penalty);
            if ($me->hp < 0) $me->update(['hp' => 0]);
            $me->update(['active_boost' => false]);
        }

        if ($me->skills_locked_turns > 0) $me->decrement('skills_locked_turns');

        return response()->json(['is_correct' => $isCorrect]);
    }

    /**
     * POWER SYSTEM: Handles Freeze and other abilities.
     */
    public function usePower(Request $request, $code)
    {
        $room = QuizRoom::where('room_code', $code)->first();
        $me = RoomParticipant::where('room_id', $room->id)->where('user_id', Auth::id())->first();
        
        if ($me->status !== 'active') return response()->json(['error' => 'Disabled.'], 403);

        $costs = ['heal' => 80, 'shield' => 60, 'freeze' => 40, 'boost' => 20];
        $type = $request->power_type;

        if ($me->skills_locked_turns > 0) return response()->json(['error' => 'Skills Locked!'], 403);
        if ($me->mp < $costs[$type]) return response()->json(['error' => 'No Mana'], 403);
        
        $me->decrement('mp', $costs[$type]);

        if ($type === 'heal') {
            $me->increment('hp', 15);
            $me->update(['strike_locked_until' => now()->addSeconds(5)]);
        } elseif ($type === 'freeze') {
            // ❄️ Restore Freeze: Set timestamp for 10 seconds
            RoomParticipant::where('room_id', $room->id)->where('user_id', '!=', Auth::id())
                ->where('status', 'active')
                ->update(['is_frozen' => true, 'frozen_until' => now()->addSeconds(10)]);
        } elseif ($type === 'shield') {
            $me->update(['is_shielded' => true, 'skills_locked_turns' => 2]);
        } elseif ($type === 'boost') {
            $me->update(['active_boost' => true]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * RESULTS: Sorted leaderboard.
     * 🟢 FIXED: Removed orderByRaw('rank ASC') to fix MySQL 8 Reserved Word error.
     */
    public function results($code)
    {
        $room = QuizRoom::where('room_code', $code)->firstOrFail();

        // Use standard orderBy (Laravel automatically adds backticks to escape "rank")
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
        $correct = DB::table('options')->where('question_id', $qId)->where('is_correct', 1)->pluck('id')->toArray();
        $sub = (array)$submitted; sort($correct); sort($sub);
        return (!empty($sub) && $correct === $sub);
    }
}