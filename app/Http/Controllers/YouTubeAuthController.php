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
        $groupId = $request->group_id;
        $subjectId = $request->subject_id;

        // Explicitly force context retention in persistent session storage
        Session::put('sync_group_id', $groupId);
        Session::put('sync_subject_id', $subjectId);
        Session::save(); // Force absolute save commitment to storage driver

        // 🟢 THE FIX: Package parameters into a state payload to survive the external redirection thread
        $statePayload = json_encode([
            'group_id' => $groupId,
            'subject_id' => $subjectId
        ]);

        return Socialite::driver('google')
            ->scopes([
                'https://www.googleapis.com/auth/youtube.readonly',
                'openid',
                'profile',
                'email'
            ])
            ->with([
                'access_type' => 'offline', 
                'prompt' => 'consent',
                'state' => base64_encode($statePayload) // 🟢 Safely wrap within standard OAuth state parameters
            ])
            ->redirect();
    }

    /**
     * 2. Handle the Return payload from Google
     */
    public function callback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $token = $googleUser->token ?? ($googleUser->accessTokenResponseBody['access_token'] ?? null);

            if (!$token) {
                throw new \Exception("Could not retrieve access token from Google.");
            }

            // Save credentials where your AJAX handler expects it
            Session::put('youtube_access_token', $token);

            // 🟢 THE FIX: Safely parse parameters from returned state payload if sessions fell out of sync
            $groupId = null;
            $subjectId = null;

            if ($request->has('state')) {
                $decodedState = json_decode(base64_decode($request->state), true);
                if (is_array($decodedState)) {
                    $groupId = $decodedState['group_id'] ?? null;
                    $subjectId = $decodedState['subject_id'] ?? null;
                }
            }

            // Fallback to traditional session variables if state payload was dropped
            if (!$groupId) $groupId = Session::get('sync_group_id');
            if (!$subjectId) $subjectId = Session::get('sync_subject_id');

            // Re-commit variables firmly into tracking session memory
            Session::put('sync_group_id', $groupId);
            Session::put('sync_subject_id', $subjectId);
            Session::save();

            // Force parameters directly back into the destination search array parameters
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