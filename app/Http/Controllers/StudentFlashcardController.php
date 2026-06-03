<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Flashcard;
use App\Models\Subject;
use App\Models\SrsLog;
use Illuminate\Support\Facades\Auth;

class StudentFlashcardController extends Controller
{
    // 1. DASHBOARD (Deck List)
    public function index()
    {
        $user = Auth::user();
        $subjects = Subject::all();

        // Icon & Color Mapping for UI
        $styles = [
            'Al-Quran' => ['icon' => 'fa-quran', 'color' => 'primary'], // Blue
            'Hadith'   => ['icon' => 'fa-book-open', 'color' => 'success'], // Green
            'Akidah'   => ['icon' => 'fa-star-and-crescent', 'color' => 'info'], // Light Blue
            'Fiqh'     => ['icon' => 'fa-scale-balanced', 'color' => 'warning'], // Yellow
            'Sirah'    => ['icon' => 'fa-landmark', 'color' => 'danger'], // Red
            'Akhlak'   => ['icon' => 'fa-heart', 'color' => 'secondary'], // Grey
        ];

        foreach($subjects as $subject) {
            // Get Cards
            $allCardIds = Flashcard::where('subject_id', $subject->id)->pluck('id');
            
            // 1. Due Cards (Review Date <= Today)
            $dueCount = SrsLog::where('user_id', $user->id)
                              ->whereIn('flashcard_id', $allCardIds)
                              ->whereDate('next_review_date', '<=', now())
                              ->count();

            // 2. New Cards (Never studied)
            $studiedIds = SrsLog::where('user_id', $user->id)->pluck('flashcard_id');
            $newCount = Flashcard::where('subject_id', $subject->id)
                                 ->whereNotIn('id', $studiedIds)
                                 ->count();

            $subject->due_cards = $dueCount + $newCount;
            
            // Assign Style
            $key = $subject->subject_name; // Ensure DB name matches keys or fallback
            $subject->style = $styles[$key] ?? ['icon' => 'fa-layer-group', 'color' => 'primary'];
        }

        return view('users.flashcards.index', compact('subjects'));
    }

    // 2. STUDY MODE (SRS Engine)
    public function study($subjectId)
    {
        $user = Auth::user();

        // A. Get Due Cards (Log exists & date passed)
        $dueCardIds = SrsLog::where('user_id', $user->id)
                            ->whereDate('next_review_date', '<=', now())
                            ->pluck('flashcard_id');

        // B. Get New Cards (No Log)
        $studiedIds = SrsLog::where('user_id', $user->id)->pluck('flashcard_id');
        $newCardIds = Flashcard::where('subject_id', $subjectId)
                               ->whereNotIn('id', $studiedIds)
                               ->pluck('id');

        // Merge pools
        $studyPool = $dueCardIds->merge($newCardIds);

        if ($studyPool->isEmpty()) {
            return redirect()->route('student.flashcards.index')->with('success', 'Great job! No cards due for this subject.');
        }

        // Get the first card from the pool
        $card = Flashcard::find($studyPool->first());
        $remaining = $studyPool->count();

        return view('users.flashcards.study', compact('card', 'subjectId', 'remaining'));
    }

    // 3. UPDATE SRS LOGIC (SM-2 Algorithm Fixed)
    public function updateLog(Request $request)
    {
        $user = Auth::user();
        $cardId = $request->card_id;
        $rating = $request->rating; // 1=Again, 2=Hard, 3=Good, 4=Easy

        $log = SrsLog::firstOrNew([
            'user_id' => $user->id,
            'flashcard_id' => $cardId
        ]);

        // Set Defaults for New Cards
        if (!$log->exists) {
            $log->ease_factor = 2.5; // Standard starting ease
            $log->repetition_count = 0;
            $log->interval = 0;
        }

        // --- ALGORITHM ---
        if ($rating == 1) { // Forgot
            $log->repetition_count = 0;
            $log->interval = 1; // Show again tomorrow
        } else {
            // Update Ease Factor (Standard Formula)
            // rating 1-4 mapped to quality 3-5 roughly
            $log->ease_factor = $log->ease_factor + (0.1 - (5 - $rating) * (0.08 + (5 - $rating) * 0.02));
            if ($log->ease_factor < 1.3) $log->ease_factor = 1.3; // Min cap

            $log->repetition_count++;

            // Calculate Interval
            if ($log->repetition_count == 1) {
                $log->interval = 1;
            } elseif ($log->repetition_count == 2) {
                $log->interval = 6;
            } else {
                $log->interval = round($log->interval * $log->ease_factor);
            }
        }

        // Bonus: Easy button boosts interval slightly more
        if($rating == 4) $log->interval += 1;

        $log->next_review_date = now()->addDays($log->interval);
        $log->save();

        return redirect()->route('student.flashcards.study', $request->subject_id);
    }

    // 4. MANUAL BROWSING (No SRS)
    public function manual($subjectId)
    {
        $subject = Subject::findOrFail($subjectId);
        $cards = Flashcard::where('subject_id', $subjectId)->simplePaginate(1);
        
        // Progress bar calculation
        $total = Flashcard::where('subject_id', $subjectId)->count();
        $current = $cards->currentPage();
        $progress = $total > 0 ? ($current / $total) * 100 : 0;

        return view('users.flashcards.manual', compact('cards', 'subject', 'progress', 'current', 'total'));
    }
}