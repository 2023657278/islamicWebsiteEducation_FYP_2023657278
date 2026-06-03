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
     * 1. DASHBOARD INDEX
     * Strictly tracks and displays cards that are overdue based on their database timestamp.
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
            
            // Strictly count cards where the next review time has passed
            $dueCount = SrsLog::where('user_id', $user->id)
                              ->whereIn('flashcard_id', $allCardIds)
                              ->where('next_review_date', '<=', now())
                              ->count();

            // Set dashboard badge to ONLY reflect true due cards
            $subject->due_cards = $dueCount;
            
            $key = $subject->subject_name;
            $subject->style = $styles[$key] ?? ['icon' => 'fa-layer-group', 'color' => 'primary'];
        }

        return view('users.flashcards.index', compact('subjects'));
    }

    /**
     * 2. STUDY ENGINE
     * Strictly forces full deck flip session for due cards only, then boots to dashboard.
     */
    public function study(Request $request, $subjectId)
    {
        $user = Auth::user();
        $allCardIds = Flashcard::where('subject_id', $subjectId)->pluck('id');
        
        $sessionKey = 'srs_session_active_' . $subjectId;
        $completedKey = 'srs_completed_cards_' . $subjectId;
        $isSessionActive = session()->get($sessionKey, false);

        // Strictly pull cards that are genuinely due or part of the active session run.
        if (!$isSessionActive) {
            $studyPool = SrsLog::where('user_id', $user->id)
                                ->whereIn('flashcard_id', $allCardIds)
                                ->where('next_review_date', '<=', now())
                                ->pluck('flashcard_id');
        } else {
            $studyPool = SrsLog::where('user_id', $user->id)
                                ->whereIn('flashcard_id', $allCardIds)
                                ->pluck('flashcard_id');
        }

        $completedInSession = session()->get($completedKey, []);
        
        // Filter out cards already answered in this specific session run
        $finalPool = $studyPool->reject(fn($id) => in_array($id, $completedInSession))->unique()->values();

        if ($finalPool->isNotEmpty() && !$isSessionActive) {
            session()->put($sessionKey, true);
        }

        // 🔴 END OF DECK: Reset session variables completely and return to dashboard
        if ($finalPool->isEmpty()) {
            session()->forget([$sessionKey, $completedKey]);
            session()->save(); 

            return redirect()->route('student.flashcards.index')
                             ->with('success', 'All due cards reviewed! Your individual timers have started.');
        }

        $card = Flashcard::findOrFail($finalPool->first());
        $remaining = $finalPool->count();
        return view('users.flashcards.study', compact('card', 'subjectId', 'remaining'));
    }

    /**
     * 3. UPDATE SRS LOG
     * Records individual grade timings precisely (1 min, 2 days, 4 days, 7 days).
     */
    public function updateLog(Request $request)
    {
        $user = Auth::user();
        $cardId = $request->card_id;
        $rating = (int)$request->rating; // 1=Again, 2=Hard, 3=Good, 4=Easy
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

        // Strict Interval Matching Rules
        if ($rating == 1) { // AGAIN
            $log->repetition_count = 0;
            $log->interval = 0;
            $log->next_review_date = now()->addMinute(); // Adds exactly 60 seconds
        } elseif ($rating == 2) { // HARD
            $log->interval = 2;
            $log->next_review_date = now()->addDays(2);
        } elseif ($rating == 3) { // GOOD
            $log->interval = 4;
            $log->next_review_date = now()->addDays(4);
        } elseif ($rating == 4) { // EASY
            $log->interval = 7;
            $log->next_review_date = now()->addDays(7);
        }

        $log->save();
        
        // Push to active session completion log
        session()->push('srs_completed_cards_' . $subjectId, $cardId);
        
        return redirect()->route('student.flashcards.study', $subjectId);
    }

    /**
     * 4. MANUAL PREVIEW MODE (Browse Deck)
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