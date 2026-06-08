<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Subject;
use App\Models\Quiz;
use App\Services\AnalyticsService;

class StudentProgressController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // 1. FETCH ALL ATTEMPTS (For Global Stats & Trend Chart)
        $allAttempts = DB::table('quiz_attempts')
                      ->join('quizzes', 'quiz_attempts.quiz_id', '=', 'quizzes.id')
                      ->join('subjects', 'quizzes.subject_id', '=', 'subjects.id')
                      ->where('quiz_attempts.user_id', $user->id)
                      ->select('quiz_attempts.*', 'quizzes.title as quiz_title', 'subjects.subject_name')
                      ->orderBy('quiz_attempts.created_at', 'asc')
                      ->get();

        // 🟢 NEW: Detect Weak Topics (Focus Areas)
        $weakTopics = DB::table('quiz_attempts')
                      ->join('quizzes', 'quiz_attempts.quiz_id', '=', 'quizzes.id')
                      ->where('quiz_attempts.user_id', $user->id)
                      ->where('quiz_attempts.score', '<', 50)
                      ->select('quizzes.title', 'quiz_attempts.score')
                      ->orderBy('quiz_attempts.score', 'asc')
                      ->limit(3)
                      ->get();

        // 2. FETCH FILTERED ATTEMPTS (Specifically for the Detailed Log Table)
        $query = DB::table('quiz_attempts')
                      ->join('quizzes', 'quiz_attempts.quiz_id', '=', 'quizzes.id')
                      ->join('subjects', 'quizzes.subject_id', '=', 'subjects.id')
                      ->where('quiz_attempts.user_id', $user->id);

        // ✅ FILTER: Search Quiz Title
        if ($request->filled('search')) {
            $query->where('quizzes.title', 'like', '%' . $request->search . '%');
        }

        // ✅ FILTER: Pass/Fail Result
        if ($request->filled('result_status')) {
            if ($request->result_status == 'pass') {
                $query->where('quiz_attempts.score', '>=', 50);
            } else {
                $query->where('quiz_attempts.score', '<', 50);
            }
        }

        // ✅ FILTER: Subject Name (Fixes the Akidah/Al-Quran bug)
        if ($request->filled('subject_filter') && $request->subject_filter !== 'all') {
            $query->where('subjects.subject_name', $request->subject_filter);
        }

        // ✅ FILTER: Specific Date
        if ($request->filled('filter_date')) {
            $query->whereDate('quiz_attempts.created_at', $request->filter_date);
        }

        $attempts = $query->select('quiz_attempts.*', 'quizzes.title as quiz_title', 'subjects.subject_name')
                          ->orderBy('quiz_attempts.created_at', 'desc')
                          ->get();

        // 3. GLOBAL STATS (Based on full history)
        $totalQuizzes = $allAttempts->unique('quiz_id')->count();
        $currentAvg = $allAttempts->avg('score') ?? 0;

        // 4. ANALYTICS (Slope & Trend Prediction)
        $historyScores = $allAttempts->pluck('score')->toArray();
        $analyticsService = new AnalyticsService();
        $slope = $analyticsService->calculateSlope($historyScores);
        $status = $analyticsService->getInterpretation($slope);
        
        $n = count($historyScores);
        $predictedNextScore = 0;
        if ($n > 1) {
            $avgX = ($n + 1) / 2;
            $intercept = $currentAvg - ($slope * $avgX);
            $predictedNextScore = round(($slope * ($n + 1)) + $intercept, 1);
            $predictedNextScore = max(0, min(100, $predictedNextScore));
        } else {
            $predictedNextScore = round($currentAvg);
        }

        // 5. CHART DATA
        $dates = $allAttempts->map(fn($a) => \Carbon\Carbon::parse($a->created_at)->format('M d'));
        $scores = $historyScores;
        $trendPoints = [];
        if ($n > 1) {
            $avgX = ($n + 1) / 2;
            $intercept = $currentAvg - ($slope * $avgX);
            for ($i = 1; $i <= $n; $i++) {
                $trendPoints[] = round(($slope * $i) + $intercept, 1);
            }
        }

        // 6. SUBJECT MASTERY CARDS
        $allSubjects = Subject::all();
        $subjectProgress = [];
        foreach($allSubjects as $sub) {
            $quizIds = Quiz::where('subject_id', $sub->id)->pluck('id');
            $subAttempts = $allAttempts->whereIn('quiz_id', $quizIds);
            $subAvgScore = $subAttempts->count() > 0 ? round($subAttempts->avg('score')) : 0;
            
            if ($subAvgScore >= 80) { $rank = 'Expert'; $color = '#00C853'; }
            elseif ($subAvgScore >= 40) { $rank = 'Intermediate'; $color = '#2962FF'; }
            else { $rank = 'Beginner'; $color = '#FFAB00'; }

            $subjectProgress[] = (object) [
                'name' => $sub->subject_name,
                'total' => $quizIds->count(),
                'completed' => $subAttempts->unique('quiz_id')->count(),
                'avg_score' => $subAvgScore,
                'rank' => $rank,
                'color' => $color
            ];
        }

        $filterSubjects = $allSubjects->pluck('subject_name');

        return view('users.progress.index', compact(
            'currentAvg', 'totalQuizzes', 'slope', 'status', 'predictedNextScore',
            'dates', 'scores', 'trendPoints', 'subjectProgress', 'attempts', 'filterSubjects', 'weakTopics'
        ));
    }
}