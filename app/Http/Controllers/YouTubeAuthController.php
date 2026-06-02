<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Models\Resources;
use App\Models\Subject;
use App\Models\Group;

class YouTubeAuthController extends Controller
{
    // 1. Send User to Google
    public function redirect(Request $request)
    {
        Session::put('sync_group_id', $request->group_id);
        Session::put('sync_subject_id', $request->subject_id);

        /** @var \Laravel\Socialite\Two\GoogleProvider $driver */
        $driver = Socialite::driver('google');

        return $driver
            ->scopes([
                'https://www.googleapis.com/auth/youtube.readonly',
                'openid',
                'profile',
                'email'
            ])
            // Force the redirect URL here:
            ->redirectUrl('https://islamic-lms.online/login/google/callback')
            ->with(['access_type' => 'offline', 'prompt' => 'consent'])
            ->redirect();
    }

    // 2. Handle the Return
    public function callback()
    {
        try {
            // A. Get Token from Google
            /** @var \Laravel\Socialite\Two\User $googleUser */
            $googleUser = Socialite::driver('google')->user();
            $token = $googleUser->token;

            // 🟢 ADDED: Retrieve the IDs from the session
            $group_id = Session::get('sync_group_id');
            $subject_id = Session::get('sync_subject_id');

            // B. Get User's Channel Details
            $channelResponse = Http::withToken($token)->get('https://www.googleapis.com/youtube/v3/channels', [
                'part' => 'contentDetails',
                'mine' => 'true',
            ]);

            if ($channelResponse->failed()) {
                return redirect()->route('resources.index')->with('error', 'Google API Error: ' . ($channelResponse->json()['error']['message'] ?? 'Unknown error'));
            }

            $items = $channelResponse->json()['items'] ?? [];

            if (empty($items)) {
                return redirect()->route('resources.index')->with('error', 'Login successful, but NO YouTube Channel found.');
            }

            $uploadsPlaylistId = $items[0]['contentDetails']['relatedPlaylists']['uploads'] ?? null;

            // C. Fetch Videos
            $videoResponse = Http::withToken($token)->get('https://www.googleapis.com/youtube/v3/playlistItems', [
                'part' => 'snippet',
                'playlistId' => $uploadsPlaylistId,
                'maxResults' => 20 // Fetches more so we can filter
            ]);

            $items = $videoResponse->json()['items'] ?? [];
            $youtubeVideos = [];

            // D. Filter and Format for Selection
            foreach ($items as $item) {
                $title = $item['snippet']['title'];
                $description = $item['snippet']['description'];

                // 🚀 SMART FILTER: CHECK FOR #MRSM
                if (preg_match('/#MRSM/i', $title) || preg_match('/#MRSM/i', $description)) {
                    $youtubeVideos[] = [
                        'id' => $item['snippet']['resourceId']['videoId'],
                        'title' => $title,
                        'thumbnail' => $item['snippet']['thumbnails']['medium']['url'] ?? '',
                    ];
                }
            }

            // 🟢 MODIFIED: Return the selection view instead of auto-saving
            return view('resources.sync_selection', compact('youtubeVideos', 'group_id', 'subject_id'));

        } catch (\Exception $e) {
            return redirect()->route('resources.index')->with('error', 'System Error: ' . $e->getMessage());
        }
    }

    
}