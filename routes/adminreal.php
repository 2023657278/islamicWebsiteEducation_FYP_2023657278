<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminRealController;

/*
|--------------------------------------------------------------------------
| Super Admin (Real Dark Mode) Pipeline
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'adminreal'])->prefix('adminreal')->name('adminreal.')->group(function () {
    
    // Main Dark Mode Dashboard Route targeting Option 2's Controller
    Route::get('/dashboard', [AdminRealController::class, 'index'])->name('dashboard');

    // Route for processing the two-tier verification timetable reset
    Route::post('/timetables/reset-all-records', [App\Http\Controllers\TimetableController::class, 'resetAll'])->name('timetables.resetAll')->middleware('auth');
    
});