<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ResourcesController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;

// =========================================================
//  SHARED ROUTES (Accessible by both Teachers & Students)
// =========================================================
Route::middleware(['auth'])->group(function () {
    
    // Downloads & Preview
    Route::get('/resources/download/{id}', [ResourcesController::class, 'download'])->name('resources.download');
    Route::get('/resources/preview/{id}', [ResourcesController::class, 'preview'])->name('resources.preview');
    
    // Messaging
    Route::get('messages/{type?}/{id?}', [MessageController::class, 'index'])->name('messages.index');
    Route::post('messages', [MessageController::class, 'store'])->name('messages.store');

    // General Profile Settings
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
        Route::delete('/delete-image', [ProfileController::class, 'deleteImage'])->name('deleteImage');
    });
});