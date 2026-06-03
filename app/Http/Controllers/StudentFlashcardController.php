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
     * 1. DASHBOARD: Display Decks with accurate Due/New counters.
     */
    public function index()
    {
        $user = Auth::user();
        $subjects = Subject::all();

        // UI Style Mapping
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
            
            // Count cards that have a log and are ready for review
            $dueCount = SrsLog::where('user_id', $user->id)
                              ->whereIn('flashcard_id', $allCardIds)
                              ->where('next_review_date', '<=', now())
                              ->count();

            // Count cards that have never been studied by this user
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
     * 2. STUDY MODE: Pulls the next card in the SRS sequence.
     */
    public function study($subjectId)
    {
        $user = Auth::user();
        $allCardIds = Flashcard::where('subject_id', $subjectId)->pluck('id');

        // Pool A: Overdue cards (High Priority)
        $dueCardIds = SrsLog::where('user_id', $user->id)
                            ->whereIn('flashcard_id', $allCardIds)
                            ->where('next_review_date', '<=', now())
                            ->pluck('flashcard_id');

        // Pool B: Unstudied cards (Medium Priority)
        $newCardIds = Flashcard::where('subject_id', $subjectId)
                               ->whereNotIn('id', function($query) use ($user) {
                                   $query->select('flashcard_id')->from('srs_logs')->where('user_id', $user->id);
                               })
                               ->pluck('id');

        // Combine pools: Due cards first, then New cards
        $studyPool = $dueCardIds->merge($newCardIds);

        if ($studyPool->isEmpty()) {
            return redirect()->route('student.flashcards.index')
                             ->with('success', 'Alhamdulillah! No more cards due for today.');
        }

        $card = Flashcard::findOrFail($studyPool->first());
        $remaining = $studyPool->count();

        return view('users.flashcards.study', compact('card', 'subjectId', 'remaining'));
    }

    /**
     * 3. UPDATE LOG: The SM-2 SRS Algorithm Engine.
     */
    public function updateLog(Request $request)
    {
        $user = Auth::user();
        $cardId = $request->card_id;
        $rating = (int) $request->rating; // 1=Again, 2=Hard, 3=Good, 4=Easy

        $log = SrsLog::firstOrNew([
            'user_id' => $user->id,
            'flashcard_id' => $cardId
        ]);

        // Default SM-2 starting values for new cards
        if (!$log->exists) {
            $log->ease_factor = 2.5; 
            $log->repetition_count = 0;
            $log->interval = 0;
        }

        // Logic for "Again" (Failed to remember)
        if ($rating == 1) { 
            $log->repetition_count = 0;
            $log->interval = 0; // Recycles immediately back into the session
            $log->ease_factor = max(1.3, $log->ease_factor - 0.20); 
            $log->next_review_date = now(); 
        } else {
            // Update Ease Factor (SM-2 Formula)
            $log->ease_factor = $log->ease_factor + (0.1 - (5 - $rating) * (0.08 + (5 - $rating) * 0.02));
            if ($log->ease_factor < 1.3) $log->ease_factor = 1.3;

            $log->repetition_count++;

            // Set new interval based on repetition history
            if ($log->repetition_count == 1) {
                $log->interval = ($rating == 4) ? 4 : 1; // Easy gets a 4-day jump
            } elseif ($log->repetition_count == 2) {
                $log->interval = 6;
            } else {
                $log->interval = round($log->interval * $log->ease_factor);
            }

            $log->next_review_date = now()->addDays($log->interval);
        }

        $log->save();

        return redirect()->route('student.flashcards.study', $request->subject_id);
    }

    /**
     * 4. MANUAL MODE: Simple browser for previewing cards.
     */
    public function manual($subjectId)
    {
        $subject = Subject::findOrFail($subjectId);
        
        // Fix: Use simplePaginate to return a Paginator object instead of an array
        $cards = Flashcard::where('subject_id', $subjectId)->simplePaginate(1);
        
        $total = Flashcard::where('subject_id', $subjectId)->count();
        $current = $cards->currentPage();
        $progress = $total > 0 ? ($current / $total) * 100 : 0;

        return view('users.flashcards.manual', compact('cards', 'subject', 'progress', 'current', 'total'));
    }
}