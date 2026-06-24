<?php

namespace App\Http\Controllers;

use Smalot\PdfParser\Parser;
use App\Models\Quiz;
use App\Models\Subject;
use App\Models\Result;
use App\Models\User;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\AnalyticsService;


class QuizController extends Controller
{
    // =========================================================
    // PART A: STUDENT FUNCTIONS (Solo Quiz Taking & Analytics)
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
    // PART B: TEACHER FUNCTIONS (Shared Global Pool Management)
    // =========================================================

    public function index()
    {
        $quizzes = Quiz::where('topic', '!=', 'PVP_ARENA_BATTLE')
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

        return redirect()->route('quizzes.manage', $quiz->id)->with('success', 'Quiz created successfully!');
    }
    
    public function edit($id)
    {
        $quiz = Quiz::findOrFail($id);
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

        $quiz = Quiz::findOrFail($id);
        $quiz->update($request->all());

        return redirect()->route('quizzes.index')->with('success', 'Quiz updated successfully!');
    }

    public function destroy($id)
    {
        $quiz = Quiz::findOrFail($id);
        $quiz->delete();
        
        return back()->with('success', 'Quiz deleted successfully from the shared library.');
    }
    
    public function manage($id)
    {
        $quiz = Quiz::with('questions.options')->findOrFail($id);
        
        // 🟢 UPGRADED ON-PAGE DETECTOR ENGINE
        $editingQuestion = null;
        if (request()->has('edit_question_id')) {
            $editingQuestion = Question::with('options')->find(request('edit_question_id'));
        }

        return view('quizzes.manage', compact('quiz', 'editingQuestion'));
    }

    public function storeQuestion(Request $request, $quiz_id)
    {
        $request->validate([
            'question_text' => 'required',
            'question_type' => 'required|in:single,multiple,text',
            'points' => 'integer|min:1',
        ]);

        $quiz = Quiz::findOrFail($quiz_id);
        $cleanQuestionText = str_replace('$', '', $request->question_text);

        // 👇 MODIFY THIS BLOCK BELOW 👇
        $questionData = [
            'question_text' => $cleanQuestionText,
            'question_type' => $request->question_type,
            'points' => $request->points ?? 1,
            'quiz_id' => $quiz->id, 
            
            // 🏁 PERMANENT FIX: Force the question to always take the subject 
            // and difficulty directly from the quiz it is being added to!
            'subject_id' => $quiz->subject_id, 
            'difficulty' => $quiz->difficulty,
        ];
        // 👆 MODIFY THIS BLOCK ABOVE 👆

        if ($request->question_type === 'text') {
            $questionData['correct_answer_text'] = $request->text_answer;
        }

        $question = Question::create($questionData);
        $quiz->questions()->attach($question->id);

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

        return redirect()->route('quizzes.manage', $quiz->id)->with('success', 'Question added successfully to this quiz!');
    }

    // =================================================================
    // 🟢 PART C: ON-SCREEN DISPATCH UPDATE PROCESSOR ENGINE (PUT)
    // =================================================================
    public function updateQuestion(Request $request, $id)
    {
        $request->validate([
            'question_text' => 'required',
            'question_type' => 'required|in:single,multiple,text',
            'points' => 'integer|min:1',
        ]);

        $question = Question::findOrFail($id);
        $cleanQuestionText = str_replace('$', '', $request->question_text);

        $questionData = [
            'question_text' => $cleanQuestionText,
            'question_type' => $request->question_type,
            'points' => $request->points ?? 1,
        ];

        if ($request->question_type === 'text') {
            $questionData['correct_answer_text'] = $request->text_answer;
        } else {
            $questionData['correct_answer_text'] = null;
        }

        $question->update($questionData);
        
        // Securely drop old option entities rows inside your tables
        $question->options()->delete();

        if ($request->question_type === 'text') {
            $question->options()->create([
                'option_text' => $request->text_answer, 
                'is_correct' => true
            ]);
        } else {
            if ($request->options) {
                foreach ($request->options as $key => $optionText) {
                    if (trim($optionText) == '') continue;
                    
                    $isCorrect = false;
                    if ($request->question_type === 'single') {
                        if ($request->correct_single == $key) $isCorrect = true;
                    } elseif ($request->question_type === 'multiple') {
                        if (isset($request->correct_multiple) && in_array($key, $request->correct_multiple)) {
                            $isCorrect = true;
                        }
                    }

                    $question->options()->create([
                        'option_text' => $optionText, 
                        'is_correct' => $isCorrect
                    ]);
                }
            }
        }

        // Pull the pivot parent relation cleanly
        $parentQuiz = $question->quizzes()->first();
        $quizId = $parentQuiz ? $parentQuiz->id : $question->quiz_id;

        return redirect()->route('quizzes.manage', $quizId)->with('success', 'Question updated successfully!');
    }

    public function destroyQuestion($id)
    {
        $question = Question::findOrFail($id);
        $question->delete();
        
        return back()->with('success', 'Question deleted successfully.');
    }

    // question banks
    public function uploadQuestionsBank(Request $request)
{
    $request->validate([
        'pdf_file' => 'required|mimes:pdf|max:20480', 
        'subject_id' => 'required|integer'
    ]);

    // 🟢 1. AUTOMATIC DATABASE SAFEGUARD: 
    // Find or automatically create a hidden placeholder quiz to hold global bank questions
    $bankQuiz = \App\Models\Quiz::firstOrCreate(
        ['title' => 'Al-Falah Global Question Bank Reservoir'],
        [
            'description' => 'System-generated container for global pool extraction.',
            'duration_minutes' => 60,
            'teacher_id' => auth()->id() ?? 1, // Fallback to first user ID if unauthenticated
            'subject_id' => $request->subject_id,
            'topic' => 'GLOBAL_BANK',
            'difficulty' => 'Easy'
        ]
    );

    // 2. Store the uploaded file temporarily inside local framework directories
    $file = $request->file('pdf_file');
    $rawText = (new \Smalot\PdfParser\Parser())->parseFile($file->getRealPath())->getText();

    // 3. Break down text into rows
    $lines = explode("\n", $rawText);
    $currentQuestion = null;
    $importCount = 0;

    foreach ($lines as $line) {
        $line = trim($line);

        if (empty($line) || str_contains($line, 'MODUL AL-FALAH') || str_contains($line, 'BUKU TEKS')) {
            continue;
        }

        // RegEx Check: Detect lines starting with numbers (e.g., "1. Apakah maksud Mad Silah?")
        if (preg_match('/^(\d+)\.\s*(.*)/', $line, $matches)) {
            $questionString = trim($matches[2]);

            if (empty($questionString)) continue;

            // 🟢 4. INGEST QUESTION WITH VALID QUIZ PARENT
            $currentQuestion = Question::create([
                'question_text' => $questionString,
                'question_type' => 'text',
                'points'        => 2,
                'subject_id'    => $request->subject_id,
                'difficulty'    => 'Easy',
                
                // Maps to our safe placeholder quiz row to satisfy MySQL integrity rules
                'quiz_id'       => $bankQuiz->id, 
            ]);

            // Also link it via the belongsToMany pivot table loop for full architectural support
            $bankQuiz->questions()->attach($currentQuestion->id);

            $importCount++;
        } 
        // Relational Binding Check: Save trailing row lines as the correct option answer text
        elseif ($currentQuestion && !empty($line) && !str_contains($line, 'Markah') && !str_contains($line, 'BIL.')) {
            $currentQuestion->options()->create([
                'option_text' => $line,
                'is_correct' => true
            ]);

            $currentQuestion->update(['correct_answer_text' => $line]);
            $currentQuestion = null; 
        }
    }

    return back()->with('success', "Al-Falah Bank Ingested Successfully! Imported {$importCount} questions.");
}

// =================================================================
// 🟢 PART D: KEYWORD SEARCH & AUTO-FILL QUESTION BANK DISPATCHER
// =================================================================

public function searchBank(Request $request)
{
    $keyword = $request->query('keyword');

    if (empty($keyword) || strlen($keyword) < 2) {
        return response()->json([]);
    }

    // Search for matching rows in your 702 global questions dataset
    $questions = Question::where('question_text', 'LIKE', "%{$keyword}%")
        ->with(['options' => function($query) {
            $query->where('is_correct', true); // Only grab the correct answer string from the bank
        }])
        ->limit(10)
        ->get();

    return response()->json($questions);
}

public function attachBankQuestion(Request $request, $quiz_id)
{
    $request->validate([
        'bank_question_id' => 'required|exists:questions,id',
        'points'           => 'required|integer|min:1',
        'wrong_options'    => 'required|array|min:1',
        'wrong_options.*'  => 'required|string'
    ]);

    $quiz = Quiz::findOrFail($quiz_id);
    $bankQuestion = Question::findOrFail($request->bank_question_id);

    // Clone the question row specifically for this quiz to avoid overwriting the master reservoir
    $newQuestion = Question::create([
        'question_text'       => $bankQuestion->question_text,
        'question_type'       => 'single', // Converted to single choice multiple choice form
        'points'              => $request->points,
        'quiz_id'             => $quiz->id,
        'subject_id'          => $quiz->subject_id,
        'difficulty'          => $quiz->difficulty,
        'correct_answer_text' => $bankQuestion->correct_answer_text
    ]);

    // Attach to the current quiz pivot table
    $quiz->questions()->attach($newQuestion->id);

    // 1. Re-add the master correct answer text choice for the student interface
    $newQuestion->options()->create([
        'option_text' => $bankQuestion->correct_answer_text,
        'is_correct'  => true
    ]);

    // 2. Loop and attach the custom wrong options submitted by the teacher
    foreach ($request->wrong_options as $wrongText) {
        if (trim($wrongText) == '') continue;
        
        $newQuestion->options()->create([
            'option_text' => $wrongText,
            'is_correct'  => false // Strictly marked as wrong distractor rows
        ]);
    }

    return redirect()->route('quizzes.manage', $quiz->id)->with('success', 'Question linked from Al-Falah Bank and distractors added successfully!');
}

}