<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class YouTubeAuthController extends Controller
{
    private const CALLBACK_URL = 'https://islamic-lms.online/login/google/callback';

    /**
     * 1. Send User to Google
     */
    public function redirect(Request $request)
    {
        Session::put('sync_group_id', $request->group_id);
        Session::put('sync_subject_id', $request->subject_id);
        Session::save(); 

        return Socialite::driver('google')
            ->scopes([
                'https://www.googleapis.com/auth/youtube.readonly',
                'openid',
                'profile',
                'email'
            ])
            ->with([
                'access_type' => 'offline', 
                // 🟢 FORCES the account picker menu to pop up every single time!
                'prompt'      => 'select_account consent' 
            ])
            ->redirect();
    }

    /**
     * 2. Handle the Return payload from Google
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            $token = $googleUser->token ?? ($googleUser->accessTokenResponseBody['access_token'] ?? null);

            if (!$token) {
                throw new \Exception("Could not retrieve access token from Google.");
            }

            // Save credentials where your AJAX handler expects it
            Session::put('youtube_access_token', $token);

            $groupId = Session::get('sync_group_id');
            $subjectId = Session::get('sync_subject_id');
            Session::save();

            return redirect()->route('resources.youtube.search', [
                'group_id' => $groupId,
                'subject_id' => $subjectId,
                'type' => 'mine'
            ]);
            
        } catch (\Exception $e) {
            return redirect()->route('resources.index')
                ->with('error', 'Authentication failed: ' . $e->getMessage());
        }
    }

    /**
     * 3. 🟢 NEW: Explicitly Disconnect/Logout YouTube Google Session
     */
   public function disconnect()
    {
        // 1. Completely destroy local credentials from your system session memory
        Session::forget(['youtube_access_token', 'sync_group_id', 'sync_subject_id']);
        Session::save();

        // 2. 🟢 THE SMART BYPASS: Send them back to your library index page with an explicit instruction.
        // This avoids Google's broken 400 error page entirely!
        return redirect()->route('resources.index')->with('success', 'Session disconnected! You can now link a completely different YouTube channel.');
    }
}