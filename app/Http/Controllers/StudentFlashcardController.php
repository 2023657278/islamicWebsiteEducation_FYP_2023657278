<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Flashcard;
use App\Models\Subject;
use App\Models\SrsLog;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StudentFlashcardController extends Controller
{
    /**
     * 1. DASHBOARD: Shows deck collections with accurate counters.
     */
    public function index()
    {
        $user = Auth::user();
        $subjects = Subject::all();

        $styles = [
            'Al-Quran' => ['icon' => 'fa-quran', 'color' => 'primary'],
            'Hadith'   => ['icon' => 'fa-book-open', 'color' => 'success'],
            'Akidah'   => ['icon' => 'fa-star-and-crescent', 'color' => 'info'],
            'Fiqh'     => ['icon' => 'fa-scale-balanced', 'color' => 'warning'],
            'Sirah'    => ['icon' => 'fa-landmark', 'color' => 'danger'],
            'Akhlak'   => ['icon' => 'fa-heart', 'color' => 'secondary'],
        ];

        foreach($subjects as $subject) {
            $allCardIds = Flashcard::where('subject_id', $subject->id)->pluck('id');
            
            // 🟢 FIXED: Match the exact query parameters used in the study module
            $dueCount = SrsLog::where('user_id', $user->id)
                              ->whereIn('flashcard_id', $allCardIds)
                              ->where(function($query) {
                                  $query->where('next_review_date', '<=', now())
                                        ->orWhere('interval', 0); // Always count active "Again" cards
                              })
                              ->count();

            $newCount = Flashcard::where('subject_id', $subject->id)
                                 ->whereNotIn('id', function($query) use ($user) {
                                     $query->select('flashcard_id')->from('srs_logs')->where('user_id', $user->id);
                                 })
                                 ->count();

            $subject->due_cards = $dueCount + $newCount;
            
            $key = $subject->subject_name;
            $subject->style = $styles[$key] ?? ['icon' => 'fa-layer-group', 'color' => 'primary'];
        }

        return view('users.flashcards.index', compact('subjects'));
    }

    /**
     * 2. STUDY ENGINE: Manages active cards for the session.
     */
    // 2. STUDY ENGINE (With Real-Time Minute Delay)
    public function study(Request $request, $subjectId)
    {
        $user = Auth::user();
        $allCardIds = Flashcard::where('subject_id', $subjectId)->pluck('id');

        // Check if there's an active "Again" card waiting in session memory
        $againCardId = session()->get('srs_again_card_' . $subjectId);

        if ($againCardId) {
            $log = SrsLog::where('user_id', $user->id)
                         ->where('flashcard_id', $againCardId)
                         ->first();

            // If 1 minute has passed, bring it back! Otherwise, keep it hidden for now
            if ($log && $log->next_review_date->isPast()) {
                // The minute is up! Clear the session block so it behaves normally
                session()->forget('srs_again_card_' . $subjectId);
            }
        }

        // Pull database cards that are genuinely due right now (next_review_date <= current time)
        $dueCardIds = SrsLog::where('user_id', $user->id)
                            ->whereIn('flashcard_id', $allCardIds)
                            ->where('next_review_date', '<=', now())
                            ->pluck('flashcard_id');

        // Pull completely unstudied cards
        $newCardIds = Flashcard::where('subject_id', $subjectId)
                               ->whereNotIn('id', function($query) use ($user) {
                                   $query->select('flashcard_id')->from('srs_logs')->where('user_id', $user->id);
                               })
                               ->pluck('id');

        // Combine overdue and new cards
        $studyPool = $dueCardIds->concat($newCardIds);

        // If we are waiting on an "Again" card but other cards are available, skip the "Again" card for now
        if (session()->has('srs_again_card_' . $subjectId) && $studyPool->isNotEmpty()) {
            // Remove the waiting card from this specific loop so the student sees other cards first
            $studyPool = $studyPool->reject(function ($id) use ($againCardId) {
                return $id == $againCardId;
            });
        }

        // If the pool is empty but we have an "Again" card waiting, we must check if its minute is up
        if ($studyPool->isEmpty() && $againCardId) {
            $log = SrsLog::where('user_id', $user->id)->where('flashcard_id', $againCardId)->first();
            if ($log && $log->next_review_date->isFuture()) {
                // The minute isn't up yet, and there are no other cards left to do!
                $secondsLeft = now()->diffInSeconds($log->next_review_date);
                return redirect()->route('student.flashcards.index')
                                 ->with('info', "Card recycling active. Reappears in {$secondsLeft} seconds!");
            }
        }

        $finalQueue = $studyPool->unique()->values();

        if ($finalQueue->isEmpty()) {
            session()->forget('srs_again_card_' . $subjectId);
            return redirect()->route('student.flashcards.index')->with('success', 'All caught up!');
        }

        $card = Flashcard::findOrFail($finalQueue->first());
        $remaining = $finalQueue->count();

        return view('users.flashcards.study', compact('card', 'subjectId', 'remaining'));
    }

    // 3. UPDATE SRS ENGINE
    public function updateLog(Request $request)
    {
        $user = Auth::user();
        $cardId = $request->card_id;
        $rating = (int)$request->rating; 
        $subjectId = $request->subject_id;

        $log = SrsLog::firstOrNew([
            'user_id' => $user->id,
            'flashcard_id' => $cardId
        ]);

        if (!$log->exists) {
            $log->ease_factor = 2.5; 
            $log->repetition_count = 0;
            $log->interval = 0;
            $log->box_number = 1;
        }

        if ($rating == 1) { 
            // Save the card ID to session storage so our study method knows we are tracking a delay
            session()->put('srs_again_card_' . $subjectId, $cardId);

            $log->repetition_count = 0;
            $log->interval = 0; 
            $log->ease_factor = max(1.3, $log->ease_factor - 0.20); 
            
            // 🟢 THE REAL 1-MINUTE TIME DELAY: Set review date to exactly 1 minute from right now
            $log->next_review_date = now()->addMinute(); 
        } else {
            if (session()->get('srs_again_card_' . $subjectId) == $cardId) {
                session()->forget('srs_again_card_' . $subjectId);
            }

            $log->ease_factor = $log->ease_factor + (0.1 - (5 - $rating) * (0.08 + (5 - $rating) * 0.02));
            if ($log->ease_factor < 1.3) $log->ease_factor = 1.3;

            $log->repetition_count++;

            if ($log->repetition_count == 1) {
                $log->interval = ($rating == 4) ? 4 : 1; 
            } elseif ($log->repetition_count == 2) {
                $log->interval = 6;
            } else {
                $log->interval = round($log->interval * $log->ease_factor);
            }

            $log->next_review_date = now()->addDays($log->interval);
        }

        $log->save();

        return redirect()->route('student.flashcards.study', $subjectId);
    }

    /**
     * 4. MANUAL PREVIEW MODE
     */
    public function manual($subjectId)
    {
        $subject = Subject::findOrFail($subjectId);
        $cards = Flashcard::where('subject_id', $subjectId)->simplePaginate(1);
        
        $total = Flashcard::where('subject_id', $subjectId)->count();
        $current = $cards->currentPage();
        $progress = $total > 0 ? ($current / $total) * 100 : 0;

        return view('users.flashcards.manual', compact('cards', 'subject', 'progress', 'current', 'total'));
    }
}