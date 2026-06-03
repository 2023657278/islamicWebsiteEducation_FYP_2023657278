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
        // Save state context to session to recover after authorization
        Session::put('sync_group_id', $request->group_id);
        Session::put('sync_subject_id', $request->subject_id);

        return Socialite::driver('google')
            ->scopes([
                'https://www.googleapis.com/auth/youtube.readonly',
                'openid',
                'profile',
                'email'
            ])
            ->redirect();
    }

    /**
     * 2. Handle the Return from Google
     */
    public function callback()
    {
        try {
            // 🟢 FIX: Remove ->redirectUrl() from this call chain
            $googleUser = Socialite::driver('google')->user();

            // 🟢 SAFELY ACCESS TOKEN: Handle properties cleanly
            $token = $googleUser->token ?? ($googleUser->accessTokenResponseBody['access_token'] ?? null);

            if (!$token) {
                throw new \Exception("Could not retrieve access token from Google response.");
            }

            // Store token for downstream API processing
            Session::put('youtube_access_token', $token);

            // Fetch structural data preserved prior to flight
            $groupId = Session::get('sync_group_id');
            $subjectId = Session::get('sync_subject_id');

            // Redirect smoothly back to the custom view route with parameters intact
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