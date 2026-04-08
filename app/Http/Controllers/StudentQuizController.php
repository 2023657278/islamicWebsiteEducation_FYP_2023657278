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
    // LEVEL 1: SELECT SUBJECT
    // =========================================================
    public function index()
    {
        // Only show subjects that have quizzes
        $subjects = Subject::whereHas('quizzes')->get();
        
        // Basic Stats for Header
        $student = Auth::user();
        $attempts = DB::table('quiz_attempts')->where('user_id', $student->id)->get();
        $completedCount = $attempts->unique('quiz_id')->count();
        $avgScore = $attempts->avg('score') ?? 0;

        return view('users.quizzes.level1_subjects', compact('subjects', 'completedCount', 'avgScore'));
    }

    // =========================================================
    // LEVEL 2: SELECT TOPIC (Subtopic)
    // =========================================================
    public function topics($subject_id)
    {
        $subject = Subject::findOrFail($subject_id);

        // Get distinct topics for this subject
        // If topic is NULL in DB, we label it as 'General' in the view
        $topics = Quiz::where('subject_id', $subject_id)
                      ->select('topic')
                      ->distinct()
                      ->pluck('topic');

        return view('users.quizzes.level2_topics', compact('subject', 'topics'));
    }

    // =========================================================
    // LEVEL 3: SELECT QUIZ (Grouped by Difficulty + Locking Logic)
    // =========================================================
    public function list($subject_id, $topic)
    {
        $subject = Subject::findOrFail($subject_id);
        $student = Auth::user();
        $topic = urldecode($topic); // Handle URL encoding

        // 1. Fetch Quizzes (Handle 'General' case for empty topics)
        $query = Quiz::where('subject_id', $subject_id);
        
        if ($topic === 'General') {
            $query->where(function($q) {
                $q->whereNull('topic')->orWhere('topic', 'General');
            });
        } else {
            $query->where('topic', $topic);
        }
        
        $quizzes = $query->get();

        // 2. Calculate Subject Average (For Unlock Logic)
        // We look at ALL quizzes for this subject to determine proficiency
        $allSubjectQuizIds = Quiz::where('subject_id', $subject_id)->pluck('id');
        $subjectAttempts = DB::table('quiz_attempts')
                             ->where('user_id', $student->id)
                             ->whereIn('quiz_id', $allSubjectQuizIds)
                             ->get();
        
        $avgScore = $subjectAttempts->count() > 0 ? $subjectAttempts->avg('score') : 0;

        // 3. Define Allowed Difficulties based on Average Score
        $allowed = ['Easy']; // Easy is always unlocked
        if ($avgScore >= 40) $allowed[] = 'Medium';
        if ($avgScore >= 80) $allowed[] = 'Hard';

        // 4. Check completion status for each quiz
        $attempts = DB::table('quiz_attempts')->where('user_id', $student->id)->get();

        foreach ($quizzes as $quiz) {
            $bestAttempt = $attempts->where('quiz_id', $quiz->id)->sortByDesc('score')->first();
            $quiz->my_score = $bestAttempt ? $bestAttempt->score : null;
            $quiz->is_completed = $bestAttempt ? true : false;
        }

        // Group by Difficulty for the Tabs
        $groupedQuizzes = $quizzes->groupBy('difficulty'); 

        // Pass 'allowed' array to view to disable buttons if needed
        return view('users.quizzes.level3_list', compact('subject', 'topic', 'groupedQuizzes', 'allowed', 'avgScore'));
    }

    // =========================================================
    // LEVEL 4: TAKE QUIZ (Show Questions)
    // =========================================================
    public function show($id)
    {
        // Load questions with options
        $quiz = Quiz::with('questions.options')->findOrFail($id);
        return view('users.quizzes.take', compact('quiz'));
    }

    // =========================================================
    // SUBMIT QUIZ (Scoring + Analytics + Telegram)
    // =========================================================
    public function submit(Request $request, $id)
    {
        $user = Auth::user();
        $quiz = Quiz::with(['questions.options'])->findOrFail($id);
        
        $score = 0;
        $totalQuestions = $quiz->questions->count();
        $input = $request->all();

        // 1. SCORING LOGIC
        foreach ($quiz->questions as $question) {
            $userKey = 'q_' . $question->id;
            
            if (!isset($input[$userKey])) continue;

            $userAnswer = $input[$userKey];

            // A. SINGLE CHOICE
            if ($question->question_type === 'single') {
                $correctOption = $question->options->where('is_correct', 1)->first();
                if ($correctOption && $userAnswer == $correctOption->id) {
                    $score++;
                }
            }
            
            // B. MULTIPLE CHOICE
            elseif ($question->question_type === 'multiple' && is_array($userAnswer)) {
                $correctOptionIds = $question->options->where('is_correct', 1)->pluck('id')->toArray();
                sort($userAnswer);
                sort($correctOptionIds);
                if ($userAnswer == $correctOptionIds) {
                    $score++;
                }
            }
            
            // C. TEXT ANSWER
            elseif ($question->question_type === 'text') {
                $correctOption = $question->options->where('is_correct', 1)->first();
                if ($correctOption && strtolower(trim($userAnswer)) == strtolower(trim($correctOption->option_text))) {
                    $score++;
                }
            }
        }

        $percentage = ($totalQuestions > 0) ? round(($score / $totalQuestions) * 100) : 0;

        // 2. SAVE ATTEMPT
        DB::table('quiz_attempts')->insert([
            'user_id' => $user->id,
            'quiz_id' => $id,
            'score' => $percentage,
            'total_questions' => $totalQuestions,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. ANALYTICS (LINEAR REGRESSION SLOPE)
        $historyScores = DB::table('quiz_attempts')
                            ->where('user_id', $user->id)
                            ->orderBy('created_at', 'asc') 
                            ->pluck('score')
                            ->toArray();

        $analyticsService = new AnalyticsService();
        $slope = $analyticsService->calculateSlope($historyScores);
        $status = $analyticsService->getInterpretation($slope);

        DB::table('student_analytics')->updateOrInsert(
            ['user_id' => $user->id],
            [
                'current_slope' => $slope,
                'status' => $status,
                'last_calculated_at' => now(),
                'updated_at' => now()
            ]
        );

        // 4. TELEGRAM NOTIFICATION
        if ($user->telegram_chat_id) {
            $statusEmoji = $percentage >= 50 ? '✅' : '⚠️';
            $statusText = $percentage >= 50 ? 'Passed' : 'Needs Improvement';

            $msg  = "<b>New Quiz Result!</b> 📝\n\n";
            $msg .= "📘 <b>Quiz:</b> {$quiz->title}\n";
            $msg .= "📊 <b>Score:</b> {$percentage}%\n";
            $msg .= "🏆 <b>Status:</b> {$statusEmoji} {$statusText}\n";
            $msg .= "📅 <b>Date:</b> " . now()->format('d M Y, h:i A');

            $telegram = new TelegramService();
            $telegram->sendMessage($user->telegram_chat_id, $msg);
        }

        return view('users.quizzes.result', compact('quiz', 'score', 'totalQuestions', 'percentage'));
    }
}