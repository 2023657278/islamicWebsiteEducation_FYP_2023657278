<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class YouTubeAuthController extends Controller
{
    private const CALLBACK_URL = 'https://islamic-lms.online/login/google/callback';

    public function redirect(Request $request)
    {
        Session::put('sync_group_id', $request->group_id);
        Session::put('sync_subject_id', $request->subject_id);

        return Socialite::driver('google')
            ->redirectUrl(self::CALLBACK_URL)
            ->scopes([
                'https://www.googleapis.com/auth/youtube.readonly',
                'openid',
                'profile',
                'email'
            ])
            ->with(['access_type' => 'offline', 'prompt' => 'consent'])
            ->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')
                ->redirectUrl(self::CALLBACK_URL)
                ->user();

            $token = $googleUser->token;
            $group_id = Session::get('sync_group_id');
            $subject_id = Session::get('sync_subject_id');

            $channelResponse = Http::withToken($token)->get('https://www.googleapis.com/youtube/v3/channels', [
                'part' => 'contentDetails',
                'mine' => 'true',
            ]);

            if ($channelResponse->failed()) {
                return redirect()->route('resources.index')->with('error', 'API Error: Could not connect to YouTube.');
            }

            $items = $channelResponse->json()['items'] ?? [];
            if (empty($items)) {
                return redirect()->route('resources.index')->with('error', 'No YouTube channel found.');
            }

            $uploadsPlaylistId = $items[0]['contentDetails']['relatedPlaylists']['uploads'] ?? null;

            $videoResponse = Http::withToken($token)->get('https://www.googleapis.com/youtube/v3/playlistItems', [
                'part' => 'snippet',
                'playlistId' => $uploadsPlaylistId,
                'maxResults' => 20
            ]);

            $items = $videoResponse->json()['items'] ?? [];
            $youtubeVideos = [];

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

            return view('resources.sync_selection', compact('youtubeVideos', 'group_id', 'subject_id'));

        } catch (\Exception $e) {
            return redirect()->route('resources.index')->with('error', 'System Error: ' . $e->getMessage());
        }
    }
}