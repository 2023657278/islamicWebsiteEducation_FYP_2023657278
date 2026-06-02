@extends(auth()->user()->role === 'admin' ? 'adminreal.master' : 'admin.adminhome')

@section('content')
<style>
    /* --- TIMETABLE CSS --- */
    :root { --deep-maroon: #4a0000; --accent-gold: #c5a059; }
    .timetable-grid { display: grid; grid-template-columns: 80px repeat(11, 1fr); gap: 6px; overflow-x: auto; padding-bottom: 15px; }
    .grid-header-time { background: var(--deep-maroon); color: white; text-align: center; padding: 5px; border-radius: 6px; font-size: 0.7rem; }
    .day-label-cell { display: flex; align-items: center; justify-content: center; background: #f1f5f9; color: var(--deep-maroon); font-weight: 800; font-size: 0.9rem; border-radius: 8px; }
    .cell { background: #f8fafc; border-radius: 8px; min-height: 80px; border: 1px solid #eef2f6; padding: 2px; }
    .lesson-card { background: white; border-radius: 6px; padding: 6px; height: 100%; border-left: 3px solid var(--accent-gold); box-shadow: 0 2px 4px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: center; font-size: 0.75rem; }
    .subject-name { font-weight: 800; color: #1e293b; line-height: 1.1; margin-bottom: 3px; }
    .location-name { color: #64748b; font-weight: 600; font-size: 0.7rem; }
</style>

<div class="container-fluid">
    {{-- HEADER WITH BACK BUTTON --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">Teacher Profile</h2>
            <p class="text-muted">Administration & Performance Overview</p>
        </div>
        <a href="{{ route('teachers.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
    </div>

    <div class="row">
        {{-- LEFT COLUMN: PROFILE --}}
        <div class="col-md-3">
            <div class="card shadow-sm mb-4" style="border-top: 5px solid #800000;">
                <div class="card-body text-center">
                    <i class="fas fa-chalkboard-teacher fa-4x text-muted mb-3"></i>
                    <h4 class="fw-bold">{{ $teacher->name }}</h4>
                    <p class="text-muted small">{{ $teacher->email }}</p>
                    <hr>
                    <div class="text-start">
                        <p class="small mb-2"><strong>Phone:</strong> {{ $teacher->phone_number ?? 'N/A' }}</p>
                        <p class="small mb-2"><strong>Role:</strong> Teacher</p>
                        <p class="small mb-0"><strong>Joined:</strong> {{ $teacher->created_at->format('M Y') }}</p>
                    </div>
                </div>
            </div>

            {{-- OVERALL RATING CARD --}}
            <div class="card shadow-sm border-0 bg-dark text-white">
                <div class="card-body text-center">
                    <h6 class="opacity-75 text-uppercase small">Average Class Rating</h6>
                    <h1 class="fw-bold text-warning mb-0">{{ round($overallTeacherRating, 1) }}%</h1>
                    <small class="opacity-75">Based on student quiz results</small>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: SCHEDULE & STATS --}}
        <div class="col-md-9">
            
            {{-- 1. WEEKLY SCHEDULE --}}
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-calendar-alt me-2 text-primary"></i>Weekly Schedule</h5>
                </div>
                <div class="card-body">
                    @php 
                        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
                        $shortDays = ['Mo', 'Tu', 'We', 'Th', 'Fr'];
                        $timeslots = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00'];
                    @endphp

                    <div class="timetable-grid">
                        <div class="text-center small text-muted"></div>
                        @foreach($timeslots as $time)
                            <div class="grid-header-time">{{ $time }}</div>
                        @endforeach

                        @foreach($days as $dayIndex => $day)
                            <div class="day-label-cell">{{ $shortDays[$dayIndex] }}</div>
                            @php $slotsToSkip = 0; @endphp

                            @foreach($timeslots as $time)
                                @if($slotsToSkip > 0)
                                    @php $slotsToSkip--; @endphp
                                    @continue
                                @endif

                                @php
                                    $currentLesson = $schedules->filter(function($item) use ($day, $time) {
                                        return $item->day->day_name == $day && 
                                                date('H', strtotime($item->time_from)) == date('H', strtotime($time));
                                    })->first();

                                    $colSpan = 1;
                                    if($currentLesson) {
                                        $start = \Carbon\Carbon::parse($currentLesson->time_from);
                                        $end   = \Carbon\Carbon::parse($currentLesson->time_to); 
                                        $colSpan = max(1, $end->diffInHours($start)); 
                                        $slotsToSkip = $colSpan - 1; 
                                    }
                                @endphp

                                <div class="cell" style="grid-column: span {{ $colSpan }};">
                                    @if($currentLesson)
                                        <div class="lesson-card">
                                            <div class="subject-name">{{ $currentLesson->subject->subject_name }}</div>
                                            <div class="location-name">
                                                <i class="fas fa-users me-1"></i> {{ $currentLesson->group->group_name }}
                                            </div>
                                            <div class="text-muted small" style="font-size:0.6rem; margin-top:2px;">
                                                {{ date('H:i', strtotime($currentLesson->time_from)) }} - {{ date('H:i', strtotime($currentLesson->time_to)) }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- 2. DYNAMIC GROUP PERFORMANCE --}}
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Class Performance Metrics</h5>
                </div>
                <div class="card-body bg-light">
                    <div class="row">
                        @forelse($groups as $group)
                            @php
                                // Dynamic Color Logic
                                $color = 'primary';
                                if($group->performance_status == 'Excellent') $color = 'success';
                                if($group->performance_status == 'Critical') $color = 'danger';
                            @endphp

                            <div class="col-md-6 mb-3">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h5 class="fw-bold mb-0 text-dark">{{ $group->group_name }}</h5>
                                            <span class="badge bg-{{ $color }}">{{ $group->performance_status }}</span>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between small text-muted mb-2">
                                            <span>Students: {{ $group->students_count }}</span>
                                            <span>Quizzes: {{ $group->total_activity }}</span>
                                        </div>

                                        <label class="small fw-bold mb-1">Average Score: {{ $group->avg_performance }}%</label>
                                        <div class="progress" style="height: 10px;">
                                            <div class="progress-bar bg-{{ $color }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $group->avg_performance }}%">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center p-5 text-muted">
                                <i class="fas fa-folder-open fa-2x mb-2"></i>
                                <p>No classes assigned to this teacher yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection