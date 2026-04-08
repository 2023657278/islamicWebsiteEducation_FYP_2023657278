<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Group;
use App\Models\QuizAttempt; // ✅ FIX: Use the correct model
use App\Models\Subject; 
use App\Services\AnalyticsService; // Optional if you want to use the service
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    // =========================================================
    // 1. LIST ALL STUDENTS
    // =========================================================
    public function index(Request $request)
    {
        $groups = Group::all();
        $query = User::with('group')->where('role', 'student');

        if ($request->filled('search')) {
            $query->where('name', 'LIKE', '%' . $request->search . '%');
        }

        if ($request->filled('group')) {
            $query->where('group_id', $request->group);
        }

        $students = $query->latest()->get();

        return view('students.index', compact('students', 'groups'));
    }

    // =========================================================
    // 2. SHOW ANALYTICS (Matches Student Side Logic)
    // =========================================================
    public function show(User $student)
    {
        // 1. Load basic relationships
        $student->load(['group.year']);

        // 2. Fetch Attempts (Using QuizAttempt table)
        $attempts = QuizAttempt::with('quiz.subject')
                             ->where('user_id', $student->id)
                             ->orderBy('created_at', 'asc') // Oldest first for chart
                             ->get();

        // 3. Prepare Chart Data
        $dates = [];
        $scores = [];
        foreach ($attempts as $att) {
            $dates[] = $att->created_at->format('d M'); 
            $scores[] = $att->score;
        }

        // 4. PREDICTION ALGORITHM (Same as Student Side)
        $n = count($scores);
        $slope = 0;
        $predictedNextScore = 0;
        $trendPoints = [];
        $currentAvg = $attempts->avg('score') ?? 0;
        
        if ($n > 1) {
            $x = range(1, $n); 
            $y = $scores;   
            
            $sumX = array_sum($x);
            $sumY = array_sum($y);
            
            $sumXY = 0;
            $sumXX = 0;
            
            for ($i = 0; $i < $n; $i++) {
                $sumXY += ($x[$i] * $y[$i]);
                $sumXX += ($x[$i] * $x[$i]);
            }
            
            // Slope (m)
            $denominator = ($n * $sumXX - $sumX * $sumX);
            if($denominator != 0) {
                $slope = ($n * $sumXY - $sumX * $sumY) / $denominator;
            }

            // Intercept (c)
            $avgX = ($n + 1) / 2;
            $intercept = $currentAvg - ($slope * $avgX);
            
            // Generate Trend Line
            for ($i = 1; $i <= $n; $i++) {
                $trendPoints[] = round(($slope * $i) + $intercept, 1);
            }

            // Predict Next Score
            $predictedNextScore = round(($slope * ($n + 1)) + $intercept, 1);
            if($predictedNextScore > 100) $predictedNextScore = 100;
            if($predictedNextScore < 0) $predictedNextScore = 0;
        }

        // 5. SUBJECT MASTERY (Dynamic)
        $allSubjects = Subject::withCount('quizzes')->get(); 
        
        $subjectProgress = $allSubjects->map(function($subject) use ($attempts) {
            // Filter attempts for this subject
            $subAttempts = $attempts->filter(function($attempt) use ($subject) {
                return $attempt->quiz->subject_id == $subject->id;
            });

            // Stats
            $avgScore = $subAttempts->count() > 0 ? round($subAttempts->avg('score')) : 0;
            $completedCount = $subAttempts->pluck('quiz_id')->unique()->count();
            $totalAvailable = $subject->quizzes_count;
            
            $completionPercent = $totalAvailable > 0 
                ? round(($completedCount / $totalAvailable) * 100) 
                : 0;

            return (object) [
                'name' => $subject->subject_name,
                'avg_score' => $avgScore,
                'completed' => $completedCount,
                'total' => $totalAvailable,
                'progress' => $completionPercent
            ];
        });

        // 6. CLUSTERING STATUS
        $cluster = 'Normal';
        if($currentAvg >= 80) $cluster = 'High Achiever';
        if($currentAvg < 40 && $n > 0) $cluster = 'Needs Attention';
        if($n == 0) $cluster = 'New Student';

        // 7. Return View
        return view('students.show', compact(
            'student', 
            'dates', 
            'scores', 
            'trendPoints',
            'slope', 
            'predictedNextScore', 
            'subjectProgress',
            'cluster',
            'currentAvg'
        ));
    }

    // =========================================================
    // 3. STANDARD CRUD
    // =========================================================
    public function create() {
        $groups = Group::with('year')->get();
        return view('students.create', compact('groups'));
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'group_id' => 'required|exists:groups,id',
        ]);
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'group_id' => $request->group_id,
            'phone_number' => $request->phone_number,
            'role' => 'student',
        ]);
        return redirect()->route('students.index')->with('success', 'Student registered successfully!');
    }

    public function edit(User $student) {
        $groups = Group::with('year')->get();
        return view('students.edit', compact('student', 'groups'));
    }

    public function update(Request $request, User $student) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $student->id,
            'group_id' => 'required|exists:groups,id',
            'password' => 'nullable|min:6|confirmed',
        ]);
        $data = $request->only(['name', 'email', 'group_id', 'phone_number']);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        $student->update($data);
        return redirect()->route('students.index')->with('success', 'Student updated successfully.');
    }

    public function destroy(User $student) {
        $student->delete();
        return redirect()->route('students.index')->with('success','Student deleted successfully');
    }
}