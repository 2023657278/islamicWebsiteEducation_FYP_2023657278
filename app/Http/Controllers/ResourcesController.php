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

class ResourcesController extends Controller
{
    public function index()
    {
        // Eager load Group and Year for proper labeling
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

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string', 
            'type' => 'required|in:note,textbook', 
            'subject_id' => 'required', 
            'file' => 'required|file|max:51200'
        ]);
        
        $path = $request->file('file')->store('class_materials', 'public');

        Resources::create([
            'title' => $request->title, 
            'file_url' => $path, // Standardized column name
            'type' => $request->type, 
            'teacher_id' => Auth::id(),
            'subject_id' => $request->subject_id, 
            'group_id' => ($request->type === 'textbook') ? null : $request->group_id,
            'is_public' => ($request->type === 'textbook'),
        ]);

        return back()->with('success', 'Resource uploaded successfully!');
    }

    // ✅ FIXED PREVIEW & DOWNLOAD
    public function preview($id)
    {
        $resource = Resources::findOrFail($id);
        if (empty($resource->file_url)) return back()->with('error', 'No file associated with this resource.');

        $path = storage_path('app/public/' . $resource->file_url);

        if (!file_exists($path) || is_dir($path)) {
            return back()->with('error', 'File not found on server.');
        }

        $mimeType = mime_content_type($path);
        return response()->file($path, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $resource->title . '"'
        ]);
    }

    public function download($id)
    {
        $resource = Resources::findOrFail($id);
        
        if (!Storage::disk('public')->exists($resource->file_url)) {
            return back()->with('error', 'Download failed: File does not exist on server.');
        }

        // Determine the extension. If it's a textbook/note, we ensure it ends in .pdf
        $extension = pathinfo($resource->file_url, PATHINFO_EXTENSION);
        if (empty($extension)) {
            $extension = ($resource->type === 'video') ? 'mp4' : 'pdf';
        }

        $fileName = $resource->title . '.' . $extension;

        // Forced download with the correct filename and extension
        $path = storage_path('app/public/' . $resource->file_url);
        return response()->download($path, $fileName);
    }

    public function destroy($id) {
        $res = Resources::findOrFail($id);
        if($res->teacher_id == Auth::id()) { 
            if($res->file_url) Storage::disk('public')->delete($res->file_url);
            $res->delete(); 
            return back()->with('success', 'Resource deleted.'); 
        }
        return back()->with('error', 'Unauthorized');
    }

    // YouTube Sync logic remains unchanged but standardized to file_url
    public function redirectToYouTube(Request $request)
    {
        Session::put('sync_group_id', $request->group_id);
        Session::put('sync_subject_id', $request->subject_id);

        $query = http_build_query([
            'client_id' => env('GOOGLE_CLIENT_ID'),
            'redirect_uri' => env('GOOGLE_REDIRECT_URL'),
            'response_type' => 'code',
            'scope' => 'https://www.googleapis.com/auth/youtube.readonly',
            'access_type' => 'offline',
            'prompt' => 'consent'
        ]);
        return redirect('https://accounts.google.com/o/oauth2/v2/auth?' . $query);
    }

    public function handleYouTubeCallback(Request $request)
    {
        $response = Http::post('https://oauth2.googleapis.com/token', [
            'code' => $request->code,
            'client_id' => env('GOOGLE_CLIENT_ID'),
            'client_secret' => env('GOOGLE_CLIENT_SECRET'),
            'redirect_uri' => env('GOOGLE_REDIRECT_URL'),
            'grant_type' => 'authorization_code',
        ]);
        $accessToken = $response->json()['access_token'];
        $videoRes = Http::withToken($accessToken)->get('https://www.googleapis.com/youtube/v3/search', [
            'part' => 'snippet', 'forMine' => 'true', 'type' => 'video', 'q' => '#MRSM', 'maxResults' => 50
        ]);
        $youtubeVideos = [];
        if ($videoRes->successful()) {
            foreach ($videoRes->json()['items'] as $item) {
                $youtubeVideos[] = ['id' => $item['id']['videoId'], 'title' => $item['snippet']['title'], 'thumbnail' => $item['snippet']['thumbnails']['medium']['url']];
            }
        }
        $group_id = Session::get('sync_group_id');
        $subject_id = Session::get('sync_subject_id');
        return view('resources.sync_selection', compact('youtubeVideos', 'group_id', 'subject_id'));
    }

    public function storeSelectedVideos(Request $request)
    {
        // 1. Validation: Ensure we actually have videos and a subject
    if (!$request->has('video_ids') || empty($request->video_ids)) {
        return back()->with('error', 'Please select at least one video to import.');
    }

    if (!$request->subject_id) {
        return back()->with('error', 'Subject ID is missing. Please restart the sync process.');
    }

    // 2. The Import Loop
    foreach ($request->video_ids as $videoId => $title) {
        Resources::updateOrCreate(
            // Search criteria: If this teacher already imported this specific video...
            ['file_url' => $videoId, 'teacher_id' => Auth::id()], 
            
            // ...then just update these fields instead of making a duplicate
            [
                'title' => $title, 
                'type' => 'video', 
                'subject_id' => $request->subject_id, 
                'group_id' => ($request->group_id == 'null') ? null : $request->group_id, 
                'is_public' => false
            ]
        );
    }

    return redirect()->route('resources.index')->with('success', 'YouTube sync complete!');
    }
}