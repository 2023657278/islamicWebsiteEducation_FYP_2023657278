<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\QuizAttempt;
use App\Models\Subject; 
use App\Models\Quiz; // 🟢 Added to query quizzes directly
use App\Services\AnalyticsService;

class StudentDashboardController extends Controller
{
    /**
     * Display the Student Dashboard
     */
    public function index()
    {
        $user = Auth::user();

        // 1. FETCH ALL ATTEMPTS (Ordered by Date)
        $attempts = QuizAttempt::with(['quiz.subject'])
                        ->where('user_id', $user->id)
                        ->orderBy('created_at', 'asc')
                        ->get();

        // Get an array of quiz IDs that the user has already answered/attempted
        $answeredQuizIds = $attempts->pluck('quiz_id')->unique()->toArray();

        // 2. BASIC STATS
        $totalQuizzes = $attempts->count();
        $averageScore = $attempts->avg('score') ?? 0;

        // 3. LIVE ANALYTICS CALCULATION
        $analyticsService = new AnalyticsService();
        $slope = $analyticsService->calculateSlope($attempts->pluck('score')->toArray());
        $status = $analyticsService->getInterpretation($slope);

        // 4. CHART DATA (Last 10 quizzes)
        $quizHistory = $attempts->take(-10); 

        // 5. FETCH ALL SUBJECTS & GENERATE THE PROGRESS DETAILS
        $allSubjects = Subject::all();
        $subjectProgress = [];
        $subjectPerformance = [];

        foreach ($allSubjects as $sub) {
            // Filter user attempts for this specific subject
            $subAttempts = $attempts->filter(function($item) use ($sub) {
                return $item->quiz && $item->quiz->subject_id == $sub->id;
            });
            
            $subAvgScore = $subAttempts->count() > 0 ? round($subAttempts->avg('score'), 1) : 0;

            if ($subAvgScore >= 75) {
                $rank = 'Al-Fatih'; $badge = 'badge-success bg-success'; $color = '#10B981'; $icon = 'fa-crown';
            } elseif ($subAvgScore >= 40) {
                $rank = 'Pejuang'; $badge = 'badge-warning bg-warning text-dark'; $color = '#F59E0B'; $icon = 'fa-shield-alt';
            } else {
                $rank = 'Musafir'; $badge = 'badge-danger bg-danger'; $color = '#D93025'; $icon = 'fa-compass';
            }

            // 🟢 GET ALL QUIZZES/TOPICS BELONGING TO THIS SUBJECT FOR THE ROADMAP
            // Group topics by difficulty tier
            $quizzes = Quiz::where('subject_id', $sub->id)->get();
            $roadmapQuizzes = [];
            
            foreach ($quizzes as $quiz) {
                $roadmapQuizzes[] = [
                    'id' => $quiz->id,
                    'title' => $quiz->title,
                    'topic' => $quiz->topic ?? 'General Concept',
                    'difficulty' => $quiz->difficulty, // 'Easy', 'Medium', 'Hard'
                    'is_answered' => in_array($quiz->id, $answeredQuizIds) // 🟢 Checks if already answered
                ];
            }

            $subjectProgress[] = (object) [
                'id' => $sub->id,
                'name' => $sub->subject_name,
                'avg_score' => $subAvgScore,
                'rank' => $rank,
                'badge' => $badge,
                'color' => $color,
                'icon' => $icon,
                'attempts_count' => $subAttempts->count(),
                'quizzes' => $roadmapQuizzes // 🟢 Passed into view context loop
            ];

            $subjectPerformance[$sub->subject_name] = $subAvgScore > 0 ? $subAvgScore : 0.1;
        }

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