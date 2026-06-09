<?php

namespace App\Http\Controllers;

use App\Models\Resources;
use App\Models\Group;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class ResourcesController extends Controller
{
    /**
     * Display the main library.
     */
    public function index()
    {
        $myResources = Resources::where('teacher_id', Auth::id())
                     ->whereIn('type', ['note', 'video'])
                     ->with(['group.year', 'subject']) 
                     ->latest()
                     ->get();

        $textbooks = Resources::where('type', 'textbook')
                     ->with('subject')
                     ->latest()
                     ->get();
        
        $groups = Group::with('year')->get();
        $subjects = Subject::all();

        return view('resources.index', compact('myResources', 'textbooks', 'groups', 'subjects'));
    }

    /**
     * Handle File Uploads (Notes/Textbooks).
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string', 
            'type' => 'required|in:note,textbook', 
            'subject_id' => 'required', 
            'file' => 'required|file|max:204800'
        ]);
        
        $path = $request->file('file')->store('class_materials', 'public');

        Resources::create([
            'title' => $request->title, 
            'file_url' => $path,
            'type' => $request->type, 
            'teacher_id' => Auth::id(),
            'subject_id' => $request->subject_id, 
            'group_id' => ($request->type === 'textbook') ? null : $request->group_id,
            'is_public' => ($request->type === 'textbook'),
        ]);

        return back()->with('success', 'Resource uploaded successfully!');
    }

    // --- 📂 FILE MANAGEMENT ---

    public function preview($id)
    {
        $resource = Resources::findOrFail($id);
        $path = storage_path('app/public/' . $resource->file_url);
        if (!file_exists($path) || is_dir($path)) return back()->with('error', 'File not found.');
        
        return response()->file($path, [
            'Content-Type' => mime_content_type($path),
            'Content-Disposition' => 'inline; filename="' . $resource->title . '"'
        ]);
    }

    public function download($id)
    {
        $resource = Resources::findOrFail($id);
        $path = storage_path('app/public/' . $resource->file_url);
        if (!Storage::disk('public')->exists($resource->file_url)) return back()->with('error', 'File missing.');
        
        return response()->download($path, $resource->title . '.' . pathinfo($resource->file_url, PATHINFO_EXTENSION));
    }

    public function destroy($id) {
        $res = Resources::findOrFail($id);
        if($res->teacher_id == Auth::id()) { 
            if($res->file_url && $res->type !== 'video') Storage::disk('public')->delete($res->file_url);
            $res->delete(); 
            return back()->with('success', 'Resource deleted.'); 
        }
        return back()->with('error', 'Unauthorized');
    }

    // --- 📺 YOUTUBE SEARCH & SYNC ---

    /**
     * Initial Search Landing Page.
     */
    public function youtubeSearch(Request $request)
    {
        $group_id = $request->group_id ?? Session::get('sync_group_id');
        $subject_id = $request->subject_id ?? Session::get('sync_subject_id');
        $type = $request->query('type', 'public'); 

        return view('resources.sync_selection', compact('group_id', 'subject_id', 'type'));
    }

    /**
     * AJAX Fetching Endpoint
     */
    public function fetchYoutubeData(Request $request)
    {
        $query = $request->query('q');
        $pageToken = $request->query('pageToken');
        $type = $request->query('type', 'public');
        $apiKey = env('YOUTUBE_API_KEY');

        if ($type === 'mine') {
            $token = Session::get('youtube_access_token');
            if (!$token) return response()->json(['error' => 'auth_required'], 401);

            $response = Http::withToken($token)->get("https://www.googleapis.com/youtube/v3/search", [
                'part' => 'snippet',
                'forMine' => 'true',
                'type' => 'video',
                'maxResults' => 12,
                'pageToken' => $pageToken,
                'q' => $query 
            ]);
        } else {
            $response = Http::get("https://www.googleapis.com/youtube/v3/search", [
                'part' => 'snippet',
                'maxResults' => 12,
                'q' => $query,
                'type' => 'video',
                'pageToken' => $pageToken,
                'key' => $apiKey,
            ]);
        }

        return response()->json($response->json());
    }

    /**
     * OAuth: Redirect to Google Login.
     */
    public function redirectToYouTube(Request $request)
    {
        Session::put('sync_group_id', $request->group_id);
        Session::put('sync_subject_id', $request->subject_id);
        Session::save();

        return redirect()->route('auth.youtube', [
            'group_id' => $request->group_id,
            'subject_id' => $request->subject_id
        ]);
    }

    /**
     * Final step: Save selected videos to DB.
     */
    public function storeSelectedVideos(Request $request)
    {
        if (!$request->has('video_ids') || empty($request->video_ids)) {
            return back()->with('error', 'Please select at least one video to import.');
        }

        $subject_id = $request->subject_id ?? Session::get('sync_subject_id');
        $group_id = ($request->group_id && $request->group_id !== 'null') 
                    ? $request->group_id 
                    : Session::get('sync_group_id');

        if (!$subject_id) {
            return redirect()->route('resources.index')->with('error', 'Subject ID context missing.');
        }

        DB::transaction(function () use ($request, $subject_id, $group_id) {
            foreach ($request->video_ids as $videoId => $title) {
                Resources::updateOrCreate(
                    ['file_url' => $videoId, 'teacher_id' => Auth::id()], 
                    [
                        'title' => $title, 
                        'type' => 'video', 
                        'subject_id' => $subject_id, 
                        'group_id' => $group_id, 
                        'is_public' => false
                    ]
                );
            }
        });

        // 🟢 FIX: We no longer clear 'youtube_access_token' immediately right here upon saving
        // This ensures the channel view retains its session variables on the very first try.
        Session::forget(['sync_group_id', 'sync_subject_id', 'sync_type']);
        Session::save();

        Artisan::call('cache:clear');

        return redirect()
            ->route('resources.index')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->with('success', 'YouTube videos successfully added to your library!');
    }
}