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
     * 1. DASHBOARD (Deck List Counters)
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
            
            // 🟢 FIXED: Removed whereDate to enable precise minute/hour timestamp matching
            $dueCount = SrsLog::where('user_id', $user->id)
                              ->whereIn('flashcard_id', $allCardIds)
                              ->where('next_review_date', '<=', now())
                              ->count();

            // Unstudied cards
            $newCount = Flashcard::where('subject_id', $subject->id)
                                 ->whereNotIn('id', function($query) use ($user) {
                                     $query->select('flashcard_id')->from('srs_logs')->where('user_id', $user->id);
                                 })
                                 ->count();

            // Total active items for the student's review session
            $subject->due_cards = $dueCount + $newCount;
            
            $key = $subject->subject_name;
            $subject->style = $styles[$key] ?? ['icon' => 'fa-layer-group', 'color' => 'primary'];
        }

        return view('users.flashcards.index', compact('subjects'));
    }

    /**
     * 2. STUDY MODE (Session Queue Execution Loop)
     */
    public function study(Request $request, $subjectId)
    {
        $user = Auth::user();
        $allCardIds = Flashcard::where('subject_id', $subjectId)->pluck('id');
        
        $sessionKey = 'srs_session_active_' . $subjectId;
        $completedKey = 'srs_completed_cards_' . $subjectId;
        $isSessionActive = session()->get($sessionKey, false);

        // Fetch target pool based on session active states
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
        
        // Exclude cards already answered during the active session run
        $finalPool = $studyPool->reject(fn($id) => in_array($id, $completedInSession))->unique()->values();

        if ($finalPool->isNotEmpty() && !$isSessionActive) {
            session()->put($sessionKey, true);
        }

        // 🔴 END OF DECK: Wipe session trackers and return to dashboard
        if ($finalPool->isEmpty()) {
            session()->forget([$sessionKey, $completedKey]);
            session()->save(); 

            return redirect()->route('student.flashcards.index')
                             ->with('success', 'Deck completed! Spaced review intervals activated.');
        }

        $card = Flashcard::findOrFail($finalPool->first());
        $remaining = $finalPool->count();
        
        return view('users.flashcards.study', compact('card', 'subjectId', 'remaining'));
    }

    /**
     * 3. UPDATE LOG ENGINE (Exact Time-Interleaved Calculation Mapping)
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

        // 🟢 FIXED: Strict interval matching (1 min, 2 days, 4 days, 7 days)
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

        // Push card ID to tracking array so it isn't shown again during this session
        session()->push('srs_completed_cards_' . $subjectId, $cardId);

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