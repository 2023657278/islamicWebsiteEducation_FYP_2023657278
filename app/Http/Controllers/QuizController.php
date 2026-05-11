<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Subject;
use App\Models\Result;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\AnalyticsService;

class QuizController extends Controller
{
    // =========================================================
    // PART A: STUDENT FUNCTIONS (Kept for Analytics logic)
    // =========================================================

    public function show($id)
    {
        $quiz = Quiz::with('questions.options')->findOrFail($id);
        return view('users.quizzes.take', compact('quiz'));
    }

    public function submit(Request $request)
    {
        $user = Auth::user();
        $score = $request->input('score');
        $totalQuestions = $request->input('total_questions');
        $percentage = ($score / $totalQuestions) * 100;

        Result::create([
            'user_id' => $user->id,
            'quiz_id' => $request->input('quiz_id'),
            'score' => $percentage,
            'total_questions' => $totalQuestions,
            'completed_at' => now(),
        ]);

        $historyScores = Result::where('user_id', $user->id)
                                ->orderBy('created_at', 'asc')
                                ->pluck('score')
                                ->toArray();

        $analytics = new AnalyticsService();
        $slope = $analytics->calculateSlope($historyScores);
        $status = $analytics->getInterpretation($slope);

        DB::table('student_analytics')->updateOrInsert(
            ['user_id' => $user->id],
            [
                'current_slope' => $slope,
                'status' => $status,
                'last_calculated_at' => now(),
                'updated_at' => now()
            ]
        );

        return response()->json([
            'message' => 'Quiz submitted successfully!',
            'score' => $percentage
        ]);
    }


    // =========================================================
    // PART B: TEACHER FUNCTIONS (Simplified Queries)
    // =========================================================

    public function index()
    {
        // Removed withCount and withAvg to improve performance
        $quizzes = Quiz::where('teacher_id', Auth::id())
                        ->with(['subject']) 
                        ->latest()
                        ->get();
        
        $subjects = Subject::all();
        return view('quizzes.index', compact('quizzes', 'subjects'));
    }

    public function create()
    {
        $subjects = Subject::all();
        return view('quizzes.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'duration_minutes' => 'required|integer|min:1',
            'topic' => 'required|string|max:100',
            'difficulty' => 'required|in:Easy,Medium,Hard',
        ]);

        $quiz = Quiz::create([
            'title' => $request->title,
            'description' => $request->description,
            'duration_minutes' => $request->duration_minutes,
            'teacher_id' => Auth::id(),
            'subject_id' => $request->subject_id,
            'topic' => $request->topic,
            'difficulty' => $request->difficulty,
        ]);

        return redirect()->route('quizzes.manage', $quiz->id)->with('success', 'Quiz created!');
    }
    
    public function edit($id)
    {
        $quiz = Quiz::where('teacher_id', Auth::id())->findOrFail($id);
        $subjects = Subject::all();
        return view('quizzes.edit', compact('quiz', 'subjects'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'duration_minutes' => 'required|integer|min:1',
            'topic' => 'required|string|max:100',
            'difficulty' => 'required|in:Easy,Medium,Hard',
        ]);

        $quiz = Quiz::where('teacher_id', Auth::id())->findOrFail($id);
        
        $quiz->update($request->all());

        return redirect()->route('quizzes.index')->with('success', 'Quiz updated successfully!');
    }

    public function destroy($id)
    {
        $quiz = Quiz::where('teacher_id', Auth::id())->findOrFail($id);
        $quiz->delete();
        return back()->with('success', 'Quiz deleted successfully.');
    }
    
    public function manage($id)
    {
        $quiz = Quiz::with('questions.options')->findOrFail($id);
        return view('quizzes.manage', compact('quiz'));
    }

    public function storeQuestion(Request $request, $quiz_id)
{
    $request->validate([
        'question_text' => 'required',
        'question_type' => 'required|in:single,multiple,text',
        'points' => 'integer|min:1',
    ]);

    $quiz = Quiz::findOrFail($quiz_id);

    // 1. Prepare Question Data
    // We pull the subject_id and difficulty from the Quiz automatically
    $questionData = [
        'question_text' => $request->question_text,
        'question_type' => $request->question_type,
        'points' => $request->points ?? 1,
        'subject_id' => $quiz->subject_id, 
        'difficulty' => $quiz->difficulty,
    ];

    if ($request->question_type === 'text') {
        $questionData['correct_answer_text'] = $request->text_answer;
    }

    // 2. Create the Question directly using the Model
    // We manually set quiz_id here to satisfy your current database requirement
    $questionData['quiz_id'] = $quiz->id; 
    $question = \App\Models\Question::create($questionData);

    // 3. 🟢 THE MOST IMPORTANT PART: Attach to the Pivot Table
    // This makes the question available for PvP shuffling!
    $quiz->questions()->attach($question->id);

    // 4. Create Options
    if ($request->question_type === 'text') {
        $question->options()->create([
            'option_text' => $request->text_answer, 
            'is_correct' => true
        ]);
    } else {
        if($request->options){
            foreach ($request->options as $key => $optionText) {
                if(trim($optionText) == '') continue;
                $isCorrect = false;
                if ($request->question_type === 'single') {
                    if ($request->correct_single == $key) $isCorrect = true;
                } elseif ($request->question_type === 'multiple') {
                    if (isset($request->correct_multiple) && in_array($key, $request->correct_multiple)) $isCorrect = true;
                }
                $question->options()->create([
                    'option_text' => $optionText, 
                    'is_correct' => $isCorrect
                ]);
            }
        }
    }

    return back()->with('success', 'Question added and linked to Arena successfully!');
}

    public function destroyQuestion($id)
    {
        \App\Models\Question::findOrFail($id)->delete();
        return back()->with('success', 'Question deleted.');
    }
}