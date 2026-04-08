<?php

namespace App\Http\Controllers;

use App\Models\Flashcard;
use App\Models\Quiz;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherFlashcardController extends Controller
{
    public function index()
    {
        $flashcards = Flashcard::where('teacher_id', Auth::id())->latest()->get();
        // Load Quizzes WITH Subject for filtering
        $quizzes = Quiz::where('teacher_id', Auth::id())->get(); 
        $subjects = Subject::all();

        return view('flashcards.index', compact('flashcards', 'quizzes', 'subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required',
            'answer' => 'required',
            'subject_id' => 'required',
            'topic' => 'required'
        ]);

        Flashcard::create([
            'teacher_id' => Auth::id(),
            'subject_id' => $request->subject_id,
            'topic' => $request->topic,
            'question' => $request->question,
            'answer' => $request->answer
        ]);

        return back()->with('success', 'Flashcard added manually!');
    }

    // ====================================================================
    // 3. AUTO-IMPORT (CUSTOMIZED FOR YOUR 'OPTIONS' TABLE STRUCTURE)
    // ====================================================================
    public function importFromQuiz(Request $request)
    {
        $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'subject_id' => 'required'
        ]);

        // Eager load questions AND their options to access the answer text
        $quiz = Quiz::with('questions.options')->findOrFail($request->quiz_id);
        $targetSubjectId = $request->subject_id; 

        if($quiz->questions->isEmpty()) {
            return back()->with('error', 'This quiz has no questions.');
        }

        $count = 0;
        foreach($quiz->questions as $q) {
            
            // 1. Prevent Duplicates
            $exists = Flashcard::where('quiz_id', $quiz->id)
                               ->where('question', $q->question_text)
                               ->exists();
            
            if(!$exists) {
                
                // Initialize default
                $finalAnswerText = "No Answer Found"; 

                // 2. LOGIC: Find the correct option(s) from the related table
                // We filter the 'options' collection for ones marked is_correct
                $correctOptions = $q->options->where('is_correct', true);

                if ($correctOptions->count() > 0) {
                    // Get the 'option_text' of all correct answers
                    $answersArray = $correctOptions->pluck('option_text')->toArray();
                    
                    // Join them with commas (handles Single, Multiple, and Text types automatically)
                    $finalAnswerText = implode(', ', $answersArray);
                }

                // 3. Create Flashcard
                Flashcard::create([
                    'teacher_id' => Auth::id(),
                    'quiz_id' => $quiz->id,
                    'subject_id' => $targetSubjectId, 
                    'topic' => $quiz->title, 
                    'question' => $q->question_text ?? 'No Question', 
                    'answer' => $finalAnswerText 
                ]);
                $count++;
            }
        }

        return back()->with('success', "Imported {$count} cards from '{$quiz->title}'!");
    }

    public function destroy($id)
    {
        $flashcard = Flashcard::findOrFail($id);
        $flashcard->delete();
        return back()->with('success', 'Flashcard deleted.');
    }
}