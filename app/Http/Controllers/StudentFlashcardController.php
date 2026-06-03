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
     * 1. DASHBOARD INDEX
     * Strictly shows ONLY cards that are truly due or brand new. 
     * Cards on active timers will NOT be counted.
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

        // Ensure we match the exact current timestamp across server layers
        $currentTime = Carbon::now();

        foreach($subjects as $subject) {
            $allCardIds = Flashcard::where('subject_id', $subject->id)->pluck('id');
            
            // A. Count cards that are genuinely overdue right now down to the second
            $dueCount = SrsLog::where('user_id', $user->id)
                              ->whereIn('flashcard_id', $allCardIds)
                              ->where('next_review_date', '<=', $currentTime)
                              ->count();

            // B. Count brand new cards (no logs exist at all yet)
            $newCount = Flashcard::where('subject_id', $subject->id)
                                 ->whereNotIn('id', function($query) use ($user) {
                                     $query->select('flashcard_id')->from('srs_logs')->where('user_id', $user->id);
                                 })
                                 ->count();

            // Total due cards = Overdue Cards + Brand New Cards
            $subject->due_cards = $dueCount + $newCount;
            
            $key = $subject->subject_name;
            $subject->style = $styles[$key] ?? ['icon' => 'fa-layer-group', 'color' => 'primary'];
        }

        return view('users.flashcards.index', compact('subjects'));
    }

    /**
     * 2. STUDY ENGINE
     * Locks cards into the session pool until the user completes the deck.
     */
    public function study(Request $request, $subjectId)
    {
        $user = Auth::user();
        $allCardIds = Flashcard::where('subject_id', $subjectId)->pluck('id');
        
        $sessionKey = 'srs_session_active_' . $subjectId;
        $completedKey = 'srs_completed_cards_' . $subjectId;
        $isSessionActive = session()->get($sessionKey, false);
        $currentTime = Carbon::now();

        // Separate initial check from active session execution flow
        if (!$isSessionActive) {
            $dueCardIds = SrsLog::where('user_id', $user->id)
                                ->whereIn('flashcard_id', $allCardIds)
                                ->where('next_review_date', '<=', $currentTime)
                                ->pluck('flashcard_id');
        } else {
            $dueCardIds = SrsLog::where('user_id', $user->id)
                                ->whereIn('flashcard_id', $allCardIds)
                                ->pluck('flashcard_id');
        }

        // Pull brand new cards
        $newCardIds = Flashcard::where('subject_id', $subjectId)
                               ->whereNotIn('id', function($query) use ($user) {
                                   $query->select('flashcard_id')->from('srs_logs')->where('user_id', $user->id);
                               })
                               ->pluck('id');

        $studyPool = $dueCardIds->concat($newCardIds);
        $completedInSession = session()->get($completedKey, []);
        
        // Remove cards already answered in this active deck run
        $finalPool = $studyPool->reject(fn($id) => in_array($id, $completedInSession))->unique()->values();

        if ($finalPool->isNotEmpty() && !$isSessionActive) {
            session()->put($sessionKey, true);
        }

        // 🔴 DECK FINISHED: Completely clear the session context and save
        if ($finalPool->isEmpty()) {
            session()->forget([$sessionKey, $completedKey]);
            session()->save(); 

            return redirect()->route('student.flashcards.index')
                             ->with('success', 'Deck completed! Review timers have been initialized.');
        }

        $card = Flashcard::findOrFail($finalPool->first());
        $remaining = $finalPool->count();
        return view('users.flashcards.study', compact('card', 'subjectId', 'remaining'));
    }

    /**
     * 3. UPDATE SRS LOG
     * Configures the strict future intervals.
     */
    public function updateLog(Request $request)
    {
        $user = Auth::user();
        $cardId = $request->card_id;
        $rating = (int)$request->rating; 
        $subjectId = $request->subject_id;
        $currentTime = Carbon::now();

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

        // Strict future timestamp scheduling assignments
        if ($rating == 1) { // AGAIN
            $log->repetition_count = 0;
            $log->interval = 0;
            $log->next_review_date = $currentTime->copy()->addMinute(); // Safe immutable execution
        } elseif ($rating == 2) { // HARD
            $log->interval = 2;
            $log->next_review_date = $currentTime->copy()->addDays(2);
        } elseif ($rating == 3) { // GOOD
            $log->interval = 4;
            $log->next_review_date = $currentTime->copy()->addDays(4);
        } elseif ($rating == 4) { // EASY
            $log->interval = 7;
            $log->next_review_date = $currentTime->copy()->addDays(7);
        }

        $log->save();
        
        // Block this card from showing up again during this deck flip session
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