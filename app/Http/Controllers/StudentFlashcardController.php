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
    // 1. DASHBOARD INDEX
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
            
            $dueCount = SrsLog::where('user_id', $user->id)
                              ->whereIn('flashcard_id', $allCardIds)
                              ->where('next_review_date', '<=', now())
                              ->count();

            // 🟢 Include the session check directly into the dashboard button render
            $sessionCardId = session()->get('srs_again_card_' . $subject->id);
            if ($sessionCardId && $allCardIds->contains($sessionCardId)) {
                $dueCount = max(1, $dueCount);
            }

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

    // 2. STUDY ENGINE
   public function study(Request $request, $subjectId)
    {
        $user = Auth::user();
        $allCardIds = Flashcard::where('subject_id', $subjectId)->pluck('id');

        // Check if there's an active "Again" card hanging in the session memory
        $againCardId = session()->get('srs_again_card_' . $subjectId);

        // Pull true overdue cards from the database
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

        // Combine them into a single collection pool
        $studyPool = $dueCardIds->concat($newCardIds);

        // If an "Again" card exists, push it back to the absolute front of the pile!
        if ($againCardId && $allCardIds->contains($againCardId)) {
            $studyPool = collect([$againCardId])->concat($studyPool)->unique();
        }

        if ($studyPool->isEmpty()) {
            // Clean up session memory once finished
            session()->forget('srs_again_card_' . $subjectId);
            return redirect()->route('student.flashcards.index')->with('success', 'All caught up!');
        }

        $card = Flashcard::findOrFail($studyPool->first());
        $remaining = $studyPool->count();

        return view('users.flashcards.study', compact('card', 'subjectId', 'remaining'));
    }

    // 3. FIXED UPDATE SRS ENGINE
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
        }

        if ($rating == 1) { 
            // 🔴 THE BULLETPROOF FIX: Store this card ID directly in the user session flash storage
            session()->put('srs_again_card_' . $subjectId, $cardId);

            $log->repetition_count = 0;
            $log->interval = 0; 
            $log->ease_factor = max(1.3, $log->ease_factor - 0.20); 
            $log->next_review_date = now(); 
        } else {
            // Clear this specific card from the active "Again" session queue if they pass it
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
     * 4. MANUAL MODE: Simple browser for previewing all cards.
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