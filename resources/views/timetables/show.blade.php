@extends('admin.adminhome')

@section('content')
<style>
    /* --- THEME VARIABLES --- */
    :root { 
        --deep-maroon: #4a0000; 
        --accent-gold: #c5a059; 
        --glass-white: rgba(255, 255, 255, 0.95); 
    }
    
    .main-wrapper { 
        background-color: var(--deep-maroon); 
        min-height: 100vh; 
        padding: 2rem; 
        background-image: radial-gradient(circle at top right, rgba(128,0,0,0.4), transparent); 
    }

    .glass-card { 
        background: var(--glass-white); 
        border-radius: 30px; 
        box-shadow: 0 40px 80px rgba(0,0,0,0.4); 
        padding: 30px; 
    }

    /* --- GRID LAYOUT --- */
    /* UPDATED: 1 Column for Day Label + 11 Columns for Time Slots (08:00 to 18:00) */
    .timetable-grid {
        display: grid;
        grid-template-columns: 80px repeat(11, 1fr); 
        gap: 8px;
        overflow-x: auto; 
        padding-bottom: 20px; 
    }

    /* Top Row: Time Headers */
    .grid-header-time {
        background: var(--deep-maroon);
        color: white;
        text-align: center;
        padding: 8px;
        border-radius: 8px;
        margin-bottom: 5px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .period-num { font-size: 1.2rem; font-weight: 800; color: var(--accent-gold); line-height: 1; }
    .period-time { font-size: 0.65rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin-top: 4px; }

    /* Left Column: Day Labels (Mo, Tu, We...) */
    .day-label-cell {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        color: var(--deep-maroon);
        font-weight: 900;
        font-size: 1.8rem;
        border-radius: 15px;
        border: 2px solid #e2e8f0;
        box-shadow: inset 0 0 10px rgba(0,0,0,0.05);
        height: 100%; /* Fill height */
    }

    /* The Lesson Cell container */
    .cell {
        background: #f8fafc;
        border-radius: 12px;
        min-height: 110px;
        border: 1px solid #eef2f6;
        padding: 4px;
        position: relative;
    }

    /* The content inside the cell */
    .lesson-card {
        background: white;
        border-radius: 8px;
        padding: 8px;
        height: 100%;
        border-left: 4px solid var(--deep-maroon);
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        justify-content: center;
        transition: transform 0.2s;
    }
    .lesson-card:hover { transform: translateY(-2px); }

    /* Highlight for the specific timetable being viewed */
    .highlight {
        border: 2px solid var(--accent-gold);
        background: #fffdf5;
        box-shadow: 0 0 15px rgba(197, 160, 89, 0.3);
        z-index: 10;
        transform: scale(1.02);
    }

    .subject-name { font-size: 0.8rem; font-weight: 800; color: #1e293b; line-height: 1.2; }
    .teacher-name { font-size: 0.65rem; color: #64748b; font-weight: 600; margin-top: 4px; }
    
    /* Back Button Style */
    .btn-back {
        background: var(--deep-maroon);
        color: white;
        border-radius: 50px;
        padding: 8px 20px;
        font-weight: 600;
        transition: 0.3s;
    }
    .btn-back:hover { background: #000; color: white; transform: translateX(-5px); }
</style>

<div class="main-wrapper">
    <div class="container-fluid">
        <div class="glass-card">
            
            {{-- HEADER SECTION --}}
            <div class="d-flex justify-content-between align-items-center mb-5 pb-3 border-bottom">
                <div>
                    <h2 class="fw-bold mb-0" style="color: var(--deep-maroon);">{{ $timetable->group->group_name }}</h2>
                    <p class="text-muted fw-bold small m-0 uppercase">
                        <span style="color: var(--accent-gold)">MASTER SCHEDULE</span> • Session {{ $timetable->group->year->year }}
                    </p>
                </div>
                <a href="{{ route('timetables.index') }}" class="btn btn-back shadow-sm text-decoration-none">
                    <i class="fas fa-chevron-left me-2"></i> Return
                </a>
            </div>

            {{-- DATA PREPARATION --}}
            @php 
                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
                $shortDays = ['Mo', 'Tu', 'We', 'Th', 'Fr'];
                // UPDATED: Added '18:00' to the array below
                $timeslots = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00'];
                
                // Fetch all schedules for this group
                $schedules = \App\Models\Timetable::where('group_id', $timetable->group_id)
                    ->with(['subject','teacher','day'])
                    ->get();
            @endphp

            {{-- TIMETABLE GRID START --}}
            <div class="timetable-grid">
                
                {{-- 1. TOP HEADER ROW (Corner + Times) --}}
                <div class="text-center fw-bold text-muted d-flex align-items-end justify-content-center pb-2">
                    <small>Day/Time</small>
                </div>
                
                @foreach($timeslots as $index => $time)
                    <div class="grid-header-time shadow-sm">
                        <span class="period-num">{{ $index + 1 }}</span>
                        <span class="period-time">{{ $time }}</span>
                    </div>
                @endforeach

                {{-- 2. MAIN LOOP: ITERATE THROUGH DAYS (ROWS) --}}
                @foreach($days as $dayIndex => $day)
                    
                    {{-- Left Column: Day Label --}}
                    <div class="day-label-cell">
                        {{ $shortDays[$dayIndex] }}
                    </div>
                    
                    @php 
                        $slotsToSkip = 0; // Reset counter for the new row
                    @endphp

                    {{-- Inner Loop: Iterate through Times (Columns) --}}
                    @foreach($timeslots as $time)
                    
                        {{-- SKIP LOGIC: If a previous class spans over this slot, skip it --}}
                        @if($slotsToSkip > 0)
                            @php $slotsToSkip--; @endphp
                            @continue
                        @endif

                        @php
                            // Check for lesson starting at this exact Day & Time
                            $currentLesson = $schedules->filter(function($item) use ($day, $time) {
                                return $item->day->day_name == $day && 
                                       date('H', strtotime($item->time_from)) == date('H', strtotime($time));
                            })->first();

                            $colSpan = 1; // Default span
                            
                            // If lesson exists, calculate duration
                            if($currentLesson) {
                                $start = \Carbon\Carbon::parse($currentLesson->time_from);
                                $end   = \Carbon\Carbon::parse($currentLesson->time_to); 
                                $hours = $end->diffInHours($start); 
                                
                                // Set span (minimum 1)
                                $colSpan = $hours > 0 ? $hours : 1; 
                                
                                // Set how many FUTURE slots to skip
                                $slotsToSkip = $colSpan - 1; 
                            }
                        @endphp

                        {{-- RENDER THE CELL --}}
                        <div class="cell" style="grid-column: span {{ $colSpan }};">
                            @if($currentLesson)
                                <div class="lesson-card {{ $currentLesson->id == $timetable->id ? 'highlight' : '' }}">
                                    
                                    {{-- Lesson Header: Subject + Duration Badge --}}
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="subject-name">{{ $currentLesson->subject->subject_name }}</div>
                                        @if($colSpan > 1)
                                            <span class="badge bg-warning text-dark" style="font-size: 0.6rem;">{{ $colSpan }} Hrs</span>
                                        @endif
                                    </div>
                                    
                                    {{-- Teacher Name --}}
                                    <div class="teacher-name text-truncate">
                                        <i class="fas fa-user-tie me-1"></i> {{ $currentLesson->teacher->name }}
                                    </div>
                                    
                                    {{-- Time Range --}}
                                    <div class="text-muted small mt-1" style="font-size: 0.65rem;">
                                        {{ date('h:i', strtotime($currentLesson->time_from)) }} - {{ date('h:i', strtotime($currentLesson->time_to)) }}
                                    </div>
                                </div>
                            @endif
                        </div>

                    @endforeach {{-- End Time Loop --}}
                @endforeach {{-- End Day Loop --}}

            </div>
            
            <div class="mt-4 text-center text-muted small fw-bold">
                <i class="fas fa-school me-1"></i> Official School Timetable Layout
            </div>

        </div>
    </div>
</div>
@endsection