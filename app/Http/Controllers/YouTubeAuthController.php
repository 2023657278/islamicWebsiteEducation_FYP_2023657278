<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class YouTubeAuthController extends Controller
{
    /**
     * 1. Send User to Google OAuth
     */
    public function redirect(Request $request)
    {
        // Save the context IDs to session before redirecting to Google
        Session::put('sync_group_id', $request->group_id);
        Session::put('sync_subject_id', $request->subject_id);
        Session::save();

        // 🟢 FIX: Let Socialite read the redirect URL automatically from your config/services.php
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
     * 2. Handle the Return payload from Google
     */
    public function callback()
    {
        try {
            // 🟢 FIX: Removed the broken ->redirectUrl() call from here too
            $googleUser = Socialite::driver('google')->user();
            
            $token = $googleUser->token ?? ($googleUser->accessTokenResponseBody['access_token'] ?? null);

            if (!$token) {
                throw new \Exception("Could not retrieve access token from Google response.");
            }

            // Save token for subsequent API calls
            Session::put('youtube_access_token', $token);

            // Pull saved context parameters out of session memory
            $groupId = Session::get('sync_group_id');
            $subjectId = Session::get('sync_subject_id');
            Session::save();

            // Redirect back to your master finder view
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