@extends('users.students')

@section('content')
<style>
    /* --- 1. Today's Current Schedule (Preferred Banner) --- */
    .section-divider {
        border-left: 5px solid #008f78;
        padding-left: 15px;
        margin-bottom: 20px;
    }

    .today-banner {
        background-color: #008f78; 
        color: white;
        border-radius: 20px;
        padding: 40px 20px;
        margin-bottom: 40px;
        text-align: center;
    }

    .today-class-item {
        background: rgba(255, 255, 255, 0.15);
        border-radius: 12px;
        padding: 15px 25px;
        margin: 10px auto;
        max-width: 800px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* --- 2. Weekly Grid Styling --- */
    .weekly-wrapper {
        background: white;
        border-radius: 16px;
        overflow-x: auto;
        border: 1px solid #eee;
    }

    .timetable-grid {
        display: grid;
        grid-template-columns: 90px repeat(5, 1fr);
        min-width: 950px;
    }

    .grid-header {
        background: #f8f9fa;
        font-weight: 800;
        font-size: 0.85rem;
        padding: 18px 5px;
        text-align: center;
        border-bottom: 2px solid #eee;
    }

    .grid-cell {
        min-height: 75px;
        padding: 8px;
        border-bottom: 1px solid #f8fafc;
        border-right: 1px solid #f8fafc;
    }

    /* Subject Colors */
    .class-card {
        border-radius: 12px;
        padding: 10px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        text-align: left;
        border-left: 4px solid;
    }
    .color-quran { background: #eef2ff; color: #4338ca; border-color: #4338ca; }
    .color-fiqh { background: #dcfce7; color: #15803d; border-color: #15803d; }
    .color-sirah { background: #ffedd5; color: #c2410c; border-color: #c2410c; }
    .color-akhlak { background: #f3e8ff; color: #7e22ce; border-color: #7e22ce; }
    .color-akidah { background: #cffafe; color: #0e7490; border-color: #0e7490; }

    /* --- 3. PRINTING LOGIC --- */
    .print-only-header { display: none; }

    @media print {
        @page { size: landscape; margin: 0.5cm; }
        
        body * { visibility: hidden; }
        #printableWeekly, #printableWeekly * { visibility: visible; }
        
        #printableWeekly {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        .print-only-header {
            display: block !important;
            margin-bottom: 20px;
            border-bottom: 2px solid #008f78;
            padding-bottom: 10px;
        }

        .grid-cell { min-height: 48px; } /* Fits 8am-6pm on one page */
        .no-print { display: none !important; }
    }
</style>

<div class="container-fluid p-0 pb-5">
    
    <div class="mb-4 d-flex justify-content-between align-items-center text-start no-print">
        <div>
            <h2 class="fw-bold text-dark mb-1">My Timetable</h2>
            <p class="text-muted small">Class: <span class="text-success fw-bold">{{ $groupName }}</span></p>
        </div>
        <button onclick="window.print()" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">
            <i class="fas fa-print me-2"></i> Save PDF
        </button>
    </div>

    {{-- WEB VIEW: Today's Schedule --}}
    <div class="no-print">
        <div class="section-divider text-start">
            <h4 class="fw-bold text-dark m-0">Today's Current Schedule</h4>
        </div>

        <div class="today-banner shadow-sm">
            @forelse($todayClasses as $class)
                <div class="today-class-item">
                    <div class="text-start">
                        <div class="fw-bold fs-5">{{ $class->subject->subject_name }}</div>
                        <small style="opacity: 0.8;">{{ $class->teacher->name }}</small>
                    </div>
                    <div class="fw-bold fs-4">{{ \Carbon\Carbon::parse($class->time_from)->format('H:i') }}</div>
                </div>
            @empty
                <div class="py-2 text-center">
                    <i class="fas fa-mug-hot fa-3x mb-3 opacity-50"></i>
                    <h5 class="fw-bold">No classes scheduled for today. Enjoy your break!</h5>
                </div>
            @endforelse
        </div>
    </div>

    {{-- PRINTABLE AREA: Weekly Grid --}}
    <div id="printableWeekly">
        
        {{-- ✅ ADDED: STUDENT INFO FOR PRINT --}}
        <div class="print-only-header text-start">
            <div class="row">
                <div class="col-6">
                    <h3 class="fw-bold text-dark mb-0">OFFICIAL CLASS TIMETABLE</h3>
                    <p class="text-muted mb-0">Generated for: <strong>{{ Auth::user()->name }}</strong></p>
                </div>
                <div class="col-6 text-end">
                    <h5 class="fw-bold text-success mb-0">Class: {{ $groupName }}</h5>
                    <p class="text-muted mb-0">Academic Year: {{ $currentYear }}</p>
                </div>
            </div>
        </div>

        <div class="section-divider text-start no-print">
            <h4 class="fw-bold text-dark m-0">Full Weekly Schedule</h4>
            <p class="text-muted small">Academic overview from 08:00 AM to 06:00 PM</p>
        </div>
        
        <div class="weekly-wrapper">
            <div class="timetable-grid">
                <div class="grid-header">Time</div>
                @foreach(['MON', 'TUE', 'WED', 'THU', 'FRI'] as $dayLabel)
                    <div class="grid-header">{{ $dayLabel }}</div>
                @endforeach

                {{-- Time slots until 6:00 PM (18:00) --}}
                @php 
                    $slots = ['08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00', '18:00']; 
                @endphp

                @foreach($slots as $slot)
                    <div class="grid-cell d-flex align-items-center justify-content-center" 
                         style="background:#fdfdfd; font-weight: bold; color: #94a3b8; font-size: 0.75rem; border-right: 1px dashed #eee;">
                        {{ $slot }}
                    </div>
                    @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $day)
                        <div class="grid-cell">
                            @php
                                $class = $weeklySchedule[$day]->first(fn($item) => 
                                    \Carbon\Carbon::parse($item->time_from)->format('H:00') == $slot);
                            @endphp
                            @if($class)
                                @php
                                    $sub = strtolower($class->subject->subject_name ?? '');
                                    $theme = 'color-quran';
                                    if(str_contains($sub, 'fiqh')) $theme = 'color-fiqh';
                                    elseif(str_contains($sub, 'sirah')) $theme = 'color-sirah';
                                    elseif(str_contains($sub, 'akhlak')) $theme = 'color-akhlak';
                                    elseif(str_contains($sub, 'akidah')) $theme = 'color-akidah';
                                @endphp
                                <div class="class-card {{ $theme }}">
                                    <div style="font-weight: 800; font-size: 0.78rem;">{{ $class->subject->subject_name }}</div>
                                    <div style="font-size: 0.65rem; opacity: 0.8;">{{ $class->teacher->name }}</div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection