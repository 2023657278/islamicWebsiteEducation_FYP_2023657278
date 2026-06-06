<?php

namespace App\Http\Controllers;

use App\Models\Timetable;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Subject;
use App\Models\Day;
use App\Models\Year;
use App\Models\Group;

class TimetableController extends Controller
{
    public function index(Request $request)
    {
        $query = Timetable::with(['teacher', 'group.year', 'subject', 'day']);

        // 1. Search by Class Name (Group Name)
        if ($request->filled('group_name')) {
            $query->whereHas('group', function($q) use ($request) {
                $q->where('group_name', 'LIKE', "%{$request->group_name}%");
            });
        }

        // 2. Search by Academic Year
        if ($request->filled('academic_year')) {
            $query->whereHas('group.year', function($q) use ($request) {
                $q->where('year', 'LIKE', "%{$request->academic_year}%");
            });
        }

        // Existing filters...
        if ($request->filled('teacher')) {
            $query->whereHas('teacher', fn($q) => $q->where('name', 'LIKE', "%{$request->teacher}%"));
        }
        if ($request->filled('subject')) {
            $query->whereHas('subject', fn($q) => $q->where('subject_name', 'LIKE', "%{$request->subject}%"));
        }
        if ($request->filled('day')) {
            $query->whereHas('day', fn($q) => $q->where('day_name', 'LIKE', "%{$request->day}%"));
        }

        $timetables = $query->latest()->get();
        return view('timetables.index', compact('timetables'));
    }

    public function create()
    {
        $teachers = User::where('role', 'teacher')->get();
        $subjects = Subject::all();
        $days = Day::all();
        $groups = Group::with('year')->get(); 

        return view('timetables.create', compact('teachers', 'subjects', 'days', 'groups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required',
            'group_id' => 'required|exists:groups,id',
            'subject_id' => 'required',
            'day_id' => 'required',
            'time_from' => 'required',
            'time_to' => 'required|after:time_from',
        ]);

        $group = Group::findOrFail($request->group_id);
        
        // 1. Check TEACHER Overlap
        $teacherClash = Timetable::where('teacher_id', $request->teacher_id)
            ->where('day_id', $request->day_id)
            ->where('time_from', '<', $request->time_to)
            ->where('time_to', '>', $request->time_from)
            ->exists();

        if ($teacherClash) {
            return back()->withErrors(['msg' => 'Teacher Clash: This teacher is already teaching another class at this time.'])->withInput();
        }

        // 2. Check GROUP Overlap
        $groupClash = Timetable::where('group_id', $request->group_id)
            ->where('day_id', $request->day_id)
            ->where('time_from', '<', $request->time_to)
            ->where('time_to', '>', $request->time_from)
            ->exists();

        if ($groupClash) {
            return back()->withErrors(['msg' => 'Class Clash: This class group already has a subject at this time.'])->withInput();
        }

        Timetable::create([
            'teacher_id' => $request->teacher_id,
            // 'user_id' removed here
            'group_id' => $request->group_id,
            'subject_id' => $request->subject_id,
            'day_id' => $request->day_id,
            'year_id' => $group->year_id,
            'time_from' => $request->time_from,
            'time_to' => $request->time_to,
        ]);

        return redirect()->route('timetables.index')->with('success', 'Assignment Created Successfully');
    }

    public function show(Timetable $timetable)
    {
        $allSchedules = Timetable::with(['teacher', 'subject', 'day'])
            ->where('group_id', $timetable->group_id)
            ->where('year_id', $timetable->year_id)
            ->get()
            ->groupBy('day.day_name');

        return view('timetables.show', compact('timetable', 'allSchedules'));
    }

    public function edit(Timetable $timetable)
    {
        $teachers = User::where('role', 'teacher')->get();
        $subjects = Subject::all();
        $days = Day::all();
        $years = Year::all();
        $groups = Group::with('year')->get();
        
        return view('timetables.edit', compact('timetable', 'teachers', 'subjects', 'days', 'years', 'groups'));
    }

    public function update(Request $request, $id)
    {
        // 1. Fetch current record
        $timetable = Timetable::findOrFail($id);

        // 2. Validate inputs
        $request->validate([
            'teacher_id' => 'required',
            'group_id'   => 'required',
            'subject_id' => 'required',
            'day_id'     => 'required',
            'time_from'  => 'required',
            'time_to'    => 'required|after:time_from',
        ]);

        // 3. OVERLAP CHECK (Excluding the current ID)
        $overlap = Timetable::where('day_id', $request->day_id)
            ->where('id', '!=', $id) // Vital: Don't check against self
            ->where(function($query) use ($request) {
                // Check if Teacher OR Group is busy
                $query->where('teacher_id', $request->teacher_id)
                      ->orWhere('group_id', $request->group_id);
            })
            ->where(function($query) use ($request) {
                // Standard Overlap Formula
                $query->where('time_from', '<', $request->time_to)
                      ->where('time_to', '>', $request->time_from);
            })
            ->exists();

        if ($overlap) {
            return back()->withErrors(['msg' => 'Conflict Error: The selected Teacher OR Class Group is already booked during this time slot.'])->withInput();
        }

        // 4. Update
        $group = Group::findOrFail($request->group_id);
        
        $timetable->update([
            'teacher_id' => $request->teacher_id,
            // 'user_id' removed here
            'group_id'   => $request->group_id,
            'subject_id' => $request->subject_id,
            'day_id'     => $request->day_id,
            'year_id'    => $group->year_id, // Ensure Year ID updates if group changes
            'time_from'  => $request->time_from,
            'time_to'    => $request->time_to,
        ]);

        return redirect()->route('timetables.index')->with('success', 'Assignment updated successfully!');
    }

    public function destroy(Timetable $timetable)
    {
        $timetable->delete();
        return redirect()->route('timetables.index')->with('success','Schedule deleted successfully');
    }

    /**
     * 🟢 TWO-TIER RESET SAFETIES: Wipes out all rows inside the 
     * timetables table after verifying the strict passphrase.
     */
    public function resetAll(Request $request)
    {
        // 1. Verify that the form field was sent
        $request->validate([
            'confirmation_text' => 'required|string'
        ]);

        // 2. Air-tight string parameter check
        if ($request->confirmation_text !== 'RESET TIMETABLE') {
            return back()->withErrors(['msg' => 'Security Verification Failed: The typed validation phrase does not match. Operation aborted.']);
        }

        try {
            // 3. Clean truncate execution command
            Timetable::truncate();

            return redirect()->route('adminreal.dashboard')->with('success', 'System Reset Successful: All active timetables have been completely purged.');
        } catch (\Exception $e) {
            return back()->withErrors(['msg' => 'Database Execution Failure: ' . $e->getMessage()]);
        }
    }
}