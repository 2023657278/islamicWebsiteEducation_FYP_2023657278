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
        $groupId = $request->group_id;
        $subjectId = $request->subject_id;

        // Force explicit context parameters into session storage
        Session::put('sync_group_id', $groupId);
        Session::put('sync_subject_id', $subjectId);
        Session::save(); 

        // Serialize variables cleanly into a string layout payload
        $statePayload = json_encode([
            'group_id' => $groupId,
            'subject_id' => $subjectId
        ]);

        // 🟢 THE ABSOLUTE FIX: 
        // 1. Force 'select_account' inside with() to prompt Google's account picker view.
        // 2. Add an evaluation parameter 'approval_prompt' => 'force' to break active browser cookie states.
        return Socialite::driver('google')
            ->scopes([
                'https://www.googleapis.com/auth/youtube.readonly',
                'openid',
                'profile',
                'email'
            ])
            ->with([
                'access_type'     => 'offline', 
                'prompt'          => 'select_account consent', // Forces account selector
                'approval_prompt' => 'force',                  // Overrides Google browser caching profiles
                'state'           => base64_encode($statePayload)
            ])
            ->redirect();
    }

    /**
     * 2. Handle the Return payload from Google
     */
    public function callback(Request $request)
    {
        try {
            // Explicitly enforce stateless parsing to process custom state payloads safely
            $googleUser = Socialite::driver('google')->stateless()->user();
            
            $token = $googleUser->token ?? ($googleUser->accessTokenResponseBody['access_token'] ?? null);

            if (!$token) {
                throw new \Exception("Could not retrieve access token from Google.");
            }

            // Save credentials where your AJAX handler expects it
            Session::put('youtube_access_token', $token);

            // Re-extract tracking parameters from returned state parameter
            $groupId = null;
            $subjectId = null;

            if ($request->has('state')) {
                $decodedState = json_decode(base64_decode($request->state), true);
                if (is_array($decodedState)) {
                    $groupId = $decodedState['group_id'] ?? null;
                    $subjectId = $decodedState['subject_id'] ?? null;
                }
            }

            // Fallback parameters evaluation loop
            if (!$groupId) $groupId = Session::get('sync_group_id');
            if (!$subjectId) $subjectId = Session::get('sync_subject_id');

            Session::put('sync_group_id', $groupId);
            Session::put('sync_subject_id', $subjectId);
            Session::save();

            // Redirect back to search view with correct parameters on the first click
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