<?php

namespace App\Http\Controllers;

use App\Models\QuizAttempt;
use App\Models\Group;
use App\Models\Subject;
use App\Services\AnalyticsService; // Import the Service
use Illuminate\Http\Request;

class TeacherResultController extends Controller
{
    protected $analytics;

    // Inject Service
    public function __construct(AnalyticsService $analytics)
    {
        $this->analytics = $analytics;
    }

    public function index(Request $request)
    {
        $groups = Group::all(); 
        $subjects = Subject::all(); 

        // 1. Base Query
        $query = QuizAttempt::with(['user.group', 'quiz.subject']);

        // 2. Search Filters
        if ($request->filled('search_name')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search_name . '%');
            });
        }
        if ($request->filled('search_group')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('group_id', $request->search_group);
            });
        }
        if ($request->filled('search_subject')) {
            $query->whereHas('quiz.subject', function($q) use ($request) {
                $q->where('id', $request->search_subject);
            });
        }

        // 3. Get Data
        $results = $query->latest()->paginate(15);
        $results->appends($request->all()); 

        // 4. CALCULATE TREND FOR EACH ROW
        // Note: This runs a small query for each row. For <1000 students this is fine.
        foreach ($results as $attempt) {
            // Get history for this student in this specific subject
            $history = QuizAttempt::where('user_id', $attempt->user_id)
                        ->whereHas('quiz', function($q) use ($attempt) {
                            $q->where('subject_id', $attempt->quiz->subject_id);
                        })
                        ->orderBy('created_at', 'asc') // Oldest first for correct slope
                        ->pluck('score')
                        ->toArray();

            // Calculate Math
            $attempt->slope = $this->analytics->calculateSlope($history);
            $attempt->trend = $this->analytics->getInterpretation($attempt->slope);
        }

        return view('results.index', compact('results', 'groups', 'subjects'));
    }

    public function destroy($id)
    {
        $attempt = QuizAttempt::findOrFail($id);
        $attempt->delete();
        return back()->with('success', 'Result deleted.');
    }
}