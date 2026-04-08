<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use App\Models\Year;
use App\Models\QuizAttempt;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        $query = Group::with('year')->withCount('students');

        if ($request->filled('search_name')) {
            $query->where('group_name', 'LIKE', '%' . $request->search_name . '%');
        }

        if ($request->filled('search_year')) {
            $yearSearch = $request->search_year;
            $query->whereHas('year', function($q) use ($yearSearch) {
                $q->where('year', 'LIKE', '%' . $yearSearch . '%');
            });
        }

        $groups = $query->latest()->get();
        return view('groups.index', compact('groups'));
    }

    public function create()
    {
        $years = Year::all();
        return view('groups.create', compact('years'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'group_name' => 'required|string|max:255',
            'year' => 'required|string|max:10',
        ]);

        $yearRecord = Year::firstOrCreate(['year' => $request->year]);

        Group::create([
            'group_name' => $request->group_name,
            'year_id' => $yearRecord->id,
        ]);

        return redirect()->route('groups.index')->with('success', 'Group created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        // ✅ FIXED: Added 'timetables.day' and 'timetables.teacher'
        $group->load(['year', 'students.quizAttempts', 'timetables.subject', 'timetables.day', 'timetables.teacher']);

        // --- Analytics Logic ---
        $totalGroupScore = 0;
        $studentCountWithData = 0;
        $allAttempts = collect(); 

        foreach ($group->students as $student) {
            $attempts = $student->quizAttempts;
            if ($attempts->count() > 0) {
                $avg = $attempts->avg('score');
                $student->average_score = round($avg);
                
                if ($avg >= 80) $student->status = 'High Achiever';
                elseif ($avg < 40) $student->status = 'Needs Attention';
                else $student->status = 'Normal';

                $totalGroupScore += $avg;
                $studentCountWithData++;

                foreach($attempts as $att) {
                    $allAttempts->push(['score' => $att->score, 'created_at' => $att->created_at->timestamp]);
                }
            } else {
                $student->average_score = 0;
                $student->status = 'New';
            }
        }

        $groupMastery = $studentCountWithData > 0 ? round($totalGroupScore / $studentCountWithData) : 0;

        $slope = 0;
        if ($allAttempts->count() > 1) {
            $sortedAttempts = $allAttempts->sortBy('created_at')->values();
            $n = $sortedAttempts->count();
            $x = range(1, $n);
            $y = $sortedAttempts->pluck('score')->toArray();
            $sumX = array_sum($x); $sumY = array_sum($y); $sumXY = 0; $sumXX = 0;
            for ($i = 0; $i < $n; $i++) { $sumXY += ($x[$i] * $y[$i]); $sumXX += ($x[$i] * $x[$i]); }
            $denominator = ($n * $sumXX - $sumX * $sumX);
            if ($denominator != 0) { $slope = ($n * $sumXY - $sumX * $sumY) / $denominator; }
        }

        return view('groups.show', compact('group', 'groupMastery', 'slope'));
    }

    public function edit(Group $group)
    {
        $years = Year::all();
        $group->load('year'); 
        return view('groups.edit', compact('group', 'years'));
    }

    public function update(Request $request, Group $group)
    {
        $request->validate([
            'group_name' => 'required|string|max:255',
            'year' => 'required|string|max:10',
        ]);

        $yearRecord = Year::firstOrCreate(['year' => $request->year]);

        $group->update([
            'group_name' => $request->group_name,
            'year_id' => $yearRecord->id,
        ]);

        return redirect()->route('groups.index')->with('success', 'Group updated successfully!');
    }

    public function destroy(Group $group)
    {
        $group->delete();
        return redirect()->route('groups.index')->with('success','Group deleted successfully');
    }
}