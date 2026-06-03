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
     * Shows only cards that are truly overdue in the database.
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

            $subject->due_cards = $dueCount;
            
            $key = $subject->subject_name;
            $subject->style = $styles[$key] ?? ['icon' => 'fa-layer-group', 'color' => 'primary'];
        }

        return view('users.flashcards.index', compact('subjects'));
    }

    /**
     * 2. STUDY ENGINE
     * Manages the full deck flip session before activating review timers.
     */
    public function study(Request $request, $subjectId)
    {
        $user = Auth::user();
        $allCardIds = Flashcard::where('subject_id', $subjectId)->pluck('id');
        
        $sessionKey = 'srs_session_active_' . $subjectId;
        $completedKey = 'srs_completed_cards_' . $subjectId;
        $isSessionActive = session()->get($sessionKey, false);

        // If no session is active, only pull due cards. If active, pull all to finish deck.
        if (!$isSessionActive) {
            $dueCardIds = SrsLog::where('user_id', $user->id)
                                ->whereIn('flashcard_id', $allCardIds)
                                ->where('next_review_date', '<=', now())
                                ->pluck('flashcard_id');
        } else {
            $dueCardIds = SrsLog::where('user_id', $user->id)
                                ->whereIn('flashcard_id', $allCardIds)
                                ->pluck('flashcard_id');
        }

        $newCardIds = Flashcard::where('subject_id', $subjectId)
                               ->whereNotIn('id', function($query) use ($user) {
                                   $query->select('flashcard_id')->from('srs_logs')->where('user_id', $user->id);
                               })
                               ->pluck('id');

        $studyPool = $dueCardIds->concat($newCardIds);
        $completedInSession = session()->get($completedKey, []);
        
        // Filter out cards already answered in this specific session run
        $finalPool = $studyPool->reject(fn($id) => in_array($id, $completedInSession))->unique()->values();

        if ($finalPool->isNotEmpty() && !$isSessionActive) {
            session()->put($sessionKey, true);
        }

        // Fix for infinite looping: Explicitly clear session and save before redirect
        if ($finalPool->isEmpty()) {
            session()->forget([$sessionKey, $completedKey]);
            session()->save(); 

            return redirect()->route('student.flashcards.index')
                             ->with('success', 'All cards reviewed! Check back after 1 minute.');
        }

        $card = Flashcard::findOrFail($finalPool->first());
        $remaining = $finalPool->count();
        return view('users.flashcards.study', compact('card', 'subjectId', 'remaining'));
    }

    /**
     * 3. UPDATE SRS LOG
     * Records feedback and sets the 1-minute timer for "Again" ratings.
     */
    public function updateLog(Request $request)
    {
        $user = Auth::user();
        $log = SrsLog::firstOrNew([
            'user_id' => $user->id, 
            'flashcard_id' => $request->card_id
        ]);

        if (!$log->exists) {
            $log->fill([
                'ease_factor' => 2.5, 
                'repetition_count' => 0, 
                'interval' => 0,
                'box_number' => 1
            ]);
        }

        $rating = (int)$request->rating;
        if ($rating == 1) { // AGAIN
            $log->repetition_count = 0;
            $log->interval = 0;
            $log->ease_factor = max(1.3, $log->ease_factor - 0.20);
            $log->next_review_date = now()->addMinute(); // Timer starts now
        } else {
            // Standard SM-2 Spaced Repetition Math
            $log->ease_factor = max(1.3, $log->ease_factor + (0.1 - (5 - $rating) * (0.08 + (5 - $rating) * 0.02)));
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
        
        // Track progress within the current session
        session()->push('srs_completed_cards_' . $request->subject_id, $request->card_id);
        
        return redirect()->route('student.flashcards.study', $request->subject_id);
    }

    /**
     * 4. MANUAL MODE
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