<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\ResourcesController;
use App\Http\Controllers\RepositoryController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\TeacherFlashcardController;
use App\Http\Controllers\TeacherResultController;
use App\Http\Controllers\RoomController;

// =========================================================
// TEACHER & ADMIN DASHBOARD
// =========================================================
Route::middleware(['auth', 'role:teacher,admin'])->group(function () {
    
    Route::get('/adminhome', [AdminController::class, 'index'])->name('admin.dashboard');

    // CRUD Resources
    Route::resource('students', StudentController::class);
    Route::resource('teachers', TeacherController::class);
    Route::resource('administrators', AdministratorController::class);
    Route::resource('subjects', SubjectController::class);
    Route::resource('groups', GroupController::class);
    Route::resource('timetables', TimetableController::class);
    Route::resource('quizzes', QuizController::class);

    // --- RESOURCE MANAGEMENT ---
    Route::get('/resources', [ResourcesController::class, 'index'])->name('resources.index');
    Route::post('/resources', [ResourcesController::class, 'store'])->name('resources.store');
    Route::delete('/resources/{id}', [ResourcesController::class, 'destroy'])->name('resources.destroy');

    // YouTube OAuth Sync Routes
    Route::get('/resources/sync/auth', [ResourcesController::class, 'redirectToYouTube'])->name('resources.sync.auth');
    Route::get('/auth/youtube/callback', [ResourcesController::class, 'handleYouTubeCallback'])->name('resources.sync.callback');
    Route::post('/resources/sync/store', [ResourcesController::class, 'storeSelectedVideos'])->name('resources.sync.store_selected');
    Route::get('/resources/youtube/search', [App\Http\Controllers\ResourcesController::class, 'youtubeSearch'])->name('resources.youtube.search');
    Route::get('/youtube/fetch-data', [ResourcesController::class, 'fetchYoutubeData'])->name('youtube.fetch');

    // Notes Management (Repository)
    Route::prefix('manage')->group(function () {
        Route::get('notes', [RepositoryController::class, 'indexNotes'])->name('repo.notes.index');
        Route::get('notes/create', [RepositoryController::class, 'createNote'])->name('repo.notes.create');
        Route::post('notes', [RepositoryController::class, 'storeNote'])->name('repo.notes.store');
        Route::delete('notes/{id}', [RepositoryController::class, 'destroyNote'])->name('repo.notes.destroy');
    });

    // Quiz Questions
    Route::get('/quizzes/{id}/manage', [QuizController::class, 'manage'])->name('quizzes.manage');
    Route::post('/quizzes/{id}/questions', [QuizController::class, 'storeQuestion'])->name('questions.store');
    Route::delete('/questions/{id}', [QuizController::class, 'destroyQuestion'])->name('questions.destroy');
    Route::get('/teacher/quizzes/{quiz_id}/create-room', [RoomController::class, 'create'])->name('teacher.quizzes.create_room');


    // Flashcards (Teacher)
    Route::get('flashcards', [TeacherFlashcardController::class, 'index'])->name('flashcards.index');
    Route::post('flashcards', [TeacherFlashcardController::class, 'store'])->name('flashcards.store');
    Route::delete('flashcards/{id}', [TeacherFlashcardController::class, 'destroy'])->name('flashcards.destroy');
    Route::post('flashcards/import', [TeacherFlashcardController::class, 'importFromQuiz'])->name('flashcards.import');

    // Student Results
    Route::get('results', [TeacherResultController::class, 'index'])->name('results.index');
    Route::delete('results/{id}', [TeacherResultController::class, 'destroy'])->name('results.destroy');
});