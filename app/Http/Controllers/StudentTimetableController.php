<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Timetable;
use Carbon\Carbon;

class StudentTimetableController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // ---------------------------------------------------------
        // 1. GET STUDENT DETAILS (Group & Year)
        // ---------------------------------------------------------
        
        // ✅ FIX 1: Use correct column 'group_name' based on your DB structure
        $groupName = ($user->group) ? $user->group->group_name : 'No Class Assigned';
        
        // Get Current Year (e.g., "2026")
        $currentYear = Carbon::now()->year;


        // ---------------------------------------------------------
        // 2. FETCH TIMETABLE DATA
        // ---------------------------------------------------------
        
        // Get the current day name (e.g., "Monday")
        $todayName = Carbon::now()->format('l');

        // Fetch ALL classes for this student's group, eager loading relationships
        $allClasses = Timetable::with(['subject', 'teacher', 'day'])
                        ->where('group_id', $user->group_id)
                        ->orderBy('time_from')
                        ->get();

        // ✅ FIX 2: Filter for TODAY'S classes using 'day_name'
        $todayClasses = $allClasses->filter(function ($class) use ($todayName) {
            // Ensure day relationship exists, then check name
            return $class->day && $class->day->day_name === $todayName;
        });

        // ✅ FIX 3: Group classes by Day using 'day.day_name'
        $weeklySchedule = [
            'Monday'    => $allClasses->where('day.day_name', 'Monday'),
            'Tuesday'   => $allClasses->where('day.day_name', 'Tuesday'),
            'Wednesday' => $allClasses->where('day.day_name', 'Wednesday'),
            'Thursday'  => $allClasses->where('day.day_name', 'Thursday'),
            'Friday'    => $allClasses->where('day.day_name', 'Friday'),
        ];

        // Pass all data to the view
        return view('users.timetable.index', compact('todayClasses', 'weeklySchedule', 'groupName', 'currentYear'));
    }
}