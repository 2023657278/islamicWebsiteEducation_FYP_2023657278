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

        $bankQuiz = \App\Models\Quiz::firstOrCreate(
            ['title' => 'Al-Falah Global Question Bank Reservoir'],
            [
                'description' => 'System-generated container for global pool extraction.',
                'duration_minutes' => 60,
                'teacher_id' => auth()->id() ?? 1, 
                'subject_id' => $request->subject_id,
                'topic' => 'GLOBAL_BANK',
                'difficulty' => 'Easy'
            ]
        );

        $file = $request->file('pdf_file');
        $rawText = (new \Smalot\PdfParser\Parser())->parseFile($file->getRealPath())->getText();

        $lines = explode("\n", $rawText);
        
        $importCount = 0;
        $currentQuestionText = "";
        $currentAnswerLines = [];

        foreach ($lines as $line) {
            $line = trim($line);

            if (empty($line) || str_contains($line, 'MODUL AL-FALAH') || str_contains($line, 'BUKU TEKS') || str_contains($line, 'Markah') || str_contains($line, 'BIL.')) {
                continue;
            }

            if (preg_match('/^\d+/', $line)) {
                if (!empty($currentQuestionText) && !empty($currentAnswerLines)) {
                    $this->saveBufferedQuestion($bankQuiz->id, $request->subject_id, $currentQuestionText, $currentAnswerLines);
                    $importCount++;
                }

                $cleaned = preg_replace('/^\d+\s*[\.\)]?\s*/', '', $line);
                $currentQuestionText = $cleaned;
                $currentAnswerLines = [];
            } else {
                if (str_contains($line, 'Umat') || preg_match('/^\d/', $line) || count($currentAnswerLines) > 0 || strlen($line) > 40) {
                    $currentAnswerLines[] = $line;
                } else {
                    $currentQuestionText .= " " . $line;
                }
            }
        }

        if (!empty($currentQuestionText) && !empty($currentAnswerLines)) {
            $this->saveBufferedQuestion($bankQuiz->id, $request->subject_id, $currentQuestionText, $currentAnswerLines);
            $importCount++;
        }

        return back()->with('success', "Al-Falah Bank Ingested Successfully! Line-by-line matching pulled {$importCount} questions.");
    }

    /**
     * 🟢 THE MISSING METHOD - PASTE THIS RIGHT BELOW THE UPLOAD FUNCTION
     */
   /**
     * Helper function to structure and save the buffered text blocks cleanly
     */
   private function saveBufferedQuestion($quizId, $subjectId, $questionText, $answerLines)
    {
        $combinedAnswerText = implode("\n", $answerLines);

        try {
            // 🟢 UPGRADED SCHEMA INSTRUCTION:
            // Safely modifies columns to be TEXT and allows subject_id to be NULL
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `options` MODIFY COLUMN `option_text` TEXT NULL");
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `questions` MODIFY COLUMN `question_text` TEXT NULL");
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `questions` MODIFY COLUMN `correct_answer_text` TEXT NULL");
            
            // Forces subject_id to be nullable so it doesn't belong to any specific subject
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `questions` MODIFY COLUMN `subject_id` INT NULL");
        } catch (\Exception $e) {
            // Fails silently if already converted or restricted
        }

        $question = \App\Models\Question::create([
            'question_text'       => trim($questionText),
            'question_type'       => 'text', 
            'points'              => 2,
            
            // 🟢 CHANGED TO NULL: This question no longer consists of or is locked to any subject!
            'subject_id'          => null, 
            
            'difficulty'          => 'Easy',
            'quiz_id'             => $quizId, 
            'correct_answer_text' => $combinedAnswerText
        ]);

        $question->quizzes()->attach($quizId);

        $question->options()->create([
            'option_text' => $combinedAnswerText,
            'is_correct'  => true
        ]);
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

    // Search for matching rows in your global questions dataset
    $questions = Question::where('question_text', 'LIKE', "%{$keyword}%")
        ->with(['options' => function($query) {
            $query->where('is_correct', true);
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
    $bankQuestion = Question::with('options')->findOrFail($request->bank_question_id);

    // 🏁 FIX: If correct_answer_text is null, look inside the options table relation for the answer
    $correctAnswerText = $bankQuestion->correct_answer_text;
    if (empty($correctAnswerText)) {
        $correctOption = $bankQuestion->options->where('is_correct', true)->first();
        $correctAnswerText = $correctOption ? $correctOption->option_text : 'Jawapan Betul';
    }

    // Clone the question row specifically for this quiz
    $newQuestion = Question::create([
        'question_text'       => $bankQuestion->question_text,
        'question_type'       => 'single', 
        'points'              => $request->points,
        'quiz_id'             => $quiz->id,
        'subject_id'          => $quiz->subject_id,
        'difficulty'          => $quiz->difficulty,
        'correct_answer_text' => $correctAnswerText
    ]);

    // Attach to the current quiz pivot table
    $quiz->questions()->attach($newQuestion->id);

    // 1. Re-add the verified correct answer text choice safely
    $newQuestion->options()->create([
        'option_text' => $correctAnswerText,
        'is_correct'  => true
    ]);

    // 2. Loop and attach the custom wrong options submitted by the teacher
    foreach ($request->wrong_options as $wrongText) {
        if (trim($wrongText) == '') continue;
        
        $newQuestion->options()->create([
            'option_text' => $wrongText,
            'is_correct'  => false 
        ]);
    }

    return redirect()->route('quizzes.manage', $quiz->id)->with('success', 'Question linked from Al-Falah Bank successfully!');
}

}