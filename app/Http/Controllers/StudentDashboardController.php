<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\QuizAttempt;
use App\Models\Subject; 
use App\Services\AnalyticsService;

class StudentDashboardController extends Controller
{
    /**
     * Display the Student Dashboard
     */
    public function index()
    {
        $user = Auth::user();

        // 1. FETCH ALL DATA (Ordered by Date) - KEPT AS IS
        $attempts = QuizAttempt::with(['quiz.subject'])
                        ->where('user_id', $user->id)
                        ->orderBy('created_at', 'asc')
                        ->get();

        // 2. BASIC STATS - KEPT AS IS
        $totalQuizzes = $attempts->count();
        $averageScore = $attempts->avg('score') ?? 0;

        // 3. LIVE ANALYTICS CALCULATION - KEPT AS IS
        $analyticsService = new AnalyticsService();
        $slope = $analyticsService->calculateSlope($attempts->pluck('score')->toArray());
        $status = $analyticsService->getInterpretation($slope);

        // 5. CHART DATA (Last 10 quizzes) - KEPT AS IS
        $quizHistory = $attempts->take(-10); 

        // 🟢 MINIMAL TARGETED UPDATE: Initialize all subjects dynamically from the master database
        $allSubjects = Subject::all();
        $subjectProgress = [];
        $subjectPerformance = [];

        foreach ($allSubjects as $sub) {
            // Filter user attempts for this specific subject
            $subAttempts = $attempts->filter(function($item) use ($sub) {
                return $item->quiz && $item->quiz->subject_id == $sub->id;
            });
            
            $subAvgScore = $subAttempts->count() > 0 ? round($subAttempts->avg('score'), 1) : 0;

            // Gamified rank calculation configuration matrix
            if ($subAvgScore >= 75) {
                $rank = 'Al-Fatih'; 
                $badge = 'badge-success bg-success'; 
                $color = '#10B981'; 
                $icon = 'fa-crown';
            } elseif ($subAvgScore >= 40) {
                $rank = 'Pejuang'; 
                $badge = 'badge-warning bg-warning text-dark'; 
                $color = '#F59E0B'; 
                $icon = 'fa-shield-alt';
            } else {
                $rank = 'Musafir'; 
                $badge = 'badge-danger bg-danger'; 
                $color = '#D93025'; 
                $icon = 'fa-compass';
            }

            // Append structured dataset object for the in-card roadmap tree view
            $subjectProgress[] = (object) [
                'name' => $sub->subject_name,
                'avg_score' => $subAvgScore,
                'rank' => $rank,
                'badge' => $badge,
                'color' => $color,
                'icon' => $icon,
                'attempts_count' => $subAttempts->count()
            ];

            // 🟢 FIXED CHART MAPPER: Force allocation of all subject keys so colors render correctly on frontend legends
            // If the score is 0, we give it a tiny value (like 0.1) so it shows up on the chart configuration list
            $subjectPerformance[$sub->subject_name] = $subAvgScore > 0 ? $subAvgScore : 0.1;
        }

        // Fallback boundary handling safety metric loop if no data exists anywhere
        if ($totalQuizzes === 0) {
            $subjectPerformance = [];
            foreach ($allSubjects as $sub) {
                $subjectPerformance[$sub->subject_name] = 0.1;
            }
        }

        // Return unified data payloads out securely to users.dashboard view
        return view('users.dashboard', compact(
            'user', 
            'totalQuizzes', 
            'averageScore', 
            'slope', 
            'status', 
            'quizHistory', 
            'subjectPerformance', 
            'subjectProgress'
        ));
    }
}