<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Flashcard;
use App\Models\Subject;
use App\Models\SrsLog;
use Illuminate\Support\Facades\Auth;

class StudentFlashcardController extends Controller
{
    // 1. DASHBOARD: Shows deck collections with accurate counters
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
            
            // Fix: Check due logs specifically belonging to THIS subject's cards
            $dueCount = SrsLog::where('user_id', $user->id)
                              ->whereIn('flashcard_id', $allCardIds)
                              ->where('next_review_date', '<=', now())
                              ->count();

            // Fix: Track unstudied new cards safely isolated by current subject scope
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

    // 2. STUDY MODE: Prioritizes and manages active cards
    public function study($subjectId)
    {
        $user = Auth::user();
        $allCardIds = Flashcard::where('subject_id', $subjectId)->pluck('id');

        // Pool A: Scheduled cards that are currently due
        $dueCardIds = SrsLog::where('user_id', $user->id)
                            ->whereIn('flashcard_id', $allCardIds)
                            ->where('next_review_date', '<=', now())
                            ->pluck('flashcard_id');

        // Pool B: Brand new unstudied cards
        $newCardIds = Flashcard::where('subject_id', $subjectId)
                               ->whereNotIn('id', function($query) use ($user) {
                                   $query->select('flashcard_id')->from('srs_logs')->where('user_id', $user->id);
                               })
                                ->pluck('id');

        // Merge pools (Due cards are placed at the front so students review them first)
        $studyPool = $dueCardIds->merge($newCardIds);

        if ($studyPool->isEmpty()) {
            return redirect()->route('student.flashcards.index')->with('success', 'All caught up for this subject!');
        }

        // Fetch the active card object instance
        $card = Flashcard::find($studyPool->first());
        $remaining = $studyPool->count();

        return view('users.flashcards.study', compact('card', 'subjectId', 'remaining'));
    }

    // 3. SRS ALGORITHM ENGINE: Updates intervals based on student performance
    public function updateLog(Request $request)
    {
        $user = Auth::user();
        $cardId = $request->card_id;
        $rating = $request->rating; 

        $log = SrsLog::firstOrNew([
            'user_id' => $user->id,
            'flashcard_id' => $cardId
        ]);

        if (!$log->exists) {
            $log->ease_factor = 2.5; 
            $log->repetition_count = 0;
            $log->interval = 0;
        }

        // --- SM-2 ALGORITHM MODIFICATIONS ---
        if ($rating == 1) { 
            // "Again" state: Set interval to 0 so it reappears immediately in the next refresh cycle
            $log->repetition_count = 0;
            $log->interval = 0; 
            $log->ease_factor = max(1.3, $log->ease_factor - 0.2); // Make the card appear more frequently later
            $log->next_review_date = now(); // Keeps it due right now
        } else {
            // Normal scheduling for Hard, Good, and Easy responses
            $log->ease_factor = $log->ease_factor + (0.1 - (5 - $rating) * (0.08 + (5 - $rating) * 0.02));
            if ($log->ease_factor < 1.3) $log->ease_factor = 1.3;

            $log->repetition_count++;

            if ($log->repetition_count == 1) {
                $log->interval = ($rating == 2) ? 1 : 2; // Hard = 1 day, Good/Easy = 2 days
            } elseif ($log->repetition_count == 2) {
                $log->interval = ($rating == 2) ? 3 : 5;
            } else {
                $log->interval = round($log->interval * $log->ease_factor);
            }

            // Apply a slight interval boost if the user selects "Easy"
            if ($rating == 4) {
                $log->interval += 2;
            }

            $log->next_review_date = now()->addDays($log->interval);
        }

        $log->save();

        return redirect()->route('student.flashcards.study', $request->subject_id);
    }

    // 4. MANUAL BROWSING MODE
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