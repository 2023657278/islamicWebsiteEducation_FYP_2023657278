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
     * 1. Send User to Google OAuth
     */
    public function redirect(Request $request)
    {
        // Explicitly capture values right before navigating away
        Session::put('sync_group_id', $request->group_id);
        Session::put('sync_subject_id', $request->subject_id);
        Session::put('sync_type', 'mine'); // Lock step targeting custom upload tab

        // Force save changes to handle immediate redirect cleanly
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
            $googleUser = Socialite::driver('google')->user();
            $token = $googleUser->token ?? ($googleUser->accessTokenResponseBody['access_token'] ?? null);

            if (!$token) {
                throw new \Exception("Could not retrieve access token from Google.");
            }

            // Save credentials natively where your views expect them
            Session::put('youtube_access_token', $token);

            // Pull stored tracking variables out of state memory
            $groupId = Session::get('sync_group_id');
            $subjectId = Session::get('sync_subject_id');
            $type = Session::get('sync_type', 'mine');

            // Force save session adjustments manually 
            Session::save();

            // 🟢 REDIRECT FIX: Explicitly append properties back into the URL parameters 
            // so ResourcesController receives them securely regardless of session engine drops
            return redirect()->route('resources.youtube.search', [
                'group_id' => $groupId,
                'subject_id' => $subjectId,
                'type' => $type
            ]);
            
        } catch (\Exception $e) {
            // If anything structural breaks, it gracefully defaults to index with notification
            return redirect()->route('resources.index')
                ->with('error', 'Authentication failed: ' . $e->getMessage());
        }
    }
}