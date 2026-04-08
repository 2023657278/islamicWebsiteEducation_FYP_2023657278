<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Resources;
use App\Models\Subject;
use App\Models\Group;

class YouTubeAuthController extends Controller
{
    // 1. Send User to Google
    public function redirect()
    {
        /** @var \Laravel\Socialite\Two\GoogleProvider $driver */
        $driver = Socialite::driver('google');

        return $driver
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
            // A. Get Token from Google
            /** @var \Laravel\Socialite\Two\User $googleUser */
            $googleUser = Socialite::driver('google')->user();
            $token = $googleUser->token;

            // B. Get User's Channel Details
            $channelResponse = Http::withToken($token)->get('https://www.googleapis.com/youtube/v3/channels', [
                'part' => 'contentDetails',
                'mine' => 'true',
            ]);

            if ($channelResponse->failed()) {
                // If API is not enabled, show error
                return redirect()->route('resources.index')->with('error', 'Google API Error: ' . $channelResponse->json()['error']['message'] ?? 'Unknown error');
            }

            $items = $channelResponse->json()['items'] ?? [];

            if (empty($items)) {
                return redirect()->route('resources.index')->with('error', 'Login successful, but NO YouTube Channel found.');
            }

            // Get the "Uploads" playlist ID
            $uploadsPlaylistId = $items[0]['contentDetails']['relatedPlaylists']['uploads'] ?? null;

            if (!$uploadsPlaylistId) {
                return redirect()->route('resources.index')->with('error', 'Channel found, but no Uploads Playlist exists.');
            }

            // C. Fetch Videos
            $videoResponse = Http::withToken($token)->get('https://www.googleapis.com/youtube/v3/playlistItems', [
                'part' => 'snippet',
                'playlistId' => $uploadsPlaylistId,
                'maxResults' => 10
            ]);

            $videos = $videoResponse->json()['items'] ?? [];
            $count = 0;

            // D. Save to Database
            $defaultSubject = Subject::first()->id ?? 1;

            foreach ($videos as $item) {
                $videoId = $item['snippet']['resourceId']['videoId'];
                $title = $item['snippet']['title'];
                $description = $item['snippet']['description'];

                // 🚀 SMART FILTER: CHECK FOR #MRSM
                if (!preg_match('/#MRSM/i', $title) && !preg_match('/#MRSM/i', $description)) {
                    continue; 
                }

                $exists = Resources::where('file_url', $videoId)->exists();

                if (!$exists) {
                    Resources::create([
                        'title' => $title,
                        'file_url' => $videoId,
                        'type' => 'video',
                        'teacher_id' => Auth::id(),
                        
                        // ✅ FIXED: Set to NULL so ALL classes can see it
                        'group_id' => null, 
                        
                        'subject_id' => $defaultSubject,
                        'is_public' => 1, 
                        'description' => $description
                    ]);
                    $count++;
                }
            }

            if ($count == 0) {
                return redirect()->route('resources.index')->with('error', "Sync connected, but no new videos found with tag #MRSM.");
            } else {
                return redirect()->route('resources.index')->with('success', "Success! Imported {$count} videos tagged with #MRSM.");
            }

        } catch (\Exception $e) {
            return redirect()->route('resources.index')->with('error', 'System Error: ' . $e->getMessage());
        }
    }
}