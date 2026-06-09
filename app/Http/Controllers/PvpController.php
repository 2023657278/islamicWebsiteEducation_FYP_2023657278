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
            // 🟢 FIXED: Calculate rank cleanly based on active health states to prevent tied ranks
            $totalPlayers = $participants->count();
            foreach ($participants as $p) {
                if ($p->hp <= 0 && $p->status === 'active') {
                    // Count how many players have strictly MORE health than this player right now
                    $playersWithMoreHp = $participants->where('hp', '>', 0)->count();
                    $myRank = $playersWithMoreHp + 1;

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
        \Log::info('Strike Received Payload:', $request->all());

        $room = QuizRoom::where('room_code', $code)->first();
        if (!$room) return response()->json(['error' => 'Room not found'], 404);

        $me = RoomParticipant::where('room_id', $room->id)
                             ->where('user_id', Auth::id())
                             ->first();
        
        if (!$me || $me->status !== 'active') {
            return response()->json(['error' => 'Defeated.'], 403);
        }

        if ($me->strike_locked_until && now()->lessThan($me->strike_locked_until)) {
            return response()->json(['error' => 'RECOVERING...'], 403);
        }

        $isCorrect = $this->checkAnswer($request->question_id, $request->answer, $request->question_type);
        $timeLeft = (int)$request->time_left;

        if ($isCorrect) {
            // 🟢 DAMAGE RULE: 20 damage if time >= 30s, 10 damage if below 30s
            $normalizedDmg = ($timeLeft >= 30) ? 20 : 10;

            // 🟢 BOOST RULE: 2x damage if correct
            if ($me->active_boost) { 
                $normalizedDmg *= 2; 
                $me->update(['active_boost' => false]); 
            }

            // 🟢 SHIELD RULE: Attacking while shielded deals half damage to opponents
            if ($me->is_shielded) {
                $normalizedDmg = (int)round($normalizedDmg / 2);
            }
            
            // 🟢 SHIELD DAMAGE BLOCK: Shield blocks damage 1 time from an opponent, then breaks
            DB::table('room_participants')
                ->where('room_id', $room->id)
                ->where('user_id', '!=', Auth::id())
                ->where('status', 'active')
                ->where('is_shielded', true)
                ->update(['is_shielded' => false]);

            // Decrement health for unshielded active opponents
            DB::table('room_participants')
                ->where('room_id', $room->id)
                ->where('user_id', '!=', Auth::id())
                ->where('status', 'active')
                ->where('is_shielded', false)
                ->decrement('hp', $normalizedDmg);

            // Clean up rows that hit 0 HP
            DB::table('room_participants')
                ->where('room_id', $room->id)
                ->where('hp', '<=', 0)
                ->update(['hp' => 0, 'status' => 'defeated']);

            $me->increment('mp', 15);
            if ($me->mp > 100) $me->update(['mp' => 100]);
        } else {
            // 🟢 DAMAGE PENALTY RULE: Base 10 health reduction if false
            $normalizedPenalty = 10;

            // 🟢 BOOST PENALTY RULE: 2x damage to user if wrong (20 health reduction)
            if ($me->active_boost) {
                $normalizedPenalty *= 2;
                $me->update(['active_boost' => false]);
            }

            $me->decrement('hp', $normalizedPenalty);
            if ($me->hp <= 0) {
                $me->update(['hp' => 0, 'status' => 'defeated']);
            }
        }

        // 🟢 FREEZE RULE: Decrement power lock turns tracker upon answering a question
        if ($me->skills_locked_turns > 0) {
            $me->decrement('skills_locked_turns');
        }

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
            // 🟢 HEAL RULE: Recover back 40 health, lock submitting answer for 5 seconds
            $me->increment('hp', 40);
            if ($me->hp > 100) $me->update(['hp' => 100]); 
            
            $me->update(['strike_locked_until' => now()->addSeconds(5)]);
        } elseif ($type === 'freeze') {
            // 🟢 FREEZE RULE: Opponents frozen 10 seconds, User cannot use power for 3 questions
            RoomParticipant::where('room_id', $room->id)->where('user_id', '!=', Auth::id())
                ->where('status', 'active')
                ->update(['is_frozen' => true, 'frozen_until' => now()->addSeconds(10)]);

            $me->update(['skills_locked_turns' => 3]);
        } elseif ($type === 'shield') {
            // 🟢 SHIELD RULE: Give user active shield layer
            $me->update(['is_shielded' => true]);
        } elseif ($type === 'boost') {
            // 🟢 BOOST RULE: Activate damage multi-plier flag
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

        // 🟢 RANKING SYSTEM: 1 TO 4 PLAYERS MATCHMAKING POOL
        if ($playerCount >= 1 && $playerCount <= 4) {
            $isWinner = ($rank === 1);
            if ($difficulty === 'easy') {
                $change = ($isWinner) ? 15 : -5;
            } elseif ($difficulty === 'medium') {
                $change = ($isWinner) ? 30 : -15;
            } elseif ($difficulty === 'hard') {
                $change = ($isWinner) ? 70 : -50;
            }
        } 
        // 🟢 RANKING SYSTEM: 5 TO 20 PLAYERS MATCHMAKING POOL
        else if ($playerCount >= 5 && $playerCount <= 20) {
            if ($difficulty === 'easy') {
                if ($rank === 1) $change = 20;
                elseif ($rank === 2) $change = 15;
                elseif ($rank === 3) $change = 10;
                else $change = -5;
            } elseif ($difficulty === 'medium') {
                if ($rank === 1) $change = 45;
                elseif ($rank === 2) $change = 30;
                elseif ($rank === 3) $change = 20;
                else $change = -15;
            } elseif ($difficulty === 'hard') {
                if ($rank === 1) $change = 100;
                elseif ($rank === 2) $change = 70;
                elseif ($rank === 3) $change = 50;
                else $change = -50;
            }
        }

        return $change;
    }

    private function checkAnswer($qId, $submitted, $type) {
        $question = Question::find($qId);
        if ($type === 'text') return strtolower(trim($submitted ?? '')) === strtolower(trim($question->correct_answer_text));
        
        $correct = DB::table('options')->where('question_id', $qId)->where('is_correct', 1)->pluck('id')->toArray();
        $sub = is_array($submitted) ? $submitted : [$submitted];
        
        $correct = array_map('intval', $correct);
        $sub = array_map('intval', array_filter($sub));
        
        sort($correct); sort($sub);
        return (!empty($sub) && $correct === $sub);
    }
}