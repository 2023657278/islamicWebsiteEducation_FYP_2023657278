<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Group;
use App\Models\Resources;
use App\Models\Timetable;
use Carbon\Carbon;

class AdminRealController extends Controller
{
    public function index()
    {
        // 1. Core Platform Telemetry
        $stats = [
            'students'  => User::where('role', 'student')->count(),
            'teachers'  => User::where('role', 'teacher')->count(),
            'groups'    => Group::count(),
            'resources' => Resources::count(),
            'timetables'=> Timetable::count(),
        ];

        // 2. Dynamic Platform Ratios (No maximum limits!)
        $totalUsers = $stats['students'] + $stats['teachers'];
        
        $ratios = [
            'student_percentage' => $totalUsers > 0 ? round(($stats['students'] / $totalUsers) * 100, 1) : 0,
            'teacher_percentage' => $totalUsers > 0 ? round(($stats['teachers'] / $totalUsers) * 100, 1) : 0,
            'average_students_per_class' => $stats['groups'] > 0 ? round($stats['students'] / $stats['groups'], 1) : 0,
        ];

        // 3. System Activity Telemetry (Simulated or via dynamic data tracking)
        $systemMetrics = [
            // Change this line temporarily:
            'active_sessions' => User::where('created_at', '>=', now()->subDays(7))->count(),
            'telegram_linked' => User::whereNotNull('telegram_chat_id')->count(),
        ];

        return view('adminreal.dashboard', compact('stats', 'ratios', 'systemMetrics'));
    }
}