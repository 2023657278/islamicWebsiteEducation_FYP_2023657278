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
     * Display the Gamified Student Dashboard (Quest Hub)
     */
    public function index()
    {
        $user = Auth::user();

        // 1. FETCH ALL ATTEMPTS DATA (Ordered by Date)
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

        // 4. ✅ DYNAMIC SUBJECT PROGRESS & PIE CHART DATA ENGINE
        // Ambil semua subjek sedia ada daripada database (Al-Quran, Hadith, Akidah, Fiqh, Sirah, Akhlak)
        $allSubjects = Subject::all();
        $subjectProgress = [];
        $subjectPerformance = [];

        foreach ($allSubjects as $sub) {
            // Ambil semua percubaan kuiz bagi subjek ini
            $subAttempts = $attempts->filter(function($attempt) use ($sub) {
                return $attempt->quiz && $attempt->quiz->subject_id == $sub->id;
            });

            $subAvgScore = $subAttempts->count() > 0 ? round($subAttempts->avg('score'), 1) : 0;

            // Gamified Milestone Rank Assignment Matrix
            if ($subAvgScore >= 75) {
                $rankTitle = 'Al-Fatih';
                $badgeClass = 'badge-success bg-success';
                $colorTheme = '#10B981'; // Hijau Mutiara
                $iconShape = 'fa-crown';
            } elseif ($subAvgScore >= 40) {
                $rankTitle = 'Pejuang';
                $badgeClass = 'badge-warning bg-warning text-dark';
                $colorTheme = '#F59E0B'; // Emas/Kuning
                $iconShape = 'fa-shield-alt';
            } else {
                $rankTitle = 'Musafir';
                $badgeClass = 'badge-danger bg-danger';
                $colorTheme = '#D93025'; // Merah
                $iconShape = 'fa-compass';
            }

            // Simpan ke dalam array progres untuk paparan Quest Tree di sebelah kiri
            $subjectProgress[] = (object) [
                'name' => $sub->subject_name,
                'avg_score' => $subAvgScore,
                'rank' => $rankTitle,
                'badge' => $badgeClass,
                'color' => $colorTheme,
                'icon' => $iconShape,
                'attempts_count' => $subAttempts->count()
            ];

            // Masukkan ke dalam array untuk kegunaan Carta Pai (Keperluan Pensyarah)
            $subjectPerformance[$sub->subject_name] = $subAvgScore;
        }

        // Fallback sekiranya pelajar baru langsung tidak mempunyai data kuiz
        if (empty($attempts) || $totalQuizzes == 0) {
            $subjectPerformance = ['Belum Ada Data' => 100];
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
            'subjectProgress',      // 🟢 Dihantar untuk Milestone Quest Tree
            'subjectPerformance'   // 🟢 Dihantar untuk Carta Pai Pensyarah
        ));
    }
}