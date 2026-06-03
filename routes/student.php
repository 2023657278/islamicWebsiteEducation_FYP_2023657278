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
use App\Http\Controllers\RoomController;
use App\Http\Controllers\PvpController; // Added missing import
use App\Http\Controllers\RankingController;
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
    // 🟢 ADD THIS: Direct path mapping to the preview streaming method for students
    Route::get('/resources/{id}/preview', [\App\Http\Controllers\ResourcesController::class, 'preview'])->name('student.resources.preview');

    // 8. STUDENT PROGRESS & ANALYTICS
    Route::get('/student/progress', [StudentProgressController::class, 'index'])->name('student.progress.index');

    // 12. TEXTBOOK READER / PRIVATE NOTES ACCESS
    Route::get('/student/textbooks/{id}/read', [StudentResourceController::class, 'read'])->name('student.textbooks.read');
    Route::post('/student/textbooks/progress', [StudentResourceController::class, 'saveProgress'])->name('student.textbooks.save_progress');
    
    // Student streaming paths bypassing teacher middleware restrictions
    Route::get('/resources/{id}/preview', [\App\Http\Controllers\ResourcesController::class, 'preview'])->name('student.resources.preview');
    Route::get('/resources/{id}/download', [\App\Http\Controllers\ResourcesController::class, 'download'])->name('student.resources.download');

    // =========================================================
// 🎓 HIERARCHICAL QUIZ CENTER (CLEANED VERSION)
// =========================================================
Route::group(['prefix' => 'student/quizzes', 'as' => 'student.quizzes.'], function () {
    
    // 1. Core Navigation
    Route::get('/', [StudentQuizController::class, 'index'])->name('index');
    Route::get('/subject/{subject_id}/mode', [StudentQuizController::class, 'selectMode'])->name('select_mode');
    Route::get('/subject/{subject_id}', [StudentQuizController::class, 'difficulties'])->name('difficulties');
    Route::get('/subject/{subject_id}/level/{difficulty}', [StudentQuizController::class, 'topicsByDifficulty'])->name('topics_diff');
    Route::get('/subject/{subject_id}/level/{difficulty}/topic/{topic}', [StudentQuizController::class, 'listByTopic'])->name('list');

    // 2. PvP Creation & Lobby
    // URL: /student/quizzes/create_pvp/...
    Route::get('/create_pvp/{subject_id}/{difficulty}', [RoomController::class, 'createFromDifficulty'])->name('create_pvp');
    Route::get('/subject/{subject_id}/browse', [RoomController::class, 'browse'])->name('browse');
    Route::get('/lobby/{code}', [RoomController::class, 'lobby'])->name('lobby');
    Route::get('/lobby/{code}/participants', [RoomController::class, 'getParticipants'])->name('participants');
    Route::post('/lobby/{code}/start', [RoomController::class, 'start'])->name('lobby.start');
    Route::post('/lobby/{code}/dismiss', [RoomController::class, 'dismiss'])->name('lobby.dismiss');
    Route::get('/lobby/{code}/leave', [RoomController::class, 'leaveLobby'])->name('lobby.leave');
    Route::post('/join', [RoomController::class, 'join'])->name('join');

    // 3. Battle Arena (Using RoomController for the View, PvpController for Logic)
    Route::get('/pvp/{code}', [RoomController::class, 'battleArena'])->name('pvp.arena');
    Route::get('/pvp/{code}/status', [PvpController::class, 'getStatus'])->name('pvp.status');
    Route::post('/pvp/{code}/strike', [PvpController::class, 'submitStrike'])->name('pvp.strike');
    Route::post('/pvp/{code}/next', [PvpController::class, 'nextRound'])->name('pvp.next');
    Route::get('/pvp/{code}/surrender', [RoomController::class, 'leaveLobby'])->name('pvp.surrender');
    Route::post('/pvp/{code}/power', [PvpController::class, 'usePower'])->name('pvp.power');
    Route::get('/pvp/{code}/results', [PvpController::class, 'results'])->name('pvp.results');

    // 4. Solo Quiz Logic
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

    // 13. RANKING
    Route::get('/ranking', [RankingController::class, 'index'])->name('student.ranking');
});