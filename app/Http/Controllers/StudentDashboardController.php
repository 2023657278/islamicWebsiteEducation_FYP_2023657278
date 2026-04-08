<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\QuizAttempt;
use App\Services\AnalyticsService;

class StudentDashboardController extends Controller
{
    /**
     * Display the Student Dashboard
     */
    public function index()
    {
        $user = Auth::user();

        // 1. FETCH ALL DATA (Ordered by Date)
        // Load the quiz and subject relations to avoid separate queries later
        $attempts = QuizAttempt::with(['quiz.subject'])
                        ->where('user_id', $user->id)
                        ->orderBy('created_at', 'asc')
                        ->get();

        // 2. BASIC STATS
        $totalQuizzes = $attempts->count();
        $averageScore = $attempts->avg('score') ?? 0;

        // 3. LIVE ANALYTICS CALCULATION
        $analyticsService = new AnalyticsService();
        $slope = $analyticsService->calculateSlope($attempts->pluck('score')->toArray());
        $status = $analyticsService->getInterpretation($slope);

        // 4. ✅ CALCULATE SUBJECT PERFORMANCE FOR PIE CHART
        // We group the attempts by the subject name and calculate the average score for each
        $subjectPerformance = $attempts->groupBy(function($item) {
            return $item->quiz->subject->subject_name ?? 'General';
        })->map(function($group) {
            return round($group->avg('score'), 1);
        })->toArray();

        // Fallback for new students with no data so the chart doesn't crash
        if (empty($subjectPerformance)) {
            $subjectPerformance = ['No Data' => 0];
        }

        // 5. CHART DATA (Last 10 quizzes)
        $quizHistory = $attempts->take(-10); 

        return view('users.dashboard', compact(
            'user', 
            'totalQuizzes', 
            'averageScore', 
            'slope', 
            'status', 
            'quizHistory',
            'subjectPerformance' // ✅ Successfully passed to the view
        ));
    }
}