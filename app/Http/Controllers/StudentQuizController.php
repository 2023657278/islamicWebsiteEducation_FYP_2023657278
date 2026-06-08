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
    $student = Auth::user();

    // 🟢 FETCH ONLY SOLO DATA: Ignore PvP topics
    $subjects = Subject::whereHas('quizzes', function($query) {
            $query->whereNotIn('topic', ['PVP_ARENA_BATTLE', 'Global Battle', 'PVP Battle']);
        })
        ->withCount(['quizzes as solo_quizzes_count' => function($query) {
            $query->whereNotIn('topic', ['PVP_ARENA_BATTLE', 'Global Battle', 'PVP Battle']);
        }])
        ->get();

    // Progress stats
    $attempts = DB::table('quiz_attempts')->where('user_id', $student->id)->get();
    $completedCount = $attempts->unique('quiz_id')->count();
    $avgScore = $attempts->avg('score') ?? 0;

    return view('users.quizzes.level1_subjects', compact('subjects', 'completedCount', 'avgScore'));
}

    // Add this method to StudentQuizController
    public function selectMode($subject_id)
    {
        $subject = Subject::findOrFail($subject_id);
        return view('users.quizzes.mode_selection', compact('subject'));
    }

    // =========================================================
    // LEVEL 2: SELECT DIFFICULTY (Kept as is - Handles Locking)
    // =========================================================
    public function difficulties($subject_id)
    {
        $subject = Subject::findOrFail($subject_id);
        $student = Auth::user();

        // 🟢 FIX: Exclude PVP quizzes here so they don't break the total count calculation
        $allQuizzes = Quiz::where('subject_id', $subject_id)
                          ->whereNotIn('topic', ['PVP_ARENA_BATTLE', 'Global Battle', 'PVP Battle'])
                          ->get();
                          
        $attempts = DB::table('quiz_attempts')->where('user_id', $student->id)->get();

        $allowed = ['Easy'];
        $stats = [];

        foreach (['Easy', 'Medium', 'Hard'] as $level) {
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

        // 🟢 If all Easy solo quizzes are done and average score is passing (>= 50%)
        if ($stats['Easy']['total'] > 0 && $stats['Easy']['done'] == $stats['Easy']['total'] && $stats['Easy']['avg'] >= 50) {
            $allowed[] = 'Medium';
        }
        
        // 🟢 If Medium is open, and all Medium solo quizzes are cleared with a passing average
        if (in_array('Medium', $allowed) && $stats['Medium']['total'] > 0 && $stats['Medium']['done'] == $stats['Medium']['total'] && $stats['Medium']['avg'] >= 50) {
            $allowed[] = 'Hard';
        }

        return view('users.quizzes.level2_difficulties', compact('subject', 'allowed', 'stats'));
    }

    // =========================================================
    // LEVEL 3: SELECT TOPIC (Added - Bridges Difficulty to Topics)
    // =========================================================
    public function topicsByDifficulty($subject_id, $difficulty)
    {
        $subject = Subject::findOrFail($subject_id);

        // 🟢 ADD THIS: Exclude PvP-specific topics from the Solo list
        $topics = Quiz::where('subject_id', $subject_id)
        ->where('difficulty', $difficulty)
        ->whereNotIn('topic', ['PVP_ARENA_BATTLE', 'Global Battle']) 
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
        // 🟢 ADD THIS: Ensure we aren't showing PvP quizzes here
        $quizzes = Quiz::where('subject_id', $subject_id)
        ->where('difficulty', $difficulty)
        ->where('topic', $topic)
        ->where('topic', '!=', 'PVP_ARENA_BATTLE') 
        ->get();
        $topic = urldecode($topic);
        $student = Auth::user();

        // Check if we are in PvP mode from the URL
        $isPvpMode = request('mode') === 'pvp';

        // When fetching solo quizzes, exclude the PvP tag
        $quizzes = Quiz::where('subject_id', $subject_id)
               ->where('topic', '!=', 'PVP_ARENA_BATTLE') // 🟢 Keep Solo clean
               ->get();

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

        return view('users.quizzes.level4_list', compact('subject', 'difficulty', 'topic', 'quizzes', 'isPvpMode'));
    }

    // =========================================================
    // LEVEL 5: TAKE QUIZ (Kept as is)
    // =========================================================
   public function show(Request $request, $id)
{
    // 1. Load quiz with its questions and options for both modes
    $quiz = Quiz::with('questions.options')->findOrFail($id);
    
    // 2. Check if the URL is trying to access PvP mode (e.g., ?mode=pvp)
    // Or check if your topic is flagged as a PvP battle row
    if ($request->query('mode') === 'pvp' || $quiz->topic === 'PVP_ARENA_BATTLE') {
        
        // ⚔️ RUNS PVP MODE (Loads Arena layout)
        $boss_hp = 100; 
        $user_hp = Auth::user()->hp;
        $question = $quiz->questions->first(); 

        return view('users.quizzes.arena', compact('quiz', 'boss_hp', 'user_hp', 'question'));
    }

    // 👤 DEFAULT: RUNS SOLO MISSION (Loads full-screen standalone layout)
    return view('users.quizzes.take', compact('quiz'));
}

    // =========================================================
    // SUBMIT QUIZ (Kept as is - Includes Scoring, Analytics, Telegram)
    // =========================================================
    public function submit(Request $request, $id)
{
    $user = Auth::user();
    $quiz = Quiz::with(['questions.options'])->findOrFail($id);
    
    $earnedPoints = 0;
    $totalPossiblePoints = $quiz->questions->sum('points');

    foreach ($quiz->questions as $question) {
    $userAnswer = $request->input('q_' . $question->id); 
    $type = $question->question_type;

    // Standardizing types: handles 'single', 'single_choice', 'text', etc.
    if (in_array($type, ['single', 'single_choice'])) {
        $correctOption = $question->options->where('is_correct', 1)->first();
        if ($correctOption && $userAnswer == $correctOption->id) {
            $earnedPoints += $question->points;
        }
    } 
    elseif (in_array($type, ['multiple', 'multiple_choice']) && is_array($userAnswer)) {
        $correctOptionIds = $question->options->where('is_correct', 1)->pluck('id')->toArray();
        sort($userAnswer); sort($correctOptionIds);
        if ($userAnswer == $correctOptionIds) { $earnedPoints += $question->points; }
    } 
    // Inside StudentQuizController@submit loop:
elseif (in_array($type, ['text', 'fill_in_the_blank'])) {
    // We trim and lowercase both sides for a fair match
    $actualCorrectAnswer = trim($question->correct_answer_text);
    
    if (strtolower(trim($userAnswer)) === strtolower($actualCorrectAnswer)) {
        $earnedPoints += $question->points;
    }
}
}

    $percentage = ($totalPossiblePoints > 0) ? round(($earnedPoints / $totalPossiblePoints) * 100) : 0;

    // Warrior Stats Update
    if ($percentage < 50) {
        $user->decrement('hp', 10); 
    } else {
        $user->increment('xp', 20);
        if ($user->xp >= ($user->level * 100)) { $user->increment('level'); }
    }

    // Save Attempt
    DB::table('quiz_attempts')->insert([
        'user_id' => $user->id,
        'quiz_id' => $id,
        'score' => $percentage, // Percentage for analytics
        'total_questions' => $quiz->questions->count(), // Total count for "X out of Y"
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Analytics (Kept as is)
    $historyScores = DB::table('quiz_attempts')->where('user_id', $user->id)->orderBy('created_at', 'asc')->pluck('score')->toArray();
    $analyticsService = new AnalyticsService();
    $slope = $analyticsService->calculateSlope($historyScores);
    $status = $analyticsService->getInterpretation($slope);
    DB::table('student_analytics')->updateOrInsert(['user_id' => $user->id], [
        'current_slope' => $slope, 'status' => $status, 'last_calculated_at' => now(), 'updated_at' => now()
    ]);

    // Telegram Logic (Kept as is)
    if ($user->telegram_chat_id) {
        $telegram = new TelegramService();
        $telegram->sendMessage($user->telegram_chat_id, "New Quiz Result: {$percentage}%");
    }

    // MAP TO VIEW
    $score = $earnedPoints; 
    $totalQuestions = $quiz->questions->count();

    return view('users.quizzes.result', compact('quiz', 'score', 'totalQuestions', 'percentage'));
}
}