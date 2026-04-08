<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resources;
use App\Models\Bookmark;
use App\Models\ExamPaper;
use App\Models\ExamResult;
use Illuminate\Support\Facades\Auth;

class PublicLibraryController extends Controller
{
    // --- LIBRARY: Show Notes & Check Bookmarks ---
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        // Fetch Public Notes
        $query = Resources::where('is_public', true);
        if ($search) {
            $query->where('title', 'LIKE', "%$search%");
        }
        $notes = $query->latest()->get();

        // Get list of IDs user has bookmarked (e.g., [1, 5, 8])
        $myBookmarks = Bookmark::where('user_id', Auth::id())->pluck('resource_id')->toArray();

        return view('library.index', compact('notes', 'myBookmarks', 'search'));
    }

    public function toggleBookmark($id)
    {
        $user = Auth::user();
        $bookmark = Bookmark::where('user_id', $user->id)->where('resource_id', $id)->first();

        if ($bookmark) {
            $bookmark->delete();
            return back()->with('success', 'Bookmark removed.');
        } else {
            Bookmark::create(['user_id' => $user->id, 'resource_id' => $id]);
            return back()->with('success', 'Added to bookmarks!');
        }
    }

    // --- EXAMS: List Available Papers ---
    public function examIndex()
    {
        $papers = ExamPaper::withCount('questions')->get();
        return view('exams.index', compact('papers'));
    }

    // --- EXAMS: Show the Question Paper ---
    public function takeExam($id)
    {
        $paper = ExamPaper::with('questions')->findOrFail($id);
        return view('exams.take', compact('paper'));
    }

    // --- EXAMS: Calculate Score ---
    public function submitExam(Request $request, $id)
    {
        $paper = ExamPaper::with('questions')->findOrFail($id);
        $score = 0;
        $answers = $request->input('answers', []);

        foreach ($paper->questions as $q) {
            // Check if student's answer matches correct option
            if (isset($answers[$q->id]) && $answers[$q->id] == $q->correct_option) {
                $score++;
            }
        }

        // Save History
        $result = ExamResult::create([
            'user_id' => Auth::id(),
            'exam_paper_id' => $id,
            'score' => $score,
            'total_questions' => $paper->questions->count()
        ]);

        return redirect()->route('exams.result', $result->id);
    }

    // --- RESULTS: Show Score ---
    public function showResult($result_id)
    {
        $result = ExamResult::with('paper')->findOrFail($result_id);
        
        // Security: Prevent students viewing others' results
        if($result->user_id != Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return view('exams.result', compact('result'));
    }

    // --- FLASHCARDS: Show Revision Mode ---
    public function flashcards($id)
    {
        $paper = ExamPaper::with('questions')->findOrFail($id);
        return view('exams.flashcards', compact('paper'));
    }
}