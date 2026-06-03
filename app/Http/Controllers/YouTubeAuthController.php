<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class YouTubeAuthController extends Controller
{
    /**
     * 1. Send User to Google
     */
    public function redirect(Request $request)
    {
        // Save the class and subject IDs to session so we don't lose them during the OAuth flight
        Session::put('sync_group_id', $request->group_id);
        Session::put('sync_subject_id', $request->subject_id);

        return Socialite::driver('google')
            ->scopes([
                'https://www.googleapis.com/auth/youtube.readonly',
                'openid',
                'profile',
                'email'
            ])
            ->with([
                'access_type' => 'offline', 
                'prompt' => 'consent'
            ])
            ->redirect();
    }

    /**
     * 2. Handle the Return from Google
     */
    public function callback()
    {
        try {
            // Get the authorized user details back from Google Socialite
            $googleUser = Socialite::driver('google')->user();

            // Extract token safely
            $token = $googleUser->token ?? ($googleUser->accessTokenResponseBody['access_token'] ?? null);

            if (!$token) {
                throw new \Exception("Could not retrieve access token from Google response.");
            }

            // 🟢 CRITICAL STEP 1: Save the token into the exact session key your resources system expects
            Session::put('youtube_access_token', $token);

            // 🟢 CRITICAL STEP 2: Pull out the saved context IDs from earlier
            $groupId = Session::get('sync_group_id');
            $subjectId = Session::get('sync_subject_id');

            // 🟢 CRITICAL STEP 3: Redirect back to your YouTube Lesson Finder view instead of index!
            // This forces the page to reload the search window with the "My Channel" tab ready.
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
}