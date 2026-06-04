<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Resources;
use App\Models\Timetable;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StudentResourceController extends Controller
{
    // =========================================================
    // 1. TEACHER RESOURCES
    // =========================================================
    public function index(Request $request)
    {
        $student = Auth::user();
        
        // Find teachers linked to this student's group
        $teacherIds = Timetable::where('group_id', $student->group_id)
                        ->pluck('teacher_id')
                        ->unique();

        $query = User::whereIn('id', $teacherIds)->with('group');

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'LIKE', "%{$request->search}%");
        }

        $teachers = $query->get();

        // Calculate stats
        $totalVideos = 0;
        $totalNotes = 0;

        foreach ($teachers as $teacher) {
            // ✅ FIX: Count private class videos OR public global videos
            $teacher->video_count = Resources::where('teacher_id', $teacher->id)
                                    ->where('type', 'video')
                                    ->where(function($q) use ($student) {
                                        $q->where('group_id', $student->group_id)
                                          ->orWhereNull('group_id');
                                    })
                                    ->count();
            
            $teacher->note_count = Resources::where('teacher_id', $teacher->id)
                                    ->where('group_id', $student->group_id)
                                    ->where('type', 'note')
                                    ->count();
                                    
            $timetable = Timetable::where('teacher_id', $teacher->id)
                            ->where('group_id', $student->group_id)
                            ->with('subject')
                            ->first();
            $teacher->subject_name = $timetable ? $timetable->subject->subject_name : 'General';

            $totalVideos += $teacher->video_count;
            $totalNotes += $teacher->note_count;
        }

        return view('users.resources.index', compact('teachers', 'totalVideos', 'totalNotes'));
    }

    public function show($teacherId)
    {
        $student = Auth::user();
        
        // Explicitly ensuring we pull all attributes including images
        $teacher = User::where('id', $teacherId)->firstOrFail();

        // Fetch videos for Class OR Global
        $videos = Resources::where('teacher_id', $teacherId)
                    ->where('type', 'video')
                    ->where(function($q) use ($student) {
                        $q->where('group_id', $student->group_id)
                          ->orWhereNull('group_id');
                    })
                    ->latest()
                    ->get();

        $notes = Resources::where('teacher_id', $teacherId)
                    ->where('group_id', $student->group_id)
                    ->where('type', 'note')
                    ->latest()
                    ->get();

        $timetable = Timetable::where('teacher_id', $teacherId)
                            ->where('group_id', $student->group_id)
                            ->with('subject')
                            ->first();
                            
        $subjectName = $timetable ? $timetable->subject->subject_name : 'General';

        return view('users.resources.show', compact('teacher', 'videos', 'notes', 'subjectName'));
    }

    // =========================================================
    // 2. TEXTBOOKS (Public - Everyone Can See)
    // =========================================================
    public function textbooks(Request $request)
    {
        $student = Auth::user();

        // 1. Base Query
        $query = Resources::with(['subject', 'teacher'])
                        ->where('type', 'textbook');

        // 2. Filter by Search (Title)
        if ($request->has('search') && $request->search != '') {
            $query->where('title', 'LIKE', '%' . $request->search . '%');
        }

        // 3. Filter by Subject (Dropdown)
        if ($request->has('subject_id') && $request->subject_id != '') {
            $query->where('subject_id', $request->subject_id);
        }

        $textbooks = $query->latest()->get();

        // 4. Inject Progress Data
        foreach ($textbooks as $book) {
            $progress = DB::table('textbook_progress')
                          ->where('user_id', $student->id)
                          ->where('resource_id', $book->id)
                          ->first();
            
            $book->progress_percent = $progress ? $progress->percentage : 0;
        }

        // 5. GROUP BY SUBJECT NAME
        $groupedTextbooks = $textbooks->groupBy(function($item) {
            return $item->subject->subject_name ?? 'General';
        });

        // 6. Get all subjects for filter
        $allSubjects = \App\Models\Subject::all();

        return view('users.resources.textbooks', compact('groupedTextbooks', 'allSubjects'));
    }

    // 4. OPEN READER VIEW
    public function read($id)
    {
        // 🟢 FIX 1: Force find the resource mapping to ensure it doesn't fail silently
        $book = Resources::where('id', $id)->where('type', 'textbook')->firstOrFail();
        $student = Auth::user();

        // Get existing progress or start at page 1
        $progress = DB::table('textbook_progress')
                      ->where('user_id', $student->id)
                      ->where('resource_id', $book->id)
                      ->first();

        $startPage = $progress ? $progress->current_page : 1;

        // 🟢 FIX 2: Ensure file URL context isn't broken or missing leading separators
        // This strips out any accidental double slashes before sending it to the view
        $book->file_url = ltrim($book->file_url, '/');

        return view('users.resources.read', compact('book', 'startPage'));
    }

    // 5. SAVE PROGRESS (AJAX)
    public function saveProgress(Request $request)
    {
        $student = Auth::user();
        
        $percent = 0;
        if($request->total_pages > 0) {
            $percent = ($request->current_page / $request->total_pages) * 100;
        }

        DB::table('textbook_progress')->updateOrInsert(
            ['user_id' => $student->id, 'resource_id' => $request->resource_id],
            [
                'current_page' => $request->current_page,
                'total_pages' => $request->total_pages,
                'percentage' => round($percent), 
                'is_completed' => $percent >= 90, 
                'updated_at' => now()
            ]
        );

        return response()->json(['status' => 'success']);
    }
}