<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Flashcard;
use App\Models\Subject;
use App\Models\SrsLog;
use Illuminate\Support\Facades\Auth;

class StudentFlashcardController extends Controller
{
    /**
     * 1. DASHBOARD INDEX: Evaluates live timestamps strictly.
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
            
            // Strictly pull cards where review time has already passed
            $dueCount = SrsLog::where('user_id', $user->id)
                              ->whereIn('flashcard_id', $allCardIds)
                              ->where('next_review_date', '<=', now())
                              ->count();

            // Pull unstudied cards
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
     * 2. STUDY MODULE
     */
    // 2. STUDY ENGINE (Session-Aware Deck Completion Loop)
    public function study(Request $request, $subjectId)
    {
        $user = Auth::user();
        $allCardIds = Flashcard::where('subject_id', $subjectId)->pluck('id');

        // Check if this is an active session loop
        $isSessionActive = session()->get('srs_session_active_' . $subjectId, false);

        if (!$isSessionActive) {
            // 🟢 FRESH SESSION: Only pull cards that are genuinely due right now or brand new
            $dueCardIds = SrsLog::where('user_id', $user->id)
                                ->whereIn('flashcard_id', $allCardIds)
                                ->where('next_review_date', '<=', now())
                                ->pluck('flashcard_id');
        } else {
            // 🟢 ACTIVE SESSION: Keep ALL cards in the loop until the student finishes flipping the deck
            $dueCardIds = SrsLog::where('user_id', $user->id)
                                ->whereIn('flashcard_id', $allCardIds)
                                ->pluck('flashcard_id');
        }

        // Pull completely unstudied cards
        $newCardIds = Flashcard::where('subject_id', $subjectId)
                               ->whereNotIn('id', function($query) use ($user) {
                                   $query->select('flashcard_id')->from('srs_logs')->where('user_id', $user->id);
                               })
                               ->pluck('id');

        // Combine overdue and new cards into our session queue
        $studyPool = $dueCardIds->concat($newCardIds);

        // Filter out any cards the user has ALREADY rated during this exact session run
        $completedInSession = session()->get('srs_completed_cards_' . $subjectId, []);
        $studyPool = $studyPool->reject(function ($cardId) use ($completedInSession) {
            return in_array($cardId, $completedInSession);
        })->unique()->values();

        // If the pool has cards, make sure the session is marked as active
        if ($studyPool->isNotEmpty() && !$isSessionActive) {
            session()->put('srs_session_active_' . $subjectId, true);
        }

        // 🔴 END OF DECK REACHED: Reset session variables and kick them to the dashboard
        if ($studyPool->isEmpty()) {
            session()->forget('srs_session_active_' . $subjectId);
            session()->forget('srs_completed_cards_' . $subjectId);
            
            return redirect()->route('student.flashcards.index')
                             ->with('success', 'Deck completed! Your spaced review timers have started.');
        }

        $card = Flashcard::findOrFail($studyPool->first());
        $remaining = $studyPool->count();

        return view('users.flashcards.study', compact('card', 'subjectId', 'remaining'));
    }

    // 3. UPDATE LOG ENGINE
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

        // Calculate Next Review Date based on choice
        if ($rating == 1) { // AGAIN (1 Minute Delay)
            $log->repetition_count = 0;
            $log->interval = 0; 
            $log->ease_factor = max(1.3, $log->ease_factor - 0.20); 
            $log->next_review_date = now()->addMinute(); // Timer will start ticking immediately
        } else {
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

        // 🟢 Push this card ID into the completed array for this session so it isn't shown again right now
        session()->push('srs_completed_cards_' . $subjectId, $cardId);

        return redirect()->route('student.flashcards.study', $subjectId);
    }
}