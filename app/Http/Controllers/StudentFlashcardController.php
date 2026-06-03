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
            
            $dueCount = SrsLog::where('user_id', $user->id)
                              ->whereIn('flashcard_id', $allCardIds)
                              ->where('next_review_date', '<=', now())
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

    /**
     * 2. STUDY ENGINE (Session-Aware Full Flip)
     */
    public function study(Request $request, $subjectId)
    {
        $user = Auth::user();
        $allCardIds = Flashcard::where('subject_id', $subjectId)->pluck('id');
        $isSessionActive = session()->get('srs_session_active_' . $subjectId, false);

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
        $completedInSession = session()->get('srs_completed_cards_' . $subjectId, []);
        $studyPool = $studyPool->reject(fn($id) => in_array($id, $completedInSession))->unique()->values();

        if ($studyPool->isNotEmpty() && !$isSessionActive) {
            session()->put('srs_session_active_' . $subjectId, true);
        }

        if ($studyPool->isEmpty()) {
            session()->forget(['srs_session_active_' . $subjectId, 'srs_completed_cards_' . $subjectId]);
            return redirect()->route('student.flashcards.index')->with('success', 'All ready read! Timer started.');
        }

        $card = Flashcard::findOrFail($studyPool->first());
        $remaining = $studyPool->count();
        return view('users.flashcards.study', compact('card', 'subjectId', 'remaining'));
    }

    /**
     * 3. UPDATE LOG
     */
    public function updateLog(Request $request)
    {
        $user = Auth::user();
        $log = SrsLog::firstOrNew(['user_id' => $user->id, 'flashcard_id' => $request->card_id]);

        if (!$log->exists) {
            $log->fill(['ease_factor' => 2.5, 'repetition_count' => 0, 'interval' => 0]);
        }

        $rating = (int)$request->rating;
        if ($rating == 1) {
            $log->repetition_count = 0;
            $log->interval = 0;
            $log->ease_factor = max(1.3, $log->ease_factor - 0.20);
            $log->next_review_date = now()->addMinute();
        } else {
            $log->ease_factor = max(1.3, $log->ease_factor + (0.1 - (5 - $rating) * (0.08 + (5 - $rating) * 0.02)));
            $log->repetition_count++;
            $log->interval = ($log->repetition_count == 1) ? ($rating == 4 ? 4 : 1) : ($log->repetition_count == 2 ? 6 : round($log->interval * $log->ease_factor));
            $log->next_review_date = now()->addDays($log->interval);
        }

        $log->save();
        session()->push('srs_completed_cards_' . $request->subject_id, $request->card_id);
        return redirect()->route('student.flashcards.study', $request->subject_id);
    }

    /**
     * 4. MANUAL MODE (Fixed: This was missing!)
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