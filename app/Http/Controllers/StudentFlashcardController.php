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
            
            // 🟢 FIX: Clamping conditions inside a localized subquery avoids logic leakage
            $dueCount = SrsLog::where('user_id', $user->id)
                              ->whereIn('flashcard_id', $allCardIds)
                              ->where(function($query) {
                                  $query->where('next_review_date', '<=', now())
                                        ->orWhere('interval', 0);
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

    // 2. STUDY ENGINE
    public function study($subjectId)
    {
        $user = Auth::user();
        $allCardIds = Flashcard::where('subject_id', $subjectId)->pluck('id');

        // 🟢 FIX: Match the exact same clamped closure constraint here
        $dueCardIds = SrsLog::where('user_id', $user->id)
                            ->whereIn('flashcard_id', $allCardIds)
                            ->where(function($query) {
                                $query->where('next_review_date', '<=', now())
                                      ->orWhere('interval', 0);
                            })
                            ->pluck('flashcard_id');

        $newCardIds = Flashcard::where('subject_id', $subjectId)
                               ->whereNotIn('id', function($query) use ($user) {
                                   $query->select('flashcard_id')->from('srs_logs')->where('user_id', $user->id);
                               })
                               ->pluck('id');

        $studyPool = $dueCardIds->concat($newCardIds);

        if ($studyPool->isEmpty()) {
            return redirect()->route('student.flashcards.index')->with('success', 'All caught up!');
        }

        $card = Flashcard::findOrFail($studyPool->first());
        $remaining = $studyPool->count();

        return view('users.flashcards.study', compact('card', 'subjectId', 'remaining'));
    }

    /**
     * 3. SRS UPDATE LOGIC: Updates progress using the SM-2 algorithm.
     */
    public function updateLog(Request $request)
    {
        $user = Auth::user();
        $cardId = $request->card_id;
        $rating = (int)$request->rating; // 1=Again, 2=Hard, 3=Good, 4=Easy

        $log = SrsLog::firstOrNew([
            'user_id' => $user->id,
            'flashcard_id' => $cardId
        ]);

        if (!$log->exists) {
            $log->ease_factor = 2.5; 
            $log->repetition_count = 0;
            $log->interval = 0;
        }

        // --- THE "AGAIN" FIX ---
        if ($rating == 1) { 
            $log->repetition_count = 0;
            $log->interval = 0; // Keeping it at 0 ensures it stays in the current session loop
            $log->ease_factor = max(1.3, $log->ease_factor - 0.20); 
            // Setting the date to 1 minute ago guarantees it appears as 'due' immediately
            $log->next_review_date = now()->subMinutes(1); 
        } else {
            // Update Ease Factor
            $log->ease_factor = $log->ease_factor + (0.1 - (5 - $rating) * (0.08 + (5 - $rating) * 0.02));
            if ($log->ease_factor < 1.3) $log->ease_factor = 1.3;

            $log->repetition_count++;

            // Calculate next interval
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

        return redirect()->route('student.flashcards.study', $request->subject_id);
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