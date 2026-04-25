<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\AnalyticsService;
use App\Services\TelegramService;

class StudentQuizController extends Controller
{
    // =========================================================
    // LEVEL 1: SELECT SUBJECT (Kept as is)
    // =========================================================
    public function index()
    {
        $subjects = Subject::whereHas('quizzes')->get();
        $student = Auth::user();
        $attempts = DB::table('quiz_attempts')->where('user_id', $student->id)->get();
        $completedCount = $attempts->unique('quiz_id')->count();
        $avgScore = $attempts->avg('score') ?? 0;

        return view('users.quizzes.level1_subjects', compact('subjects', 'completedCount', 'avgScore'));
    }

    // =========================================================
    // LEVEL 2: SELECT DIFFICULTY (Kept as is - Handles Locking)
    // =========================================================
    public function difficulties($subject_id)
    {
        $subject = Subject::findOrFail($subject_id);
        $student = Auth::user();

        $allQuizzes = Quiz::where('subject_id', $subject_id)->get();
        $attempts = DB::table('quiz_attempts')->where('user_id', $student->id)->get();

        $allowed = ['Very Easy'];
        $stats = [];

        foreach (['Very Easy', 'Easy', 'Medium', 'Hard', 'Expert'] as $level) {
            $levelQuizzes = $allQuizzes->where('difficulty', $level);
            $done = 0;
            $avg = 0;

            if ($levelQuizzes->count() > 0) {
                $ids = $levelQuizzes->pluck('id');
                $userAttempts = $attempts->whereIn('quiz_id', $ids);
                $bestScores = $userAttempts->groupBy('quiz_id')->map(fn($q) => $q->max('score'));
                
                $done = $bestScores->count();
                $avg = $bestScores->avg() ?? 0;
            }

            $stats[$level] = [
                'total' => $levelQuizzes->count(),
                'done' => $done,
                'avg' => round($avg)
            ];
        }

        if ($stats['Very Easy']['total'] > 0 && $stats['Very Easy']['done'] == $stats['Very Easy']['total'] && $stats['Very Easy']['avg'] >= 50) {
            $allowed[] = 'Easy';
        }
        if (in_array('Easy', $allowed) && $stats['Easy']['total'] > 0 && $stats['Easy']['done'] == $stats['Easy']['total'] && $stats['Easy']['avg'] >= 50) {
            $allowed[] = 'Medium';
        }
        if (in_array('Medium', $allowed) && $stats['Medium']['total'] > 0 && $stats['Medium']['done'] == $stats['Medium']['total'] && $stats['Medium']['avg'] >= 50) {
            $allowed[] = 'Hard';
        }
        if (in_array('Hard', $allowed) && $stats['Hard']['total'] > 0 && $stats['Hard']['done'] == $stats['Hard']['total'] && $stats['Hard']['avg'] >= 50) {
            $allowed[] = 'Expert';
        }

        return view('users.quizzes.level2_difficulties', compact('subject', 'allowed', 'stats'));
    }

    // =========================================================
    // LEVEL 3: SELECT TOPIC (Added - Bridges Difficulty to Topics)
    // =========================================================
    public function topicsByDifficulty($subject_id, $difficulty)
    {
        $subject = Subject::findOrFail($subject_id);

        // Get distinct topics specifically for the chosen difficulty level
        $topics = Quiz::where('subject_id', $subject_id)
                      ->where('difficulty', $difficulty)
                      ->select('topic')
                      ->distinct()
                      ->pluck('topic');

        return view('users.quizzes.level3_topics', compact('subject', 'difficulty', 'topics'));
    }

    // =========================================================
    // LEVEL 4: FINAL QUIZ LIST (Updated - Filtered by Difficulty + Topic)
    // =========================================================
    public function listByTopic($subject_id, $difficulty, $topic)
    {
        $subject = Subject::findOrFail($subject_id);
        $topic = urldecode($topic);
        $student = Auth::user();

        // Query quizzes for specific subject, difficulty, and topic
        $query = Quiz::where('subject_id', $subject_id)->where('difficulty', $difficulty);
        
        if ($topic === 'General') {
            $query->where(function($q) {
                $q->whereNull('topic')->orWhere('topic', 'General');
            });
        } else {
            $query->where('topic', $topic);
        }

        $quizzes = $query->get();
        $attempts = DB::table('quiz_attempts')->where('user_id', $student->id)->get();

        foreach ($quizzes as $quiz) {
            $bestAttempt = $attempts->where('quiz_id', $quiz->id)->sortByDesc('score')->first();
            $quiz->my_score = $bestAttempt ? $bestAttempt->score : null;
            $quiz->is_completed = $bestAttempt ? true : false;
        }

        return view('users.quizzes.level4_list', compact('subject', 'difficulty', 'topic', 'quizzes'));
    }

    // =========================================================
    // LEVEL 5: TAKE QUIZ (Kept as is)
    // =========================================================
    public function show($id)
    {
        $quiz = Quiz::with('questions.options')->findOrFail($id);
        return view('users.quizzes.take', compact('quiz'));
    }

    // =========================================================
    // SUBMIT QUIZ (Kept as is - Includes Scoring, Analytics, Telegram)
    // =========================================================
    public function submit(Request $request, $id)
    {
        $user = Auth::user();
        $quiz = Quiz::with(['questions.options'])->findOrFail($id);
        
        $score = 0;
        $totalQuestions = $quiz->questions->count();
        $input = $request->all();

        foreach ($quiz->questions as $question) {
            $userKey = 'q_' . $question->id;
            if (!isset($input[$userKey])) continue;
            $userAnswer = $input[$userKey];

            if ($question->question_type === 'single') {
                $correctOption = $question->options->where('is_correct', 1)->first();
                if ($correctOption && $userAnswer == $correctOption->id) $score++;
            }
            elseif ($question->question_type === 'multiple' && is_array($userAnswer)) {
                $correctOptionIds = $question->options->where('is_correct', 1)->pluck('id')->toArray();
                sort($userAnswer); sort($correctOptionIds);
                if ($userAnswer == $correctOptionIds) $score++;
            }
            elseif ($question->question_type === 'text') {
                $correctOption = $question->options->where('is_correct', 1)->first();
                if ($correctOption && strtolower(trim($userAnswer)) == strtolower(trim($correctOption->option_text))) $score++;
            }
        }

        $percentage = ($totalQuestions > 0) ? round(($score / $totalQuestions) * 100) : 0;

        DB::table('quiz_attempts')->insert([
            'user_id' => $user->id,
            'quiz_id' => $id,
            'score' => $percentage,
            'total_questions' => $totalQuestions,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $historyScores = DB::table('quiz_attempts')->where('user_id', $user->id)->orderBy('created_at', 'asc')->pluck('score')->toArray();
        $analyticsService = new AnalyticsService();
        $slope = $analyticsService->calculateSlope($historyScores);
        $status = $analyticsService->getInterpretation($slope);

        DB::table('student_analytics')->updateOrInsert(['user_id' => $user->id], [
            'current_slope' => $slope, 'status' => $status, 'last_calculated_at' => now(), 'updated_at' => now()
        ]);

        if ($user->telegram_chat_id) {
            $statusEmoji = $percentage >= 50 ? '✅' : '⚠️';
            $statusText = $percentage >= 50 ? 'Passed' : 'Needs Improvement';
            $msg  = "<b>New Quiz Result!</b> 📝\n\n📘 <b>Quiz:</b> {$quiz->title}\n📊 <b>Score:</b> {$percentage}%\n🏆 <b>Status:</b> {$statusEmoji} {$statusText}\n📅 <b>Date:</b> " . now()->format('d M Y, h:i A');
            $telegram = new TelegramService();
            $telegram->sendMessage($user->telegram_chat_id, $msg);
        }

        return view('users.quizzes.result', compact('quiz', 'score', 'totalQuestions', 'percentage'));
    }
}