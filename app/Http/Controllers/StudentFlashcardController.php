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
    public function index()
    {
        $user = Auth::user();
        $subjects = Subject::all();
        $now = Carbon::now();

        foreach($subjects as $subject) {
            $allCardIds = Flashcard::where('subject_id', $subject->id)->pluck('id');
            
            // Strictly Overdue
            $dueCount = SrsLog::where('user_id', $user->id)
                              ->whereIn('flashcard_id', $allCardIds)
                              ->where('next_review_date', '<=', $now->subSeconds(5)) // Buffer
                              ->count();

            // Brand New (No log exists)
            $newCount = Flashcard::where('subject_id', $subject->id)
                                 ->whereNotIn('id', function($query) use ($user) {
                                     $query->select('flashcard_id')->from('srs_logs')->where('user_id', $user->id);
                                 })
                                 ->count();

            $subject->due_cards = $dueCount + $newCount;
            
            $styles = [
                'Al-Quran' => ['icon' => 'fa-quran', 'color' => 'primary'],
                'Hadith'   => ['icon' => 'fa-book-open', 'color' => 'success'],
                'Akidah'   => ['icon' => 'fa-star-and-crescent', 'color' => 'info'],
                'Fiqh'     => ['icon' => 'fa-scale-balanced', 'color' => 'warning'],
                'Sirah'    => ['icon' => 'fa-landmark', 'color' => 'danger'],
                'Akhlak'   => ['icon' => 'fa-heart', 'color' => 'secondary'],
            ];
            $subject->style = $styles[$subject->subject_name] ?? ['icon' => 'fa-layer-group', 'color' => 'primary'];
        }

        return view('users.flashcards.index', compact('subjects'));
    }

    public function study(Request $request, $subjectId)
    {
        $user = Auth::user();
        $allCardIds = Flashcard::where('subject_id', $subjectId)->pluck('id');
        $sessionKey = 'srs_active_' . $subjectId;
        $doneKey = 'srs_done_' . $subjectId;

        // If not in a session, find what is due right now
        if (!session()->has($sessionKey)) {
            $dueIds = SrsLog::where('user_id', $user->id)
                            ->whereIn('flashcard_id', $allCardIds)
                            ->where('next_review_date', '<=', Carbon::now())
                            ->pluck('flashcard_id');
            
            $newIds = Flashcard::where('subject_id', $subjectId)
                               ->whereNotIn('id', function($q) use ($user) {
                                   $q->select('flashcard_id')->from('srs_logs')->where('user_id', $user->id);
                               })->pluck('id');

            $pool = $dueIds->concat($newIds)->unique()->toArray();
            
            if (empty($pool)) {
                return redirect()->route('student.flashcards.index')->with('success', 'Caught up!');
            }

            session()->put($sessionKey, $pool);
            session()->put($doneKey, []);
        }

        $currentPool = session()->get($sessionKey);
        $doneIds = session()->get($doneKey);
        
        $remainingIds = array_diff($currentPool, $doneIds);

        if (empty($remainingIds)) {
            session()->forget([$sessionKey, $doneKey]);
            session()->save();
            return redirect()->route('student.flashcards.index')->with('success', 'Deck Finished!');
        }

        $card = Flashcard::findOrFail(reset($remainingIds));
        $remaining = count($remainingIds);

        return view('users.flashcards.study', compact('card', 'subjectId', 'remaining'));
    }

    public function updateLog(Request $request)
    {
        $user = Auth::user();
        $rating = (int)$request->rating;
        $subjectId = $request->subject_id;

        $log = SrsLog::firstOrNew(['user_id' => $user->id, 'flashcard_id' => $request->card_id]);
        
        // Manual Interval Mapping as requested
        $times = [1 => 1, 2 => 2880, 3 => 5760, 4 => 10080]; // Minutes: 1, 2880 (2d), 5760 (4d), 10080 (7d)
        $log->next_review_date = Carbon::now()->addMinutes($times[$rating]);
        $log->save();

        session()->push('srs_done_' . $subjectId, $request->card_id);
        return redirect()->route('student.flashcards.study', $subjectId);
    }

    public function manual($subjectId)
    {
        $subject = Subject::findOrFail($subjectId);
        $cards = Flashcard::where('subject_id', $subjectId)->simplePaginate(1);
        $total = Flashcard::where('subject_id', $subjectId)->count();
        return view('users.flashcards.manual', compact('cards', 'subject', 'total'))->with([
            'current' => $cards->currentPage(),
            'progress' => $total > 0 ? ($cards->currentPage() / $total) * 100 : 0
        ]);
    }
}