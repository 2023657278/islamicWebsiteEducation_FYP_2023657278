<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
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
        // 🟢 Save context to session to recover after return
        Session::put('sync_group_id', $request->group_id);
        Session::put('sync_subject_id', $request->subject_id);

        /** @var \Laravel\Socialite\Two\AbstractProvider $googleProvider */
        $googleProvider = Socialite::driver('google');

        return $googleProvider
            ->scopes([
                'https://www.googleapis.com/auth/youtube.readonly',
                'openid',
                'profile',
                'email'
            ])
            ->with(['access_type' => 'offline', 'prompt' => 'consent'])
            ->redirect();
    }

    // 2. Handle the Return
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $token = $googleUser->token ?? $googleUser->accessToken ?? null;

            if (! $token) {
                return redirect()->route('resources.index')
                    ->with('error', 'Google token unavailable.');
            }

            // 🟢 Retrieve IDs from session
            $group_id = Session::get('sync_group_id');
            $subject_id = Session::get('sync_subject_id');

            // B. Get User's Channel Details
            $channelResponse = Http::withToken($token)->get('https://www.googleapis.com/youtube/v3/channels', [
                'part' => 'contentDetails',
                'mine' => 'true',
            ]);

            if ($channelResponse->failed()) {
                return redirect()->route('resources.index')
                    ->with('error', 'Google API Error: ' . ($channelResponse->json()['error']['message'] ?? 'Unknown error'));
            }

            $items = $channelResponse->json()['items'] ?? [];
            if (empty($items)) {
                return redirect()->route('resources.index')
                    ->with('error', 'Login successful, but NO YouTube Channel found.');
            }

            $uploadsPlaylistId = $items[0]['contentDetails']['relatedPlaylists']['uploads'] ?? null;

            // C. Fetch Videos from Uploads Playlist
            $videoResponse = Http::withToken($token)->get('https://www.googleapis.com/youtube/v3/playlistItems', [
                'part' => 'snippet',
                'playlistId' => $uploadsPlaylistId,
                'maxResults' => 20 
            ]);

            $items = $videoResponse->json()['items'] ?? [];
            $youtubeVideos = [];

            // D. Filter for #MRSM
            foreach ($items as $item) {
                $title = $item['snippet']['title'];
                $description = $item['snippet']['description'];

                if (preg_match('/#MRSM/i', $title) || preg_match('/#MRSM/i', $description)) {
                    $youtubeVideos[] = [
                        'id' => $item['snippet']['resourceId']['videoId'],
                        'title' => $title,
                        'thumbnail' => $item['snippet']['thumbnails']['medium']['url'] ?? '',
                    ];
                }
            }

            // 🟢 Return to selection view - do NOT auto-save
            return view('resources.sync_selection', compact('youtubeVideos', 'group_id', 'subject_id'));

        } catch (\Exception $e) {
            return redirect()->route('resources.index')
                ->with('error', 'System Error: ' . $e->getMessage());
        }
    }
}