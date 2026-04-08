<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TelegramController;

// Existing API routes...

// ADD THIS LINE:
Route::post('/telegram/webhook', [TelegramController::class, 'handle']);