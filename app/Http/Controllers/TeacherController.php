<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Timetable;
use App\Models\Group;
use App\Models\QuizAttempt; // ✅ Added to fetch student scores

class TeacherController extends Controller
{
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

    public function create()
    {
        return view('teachers.create');
    }

    public function store(Request $request)
    {
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

        return redirect()->route('teachers.index')->with('success', 'Teacher added successfully!');
    }

    // =========================================================
    //  SHOW METHOD (Upgraded for Analytics)
    // =========================================================
    public function show(User $teacher)
    {
        // 1. Get Timetables (Weekly Schedule)
        $schedules = Timetable::where('teacher_id', $teacher->id)
                    ->with(['subject', 'group', 'day']) 
                    ->get();

        // 2. Get Groups assigned to this teacher
        $groupIds = $schedules->pluck('group_id')->unique();
        
        // 3. AGGREGATE PERFORMANCE PER GROUP
        $groups = Group::whereIn('id', $groupIds)
                    ->withCount('students')
                    ->with(['students.quizAttempts']) // Load students and their quiz results
                    ->get()
                    ->map(function($group) {
                        
                        // Collect all quiz scores from all students in this group
                        $allScores = collect();
                        foreach($group->students as $student) {
                            foreach($student->quizAttempts as $attempt) {
                                $allScores->push($attempt->score);
                            }
                        }

                        // Calculate Stats
                        $avgScore = $allScores->avg() ?? 0;
                        $totalQuizzesTaken = $allScores->count();

                        // Determine Health Status
                        $status = 'Normal';
                        if($avgScore >= 80) $status = 'Excellent';
                        if($avgScore < 50 && $totalQuizzesTaken > 0) $status = 'Critical';

                        // Attach data to the group object for the view
                        $group->avg_performance = round($avgScore, 1);
                        $group->total_activity = $totalQuizzesTaken;
                        $group->performance_status = $status;

                        return $group;
                    });

        // 4. Calculate Overall Teacher Rating (Average of all groups)
        $overallTeacherRating = $groups->avg('avg_performance') ?? 0;

        return view('teachers.show', compact('teacher', 'groups', 'schedules', 'overallTeacherRating'));
    }

    public function edit(User $teacher)
    {
        return view('teachers.edit', compact('teacher'));
    }

    public function update(Request $request, User $teacher)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $teacher->id,
            'password' => 'nullable|min:6|confirmed',
        ]);

        $data = $request->only(['name', 'email', 'phone_number']);
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $teacher->update($data);

        return redirect()->route('teachers.index')->with('success', 'Teacher profile updated.');
    }

    public function destroy(User $teacher)
    {
        $teacher->delete();
        return redirect()->route('teachers.index')->with('success','Teacher deleted successfully');
    }
}