<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WebhookSetupController;
use App\Http\Controllers\YouTubeAuthController;

/*
|--------------------------------------------------------------------------
| Web Routes (Main)
|--------------------------------------------------------------------------
*/

// 1. Public Routes
Route::get('/', function () { return view('auth.login'); });
Route::get('/laravel', function () { return view('welcome'); });
//Route::get('/homepage', function () { return view('homepage.welcome'); });

// 2. Authentication
Auth::routes();
Route::get('/home', function () {
    $user = Auth::user();

    // 1. If Super Admin -> Go to Admin Panel
    if ($user->role === 'admin') {
        return redirect()->route('adminreal.dashboard');
    }

    // 2. If Teacher -> Go to Teacher Management Panel
    if ($user->role === 'teacher') {
        return redirect()->route('admin.dashboard');
    }

    // 3. If Student -> Go to Student Homepage
    if ($user->role === 'student') {
        return redirect()->route('student.homepage');
    }

    // Fallback if role is missing or mismatched
    return redirect('/');
})->middleware('auth')->name('home');

// 3. Admin Tools (Webhook Setup)
Route::prefix('webhook')->group(function () {
    Route::get('setup-telegram', [WebhookSetupController::class, 'setupTelegramWebhook'])->name('webhook.setup');
    Route::get('check-telegram', [WebhookSetupController::class, 'checkTelegramWebhook'])->name('webhook.check');
    Route::get('delete-telegram', [WebhookSetupController::class, 'deleteTelegramWebhook'])->name('webhook.delete');
});

// 4. Public API / Auth Routes
//Route::get('/auth/youtube', [YouTubeAuthController::class, 'redirect'])->name('auth.youtube');
Route::get('/auth/youtube', [YouTubeAuthController::class, 'redirect'])->name('resources.sync.auth');
Route::get('/login/google/callback', [YouTubeAuthController::class, 'callback'])->name('login.google.callback');
//Route::get('/auth/youtube/callback', [YouTubeAuthController::class, 'callback']);

// =========================================================
//  LOAD SEPARATE ROUTE FILES
// =========================================================
require __DIR__.'/adminreal.php';
require __DIR__.'/teacher.php';
require __DIR__.'/student.php';
require __DIR__.'/shared.php';


// --- DIAGNOSTIC TOOL ---
Route::get('/debug-bot', function () {
    $results = [];

    // 1. Check if CURL exists in the Web Server
    if (function_exists('curl_init')) {
        $results[] = "✅ <b style='color:green'>CURL is Enabled</b>";
    } else {
        $results[] = "❌ <b style='color:red'>CURL is MISSING</b> (Enable php_curl in php.ini)";
    }

    // 2. Check Database Connection & Data
    try {
        $sCount = \App\Models\Subject::count();
        $results[] = "✅ Database Connected. Found <b>$sCount</b> subjects.";
        
        $qCount = \App\Models\Quiz::count();
        $results[] = "✅ Quizzes Table Accessible. Found <b>$qCount</b> total quizzes.";
        
        // Check specifically for Al-Quran (ID 1)
        $s1_quizzes = \App\Models\Quiz::where('subject_id', 1)->count();
        $results[] = "ℹ️ Subject 1 (Al-Quran) has <b>$s1_quizzes</b> quizzes.";
        
    } catch (\Exception $e) {
        $results[] = "❌ <b style='color:red'>Database Error:</b> " . $e->getMessage();
    }

    // 3. Check Telegram Connection
    $token = '8036575496:AAFtaYbG65gKDAFPV7BBcDNs9vmeFrB4nk0';
    try {
        $url = "https://api.telegram.org/bot{$token}/getMe";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $resp = curl_exec($ch);
        
        if ($resp === false) {
             $results[] = "❌ <b style='color:red'>Telegram Connect Failed:</b> " . curl_error($ch);
        } else {
            $json = json_decode($resp, true);
            if ($json && $json['ok']) {
                $results[] = "✅ Telegram API Connected. Bot Name: <b>" . $json['result']['username'] . "</b>";
            } else {
                $results[] = "❌ <b style='color:red'>Telegram API Error:</b> " . $resp;
            }
        }
        curl_close($ch);
    } catch (\Exception $e) {
        $results[] = "❌ Telegram Connection Error: " . $e->getMessage();
    }

    return implode('<br><br>', $results);
}

);