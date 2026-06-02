<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Timetable;
use App\Models\Group;
use App\Models\QuizAttempt;

class TeacherController extends Controller
{
    /**
     * Display a listing of teachers.
     * Both Teachers and Admins can view this directory layout index.
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'teacher');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%")
                  ->orWhere('email', 'LIKE', "%$search%");
            });
        }

        $teachers = $query->latest()->get();
        return view('teachers.index', compact('teachers'));
    }

    /**
     * Show the form for creating a new teacher.
     * Access Restriction: Admins Only
     */
    public function create()
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('teachers.index')->with('error', 'Access denied. Only system administrators can register staff.');
        }

        return view('teachers.create');
    }

    /**
     * Store a newly created teacher in storage.
     * Access Restriction: Admins Only
     */
    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('teachers.index')->with('error', 'Unauthorized operation.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'phone_number' => 'nullable|string',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'role' => 'teacher',
        ]);

        return redirect()->route('teachers.index')->with('success', 'Teacher registered successfully!');
    }

    /**
     * Display the specified teacher's detailed profiles and schedules.
     * Accessible by both Admins and Teachers.
     */
    public function show(User $teacher)
    {
        // 1. Get Timetables (Weekly Schedule)
        $schedules = Timetable::where('teacher_id', $teacher->id)
                    ->with(['subject', 'group', 'day']) 
                    ->get();

        // 2. Get Groups assigned to this teacher
        $groupIds = $schedules->pluck('group_id')->unique();
        
        // 3. Aggregate performance vectors across groups
        $groups = Group::whereIn('id', $groupIds)
                    ->withCount('students')
                    ->with(['students.quizAttempts'])
                    ->get()
                    ->map(function($group) {
                        
                        $allScores = collect();
                        foreach($group->students as $student) {
                            foreach($student->quizAttempts as $attempt) {
                                $allScores->push($attempt->score);
                            }
                        }

                        // Compute dynamic operational health matrix states
                        $avgScore = $allScores->avg() ?? 0;
                        $totalQuizzesTaken = $allScores->count();

                        $status = 'Normal';
                        if($avgScore >= 80) $status = 'Excellent';
                        if($avgScore < 50 && $totalQuizzesTaken > 0) $status = 'Critical';

                        $group->avg_performance = round($avgScore, 1);
                        $group->total_activity = $totalQuizzesTaken;
                        $group->performance_status = $status;

                        return $group;
                    });

        // 4. Calculate Overall Teacher Rating Vector
        $overallTeacherRating = $groups->avg('avg_performance') ?? 0;

        return view('teachers.show', compact('teacher', 'groups', 'schedules', 'overallTeacherRating'));
    }

    /**
     * Show the form for editing the specified teacher resource profile.
     * Access Restriction: Admins Only
     */
    public function edit(User $teacher)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('teachers.index')->with('error', 'Access denied. Only system administrators can alter staff records.');
        }

        return view('teachers.edit', compact('teacher'));
    }

    /**
     * Update the specified teacher resource entry in data storage layers.
     * Access Restriction: Admins Only
     */
    public function update(Request $request, User $teacher)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('teachers.index')->with('error', 'Unauthorized data update request.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $teacher->id,
            'password' => 'nullable|min:6|confirmed',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['name', 'email', 'phone_number']);
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // =========================================================
        // ✅ IMAGE HANDLING LOGIC (WITH PURGING RECOVERY MECHANICS)
        // =========================================================
        
        // Scenario A: Admin opted to drop avatar and return to defaults
        if ($request->boolean('remove_profile_image')) {
            if ($teacher->profile_image && \Illuminate\Support\Facades\Storage::exists('public/profile_images/' . $teacher->profile_image)) {
                \Illuminate\Support\Facades\Storage::delete('public/profile_images/' . $teacher->profile_image);
            }
            $data['profile_image'] = null; // Set field column null in DB
        } 
        // Scenario B: Admin uploaded a fresh replacement binary file
        elseif ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            $filename = 'avatar_' . time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/profile_images', $filename);
            
            if ($teacher->profile_image && \Illuminate\Support\Facades\Storage::exists('public/profile_images/' . $teacher->profile_image)) {
                \Illuminate\Support\Facades\Storage::delete('public/profile_images/' . $teacher->profile_image);
            }

            $data['profile_image'] = $filename;
        }

        // Apply changes tracking vectors down to storage layers
        $teacher->update($data);

        if (auth()->id() === $teacher->id) {
            return redirect()->back()->with('success', 'Your Admin profile credentials and avatar image have been updated successfully.');
        }

        return redirect()->route('teachers.index')->with('success', 'Teacher profile updated successfully.');
    }

    /**
     * Remove the specified teacher account configuration from database layers.
     * Access Restriction: Admins Only
     */
    public function destroy(User $teacher)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('teachers.index')->with('error', 'Unauthorized action configuration.');
        }

        $teacher->delete();
        return redirect()->route('teachers.index')->with('success', 'Teacher structural record purged successfully.');
    }
}