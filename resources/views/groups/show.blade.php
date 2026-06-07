@extends('admin.adminhome')

@section('content')
<div class="container-fluid">
    {{-- HEADER CARD --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card bg-maroon text-white border-0 shadow" style="background-color: #800000;">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold mb-0">{{ $group->group_name }}</h2>
                        <p class="mb-0 opacity-75">Academic Session: {{ $group->year->year ?? 'N/A' }}</p>
                    </div>
                    <div class="text-end position-absolute top-right-btn">
                        <a href="{{ route('groups.index') }}" class="btn btn-outline-light btn-sm">Back to Groups</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        {{-- LEFT: STUDENTS LIST --}}
        <div class="col-md-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white fw-bold border-bottom py-3">
                    <i class="fas fa-user-graduate me-2 text-primary"></i>Students Enrolled
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th class="ps-3">Name</th>
                                <th>Email</th>
                                <th class="text-center">Avg Score</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($group->students as $student)
                            @php
                                $statusColor = 'secondary';
                                if($student->status == 'High Achiever') $statusColor = 'success';
                                if($student->status == 'Needs Attention') $statusColor = 'danger';
                                if($student->status == 'Normal') $statusColor = 'primary';
                            @endphp
                            <tr>
                                <td class="ps-3 fw-bold">{{ $student->name }}</td>
                                <td>{{ $student->email }}</td>
                                <td class="text-center fw-bold">{{ $student->average_score }}%</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $statusColor }} bg-opacity-75">
                                        {{ $student->status }}
                                    </span> 
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-4 text-muted">No students assigned yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- RIGHT: GROUP ANALYTICS --}}
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white fw-bold">Group Analytics Summary</div>
                <div class="card-body">
                    {{-- TOTAL STUDENTS --}}
                    <div class="mb-3 text-center">
                        <h1 class="display-4 fw-bold text-primary">{{ $group->students->count() }}</h1>
                        <p class="text-muted small">Total Students</p>
                    </div>
                    <hr>
                    
                    {{-- OVERALL MASTERY --}}
                    <label class="small fw-bold">Overall Group Mastery</label>
                    <div class="d-flex justify-content-between small mb-1">
                        <span>Average Score</span>
                        <span class="fw-bold">{{ $groupMastery }}%</span>
                    </div>
                    <div class="progress mb-4" style="height: 10px;">
                        <div class="progress-bar" role="progressbar" style="width: {{ $groupMastery }}%; background-color: #800000;"></div>
                    </div>

                    {{-- TREND INSIGHT --}}
                    <div class="alert {{ $slope >= 0 ? 'alert-success' : 'alert-warning' }} border-0 small mb-0">
                        <i class="fas {{ $slope >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }} me-2"></i>
                        @if($slope > 0)
                            Positive trend: <strong>+{{ number_format($slope, 2) }}</strong>
                        @elseif($slope < 0)
                            Declining: <strong>{{ number_format($slope, 2) }}</strong>
                        @else
                            Performance stable.
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ CORRECT TIMETABLE SECTION --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold text-dark"><i class="far fa-calendar-alt me-2 text-maroon"></i>Class Schedule</h5>
                </div>
                <div class="card-body p-4 overflow-auto">
                    
                    {{-- Prepare Data: Group the flat timetable collection by Day Name --}}
                    @php 
                        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
                        $shortDays = ['MON', 'TUE', 'WED', 'THU', 'FRI'];
                        $timeslots = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00'];
                        
                        // Group schedules by Day Name
                        $groupedSchedules = $group->timetables->groupBy(function($item) {
                            return $item->day->day_name;
                        });
                    @endphp

                    <div class="timetable-grid">
                        
                        {{-- 1. TOP HEADER ROW --}}
                        <div class="text-center fw-bold text-muted d-flex align-items-end justify-content-center pb-2">
                            <small>DAY / TIME</small>
                        </div>
                        
                        @foreach($timeslots as $index => $time)
                            <div class="grid-header-time shadow-sm">
                                <span class="period-num">{{ $index + 1 }}</span>
                                <span class="period-time">{{ $time }}</span>
                            </div>
                        @endforeach

                        {{-- 2. MAIN LOOP: DAYS --}}
                        @foreach($days as $dayIndex => $dayName)
                            
                            {{-- Day Label --}}
                            <div class="day-label-cell">
                                {{ $shortDays[$dayIndex] }}
                            </div>
                            
                            @php 
                                $slotsToSkip = 0;
                                // Get only schedules for this specific day
                                $daySchedules = $groupedSchedules->get($dayName, collect());
                            @endphp

                            {{-- Inner Loop: Times --}}
                            @foreach($timeslots as $time)
                            
                                @if($slotsToSkip > 0)
                                    @php $slotsToSkip--; @endphp
                                    @continue
                                @endif

                                @php
                                    // Check for lesson starting at this exact time (compare Hour)
                                    $currentLesson = $daySchedules->first(function($item) use ($time) {
                                        return date('H', strtotime($item->time_from)) == date('H', strtotime($time));
                                    });

                                    $colSpan = 1; 
                                    
                                    if($currentLesson) {
                                        $start = \Carbon\Carbon::parse($currentLesson->time_from);
                                        $end   = \Carbon\Carbon::parse($currentLesson->time_to); 
                                        $hours = $end->diffInHours($start); 
                                        
                                        $colSpan = $hours > 0 ? $hours : 1; 
                                        $slotsToSkip = $colSpan - 1; 
                                    }
                                @endphp

                                {{-- Render Cell --}}
                                <div class="cell" style="grid-column: span {{ $colSpan }};">
                                    @if($currentLesson)
                                        <div class="lesson-card">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="subject-name text-truncate" title="{{ $currentLesson->subject->subject_name }}">
                                                    {{ $currentLesson->subject->subject_name }}
                                                </div>
                                                @if($colSpan > 1)
                                                    <span class="badge bg-warning text-dark shadow-sm" style="font-size: 0.6rem;">{{ $colSpan }}H</span>
                                                @endif
                                            </div>
                                            
                                            <div class="teacher-name text-truncate">
                                                <i class="fas fa-chalkboard-teacher me-1 text-muted"></i> 
                                                {{ $currentLesson->teacher->name ?? 'Unknown Teacher' }}
                                            </div>
                                            
                                            <div class="text-muted small mt-2 pt-2 border-top" style="font-size: 0.65rem;">
                                                {{ date('h:i A', strtotime($currentLesson->time_from)) }} - {{ date('h:i A', strtotime($currentLesson->time_to)) }}
                                            </div>
                                        </div>
                                    @endif
                                </div>

                            @endforeach {{-- End Time --}}
                        @endforeach {{-- End Day --}}

                    </div>

                </div>
            </div>
        </div>
    </div>

</div>

<style>
    /* VARIABLES */
    :root { --deep-maroon: #4a0000; --accent-gold: #c5a059; }

    /* TIMETABLE GRID CSS (Same as Timetable View) */
    .timetable-grid {
        display: grid;
        grid-template-columns: 80px repeat(11, 1fr); 
        gap: 8px;
        overflow-x: auto; 
        padding-bottom: 20px; 
    }

    .grid-header-time {
        background: var(--deep-maroon); color: white;
        text-align: center; padding: 8px; border-radius: 8px;
        display: flex; flex-direction: column; justify-content: center;
    }
    .period-num { font-size: 1.2rem; font-weight: 800; color: var(--accent-gold); line-height: 1; }
    .period-time { font-size: 0.65rem; font-weight: 600; text-transform: uppercase; margin-top: 4px; }

    .day-label-cell {
        display: flex; align-items: center; justify-content: center;
        background: #f1f5f9; color: var(--deep-maroon);
        font-weight: 900; font-size: 1.5rem; border-radius: 12px;
        border: 2px solid #e2e8f0; height: 100%;
    }

    .cell {
        background: #f8fafc; border-radius: 12px; min-height: 110px;
        border: 1px solid #eef2f6; padding: 4px;
    }

    .lesson-card {
        background: white; border-radius: 8px; padding: 8px; height: 100%;
        border-left: 4px solid var(--deep-maroon);
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        display: flex; flex-direction: column; justify-content: center;
        transition: transform 0.2s;
    }
    .lesson-card:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }

    .subject-name { font-size: 0.85rem; font-weight: 800; color: #1e293b; line-height: 1.2; margin-bottom: 4px; }
    .teacher-name { font-size: 0.7rem; color: #64748b; font-weight: 600; }
    .text-maroon { color: #800000; }
</style>
@endsection