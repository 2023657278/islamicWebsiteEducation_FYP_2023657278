<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\StudentProfileController;
use App\Http\Controllers\StudentTimetableController;
use App\Http\Controllers\StudentMessageController;
use App\Http\Controllers\StudentResourceController;
use App\Http\Controllers\StudentQuizController;
use App\Http\Controllers\StudentFlashcardController;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\StudentProgressController; // Added missing import
use App\Models\Subject;

// =========================================================
//  STUDENT DASHBOARD ROUTES
// =========================================================
Route::middleware(['auth', 'role:student'])->group(function () {
    
    // 1. Homepage
    Route::get('/homepage', function () {
        $subjects = Subject::all();
        return view('homepage.welcome', compact('subjects')); 
    })->name('student.homepage');

    // 2. DASHBOARD
    Route::get('/student/dashboard', [StudentDashboardController::class, 'index'])->name('student.dashboard');

    // 3. Quiz Taking (Legacy/Direct Links if needed)
    // Note: The main quiz flow is now under 'student/quizzes' below
    Route::get('/quizzes/{id}/take', [QuizController::class, 'show'])->name('quizzes.take');
    Route::post('/submit-quiz', [QuizController::class, 'submit'])->name('quiz.submit');

    // 4. PROFILE
    Route::get('/student/profile', [StudentProfileController::class, 'show'])->name('student.profile.show');
    Route::get('/student/profile/edit', [StudentProfileController::class, 'edit'])->name('student.profile.edit');
    Route::put('/student/profile/update', [StudentProfileController::class, 'update'])->name('student.profile.update');
    Route::delete('/student/profile/delete-image', [StudentProfileController::class, 'deleteImage'])->name('student.profile.deleteImage');

    // 5. TIMETABLE
    Route::get('/users/timetable', [StudentTimetableController::class, 'index'])->name('student.timetable.view');

    // 6. MESSAGES
    Route::get('/student/messages', [StudentMessageController::class, 'index'])->name('student.messages.index');
    Route::get('/student/messages/{teacherId}', [StudentMessageController::class, 'show'])->name('student.messages.show');
    Route::post('/student/messages/{teacherId}', [StudentMessageController::class, 'store'])->name('student.messages.store');

    // 7. LEARNING RESOURCES
    Route::get('/student/resources', [StudentResourceController::class, 'index'])->name('student.resources.index');
    Route::get('/student/resources/{teacherId}', [StudentResourceController::class, 'show'])->name('student.resources.show');
    Route::get('/student/textbooks', [StudentResourceController::class, 'textbooks'])->name('student.textbooks.index');

    // 8. STUDENT PROGRESS & ANALYTICS
    Route::get('/student/progress', [StudentProgressController::class, 'index'])->name('student.progress.index');

    // =========================================================
    // 🎓 HIERARCHICAL QUIZ CENTER (RE-STRUCTURED FOR PROGRESSION)
    // =========================================================
    Route::group(['prefix' => 'student/quizzes', 'as' => 'student.quizzes.'], function () {
        
        // Level 1: Subject Selection
        Route::get('/', [StudentQuizController::class, 'index'])->name('index');

        // Level 2: Difficulty Selection (Easy, Medium, Hard)
        // This replaces the old 'topics' route
        Route::get('/subject/{subject_id}', [StudentQuizController::class, 'difficulties'])->name('difficulties');

        // Level 3: Topic Selection (Filtered by the chosen difficulty)
        Route::get('/subject/{subject_id}/level/{difficulty}', [StudentQuizController::class, 'topicsByDifficulty'])->name('topics_diff');

        // Level 4: Final Quiz List (Filtered by Difficulty AND Topic)
        Route::get('/subject/{subject_id}/level/{difficulty}/topic/{topic}', [StudentQuizController::class, 'listByTopic'])->name('list');

        // Level 5: Take Quiz & Submit (Unchanged)
        Route::get('/{id}/take', [StudentQuizController::class, 'show'])->name('take');
        Route::post('/{id}/submit', [StudentQuizController::class, 'submit'])->name('submit');
    });

    // 10. FLASHCARDS
    Route::get('/student/flashcards', [StudentFlashcardController::class, 'index'])->name('student.flashcards.index');
    Route::get('/student/flashcards/{subjectId}/study', [StudentFlashcardController::class, 'study'])->name('student.flashcards.study');
    Route::post('/student/flashcards/update', [StudentFlashcardController::class, 'updateLog'])->name('student.flashcards.update');
    Route::get('/student/flashcards/{subjectId}/manual', [StudentFlashcardController::class, 'manual'])->name('student.flashcards.manual');

    // 11. TELEGRAM INTEGRATION
    Route::get('/student/telegram', [TelegramController::class, 'connect'])->name('telegram.connect');
    Route::post('/student/telegram/verify', [TelegramController::class, 'verify'])->name('telegram.verify');
    Route::post('/student/telegram/unlink', [TelegramController::class, 'unlink'])->name('telegram.unlink');

    // 12. TEXTBOOK READER
    Route::get('/student/textbooks/{id}/read', [StudentResourceController::class, 'read'])->name('student.textbooks.read');
    Route::post('/student/textbooks/progress', [StudentResourceController::class, 'saveProgress'])->name('student.textbooks.save_progress');
});