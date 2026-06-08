<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\QuizAttempt;
use App\Models\Subject; // 🟢 Minimal addition to query subject definitions cleanly
use App\Services\AnalyticsService;

class StudentDashboardController extends Controller
{
    /**
     * Display the Student Dashboard
     */
    public function index()
    {
        $user = Auth::user();

        // 1. FETCH ALL DATA (Ordered by Date) - KEEP AS IS
        $attempts = QuizAttempt::with(['quiz.subject'])
                        ->where('user_id', $user->id)
                        ->orderBy('created_at', 'asc')
                        ->get();

        // 2. BASIC STATS - KEEP AS IS
        $totalQuizzes = $attempts->count();
        $averageScore = $attempts->avg('score') ?? 0;

        // 3. LIVE ANALYTICS CALCULATION - KEEP AS IS
        $analyticsService = new AnalyticsService();
        $slope = $analyticsService->calculateSlope($attempts->pluck('score')->toArray());
        $status = $analyticsService->getInterpretation($slope);

        // 4. ✅ CALCULATE SUBJECT PERFORMANCE FOR PIE CHART - KEEP AS IS
        $subjectPerformance = $attempts->groupBy(function($item) {
            return $item->quiz->subject->subject_name ?? 'General';
        })->map(function($group) {
            return round($group->avg('score'), 1);
        })->toArray();

        if (empty($subjectPerformance)) {
            $subjectPerformance = ['No Data' => 0];
        }

        // 5. CHART DATA (Last 10 quizzes) - KEEP AS IS
        $quizHistory = $attempts->take(-10); 

        // 🟢 MINIMAL TARGETED ADDITION: Map your history into the 6 gamified progress metrics
        $allSubjects = Subject::all();
        $subjectProgress = [];
        foreach ($allSubjects as $sub) {
            $subAttempts = $attempts->filter(function($item) use ($sub) {
                return $item->quiz && $item->quiz->subject_id == $sub->id;
            });
            $subAvgScore = $subAttempts->count() > 0 ? round($subAttempts->avg('score')) : 0;

            if ($subAvgScore >= 75) {
                $rank = 'Al-Fatih'; $badge = 'badge-success bg-success'; $color = '#10B981'; $icon = 'fa-crown';
            } elseif ($subAvgScore >= 40) {
                $rank = 'Pejuang'; $badge = 'badge-warning bg-warning text-dark'; $color = '#F59E0B'; $icon = 'fa-shield-alt';
            } else {
                $rank = 'Musafir'; $badge = 'badge-danger bg-danger'; $color = '#D93025'; $icon = 'fa-compass';
            }

            $subjectProgress[] = (object) [
                'name' => $sub->subject_name,
                'avg_score' => $subAvgScore,
                'rank' => $rank,
                'badge' => $badge,
                'color' => $color,
                'icon' => $icon,
                'attempts_count' => $subAttempts->count()
            ];
        }

        // 🟢 COMPACT ARRAY: Merged subjectProgress with all your original payload data boundaries safely
        return view('users.dashboard', compact(
            'user', 'totalQuizzes', 'averageScore', 'slope', 'status', 'quizHistory', 'subjectPerformance', 'subjectProgress'
        ));
    }
}