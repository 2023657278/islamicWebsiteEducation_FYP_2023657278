@extends('users.students')

@section('content')
<div class="container-fluid p-0 pb-5">

    {{-- HEADER --}}
    <div class="mb-4 text-start">
        <h3 class="fw-bold text-dark">Dashboard</h3>
        <p class="text-muted">Welcome back! Here is your learning overview.</p>
    </div>

    {{-- CALIBRATED LIVE SYSTEM CLOCK CARD --}}
    <div class="card border-0 shadow-sm rounded-4 mb-5 overflow-hidden" style="background: linear-gradient(135deg, #008f78, #00bfa5);">
        <div class="card-body p-4 text-white position-relative text-start">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <p class="text-uppercase small fw-bold opacity-75 mb-1" id="dashTimeLabel">Current System Time</p>
                    <h1 class="fw-bold mb-2" id="dashCurrentTime" style="font-size: 3.5rem; font-family: 'Inter', sans-serif; tracking-wide">00:00:00</h1>
                    
                    <div class="d-inline-flex align-items-center bg-white bg-opacity-25 rounded-pill px-3 py-1">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        <span class="fw-bold small" id="activeZoneDisplay">Melaka (Sg. Udang MRSM)</span>
                    </div>
                </div>
                <div class="col-md-4 text-end d-none d-md-block opacity-25">
                    <i class="far fa-clock fa-7x"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- 1. FULL MENU CARDS --}}
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <a href="{{ route('student.progress.index') }}" class="card border-0 shadow-sm rounded-4 text-decoration-none h-100 hover-scale">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="p-3 rounded-4 me-3 text-white" style="background-color: #2962FF;">
                        <i class="fas fa-chart-pie fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1">Progress</h5>
                        <p class="text-muted small mb-0">View subject analytics</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="{{ route('student.timetable.view') }}" class="card border-0 shadow-sm rounded-4 text-decoration-none h-100 hover-scale">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="p-3 rounded-4 me-3 text-white" style="background-color: #8B1E24;">
                        <i class="far fa-calendar-alt fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1">Timetable</h5>
                        <p class="text-muted small mb-0">Check class schedule</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="{{ route('student.flashcards.index') }}" class="card border-0 shadow-sm rounded-4 text-decoration-none h-100 hover-scale">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="p-3 rounded-4 me-3 text-white" style="background-color: #F59E0B;">
                        <i class="fas fa-layer-group fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1">Flashcards</h5>
                        <p class="text-muted small mb-0">Study & memorize</p>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-md-4">
            <a href="{{ route('student.resources.index') }}" class="card border-0 shadow-sm rounded-4 text-decoration-none h-100 hover-scale">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="p-3 rounded-4 me-3 text-white" style="background-color: #00897B;">
                        <i class="far fa-folder-open fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1">Resources</h5>
                        <p class="text-muted small mb-0">Notes & videos</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="{{ route('student.messages.index') }}" class="card border-0 shadow-sm rounded-4 text-decoration-none h-100 hover-scale">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="p-3 rounded-4 me-3 text-white" style="background-color: #D93025;">
                        <i class="far fa-comment-dots fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1">Messages</h5>
                        <p class="text-muted small mb-0">Chat with teachers</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="{{ route('student.profile.show') }}" class="card border-0 shadow-sm rounded-4 text-decoration-none h-100 hover-scale">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="p-3 rounded-4 me-3 text-white" style="background-color: #C05621;">
                        <i class="far fa-user fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1">Profile</h5>
                        <p class="text-muted small mb-0">Account settings</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- MIDDLE CONTAINER BLOCK --}}
    <div class="row g-4 mb-5">
        
        {{-- AL-FALAH CURRICULUM CORE BOX (HOLDS INTEGRATED ROADMAP TREE DIRECTLY) --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100 position-relative" style="overflow: hidden; background: #ffffff;">
                
                {{-- RADIAL BACKGROUND ART GRID DESIGN --}}
                <div class="roadmap-grid-bg"></div>

                <div class="card-header bg-white border-0 pt-4 px-4 pb-1 text-start position-relative" style="z-index: 10;">
                    <span class="badge rounded-pill bg-primary text-uppercase px-2 py-1 mb-1 shadow-sm" style="font-size:0.6rem; letter-spacing: 0.5px;">Solo Training Center</span>
                    <h5 class="fw-bold text-dark mb-0">Al-Falah Module Solo Roadmap</h5>
                    <small class="text-muted">Winding progress tracking map of active topics. Nodes are locked for security validation logs.</small>
                </div>

                <div class="card-body px-4 pb-4 pt-2 position-relative" style="z-index: 5;">
                    
                    {{-- 🎮 SCROLLABLE INNER CANDY CRUSH ROADMAP (NON-CLICKABLE STATIC PATHWAY) --}}
                    <div class="roadmap-scroll-wrapper">
                        <div class="candy-crush-spine-box" id="candyCrushSpineBox">
                            
                            <svg class="spine-svg-layer">
                                <path id="roadmapSpineVector" d="" fill="none" stroke="#e3e6f0" stroke-width="6" stroke-dasharray="10,10" />
                            </svg>
                            
                            {{-- Flatten and map all quiz topics sequentially across the snaking tree grid track --}}
                            @php $nodeCounter = 0; @endphp
                            @foreach($subjectProgress as $sub)
                                @foreach($sub->quizzes as $quiz)
                                    @php 
                                        $alignmentIndex = $nodeCounter % 4; 
                                        $nodeBgColor = $quiz['is_answered'] ? 'linear-gradient(135deg, #10B981, #059669)' : 'linear-gradient(135deg, #EF4444, #DC2626)';
                                        $shadowColor = $quiz['is_answered'] ? 'rgba(16,185,129,0.35)' : 'rgba(239,68,68,0.35)';
                                        $iconSymbol = $quiz['is_answered'] ? 'fa-check' : 'fa-lock';
                                        $statusText = $quiz['is_answered'] ? 'COMPLETED' : 'LOCKED';
                                        $nodeCounter++;
                                    @endphp

                                    <div class="candy-node-row node-pos-{{ $alignmentIndex }}">
                                        {{-- 🟢 FIXED: Removed any href or click events to make the nodes completely static --}}
                                        <div class="candy-checkpoint-bubble shadow text-white d-node-bubble" 
                                             style="background: {{ $nodeBgColor }}; box-shadow: 0 0 15px {{ $shadowColor }}; cursor: default;">
                                            
                                            <i class="fas {{ $iconSymbol }}"></i>
                                            <div class="checkpoint-tag" style="background: {{ $quiz['is_answered'] ? '#059669' : '#DC2626' }}">
                                                {{ $sub->name }}
                                            </div>
                                            
                                            {{-- HOVER DESCRIPTIVE POPUP CHIPS --}}
                                            <div class="checkpoint-popup-card card border-0 shadow p-2 text-start">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="badge bg-secondary font-weight-bold" style="font-size:0.55rem;">Tier: {{ $quiz['difficulty'] }}</span>
                                                    <span class="badge {{ $quiz['is_answered'] ? 'bg-success' : 'bg-danger' }}" style="font-size:0.55rem;">{{ $statusText }}</span>
                                                </div>
                                                <h6 class="fw-bold text-dark mb-1" style="font-size: 0.75rem;">{{ $quiz['topic'] }}</h6>
                                                <p class="text-muted mb-0 small" style="font-size: 0.65rem;">Title: {{ $quiz['title'] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endforeach

                            @if($nodeCounter === 0)
                                <div class="text-center text-muted py-5" style="cursor: default;">No quiz records assigned in the database repository pool.</div>
                            @endif

                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- PIE CHART AND PERFORMANCE RATIO PANEL BOXES (WIDTH: 4) --}}
        <div class="col-lg-4">
            <div class="d-flex flex-column h-100">
                
                <div class="card border-0 shadow-sm rounded-4 mb-4 flex-grow-1">
                    <div class="card-body p-4">
                        <div class="text-start mb-3">
                            <h5 class="fw-bold text-dark mb-0">Subject-wise Performance</h5>
                            <small class="text-muted">Real-time data distribution maps</small>
                        </div>
                        <div style="height: 240px; position: relative;">
                            <canvas id="performancePieChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 text-start">
                        <h5 class="fw-bold text-dark mb-3">Study Summary</h5>
                        <div class="row align-items-center g-0">
                            <div class="col-4 border-end">
                                <h1 class="fw-bold mb-0 text-dark" style="font-size: 2.8rem;">{{ $totalQuizzes ?? 0 }}</h1>
                                <p class="text-muted small mb-0">Quizzes Completed</p>
                            </div>
                            <div class="col-8 ps-3">
                                <div class="d-flex justify-content-between align-items-end mb-1">
                                    <h3 class="fw-bold mb-0 text-dark">{{ round($averageScore ?? 0, 1) }}%</h3>
                                    <small class="text-muted">Avg Score</small>
                                </div>
                                <div class="progress rounded-pill" style="height: 10px;">
                                    <div class="progress-bar bg-success rounded-pill" role="progressbar" style="width: {{ $averageScore ?? 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- GOOGLE CALENDAR AT bottom LINE FRAME --}}
    <div class="card border-0 shadow-sm rounded-4 mb-5">
        <div class="card-header bg-white border-0 pt-4 px-4 text-start">
            <h5 class="fw-bold text-dark mb-0"><i class="far fa-calendar-alt me-2 text-danger"></i>Calendar Events</h5>
        </div>
        <div class="card-body p-4">
            <div style="width: 100%; height: 450px; overflow: hidden; border-radius: 12px; border: 1px solid #eee;">
                <iframe src="https://calendar.google.com/calendar/embed?height=600&wkst=1&bgcolor=%23ffffff&ctz=Asia%2FKuala_Lumpur&src=ZW4ubWFsYXlzaWEjaG9saWRheUBncm91cC52LmNhbGVuZGFyLmdvb2dsZS5jb20&color=%230B8043&showTitle=0&showNav=1&showDate=1&showPrint=0&showTabs=1&showCalendars=0&showTz=0" 
                    style="border:0" width="100%" height="450" frameborder="0" scrolling="no">
                </iframe>
            </div>
        </div>
    </div>

</div>

<style>
    .hover-scale { transition: transform 0.2s; }
    .hover-scale:hover { transform: translateY(-5px); }
    .prayer-widget-container { position: fixed; bottom: 30px; right: 30px; z-index: 999; display: flex; flex-direction: column; align-items: end; }
    .prayer-icon { width: 60px; height: 60px; }
    .prayer-list-card { width: 220px; }
    .text-start { text-align: left !important; }

    .roadmap-grid-bg {
        position: absolute; top:0; left:0; width:100%; height:100%;
        background-color: #ffffff;
        background-image: radial-gradient(rgba(0, 0, 0, 0.03) 1.5px, transparent 0);
        background-size: 20px 20px; opacity: 0.9; pointer-events: none; z-index: 1;
    }

    /* INDEPENDENT ROADMAP AREA VERTICAL SCROLL WRAPPER GRID LIMITATION */
    .roadmap-scroll-wrapper { max-height: 490px; overflow-y: auto; overflow-x: hidden; padding: 25px 10px; scroll-behavior: smooth; }
    .candy-crush-spine-box { position: relative; width: 100%; max-width: 430px; margin: 0 auto; display: flex; flex-direction: column; gap: 75px; }
    .spine-svg-layer { position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 2; }
    
    .candy-node-row { display: flex; width: 100%; position: relative; z-index: 10; }
    .node-pos-0 { justify-content: flex-start; padding-left: 10%; }
    .node-pos-1 { justify-content: center; }
    .node-pos-2 { justify-content: flex-end; padding-right: 10%; }
    .node-pos-3 { justify-content: center; }

    .candy-checkpoint-bubble {
        width: 58px; height: 58px; border-radius: 50%; border: 4px solid #fff;
        display: flex; align-items: center; justify-content: center; position: relative;
        transition: transform 0.2s ease; z-index: 5;
    }
    .candy-checkpoint-bubble:hover { transform: scale(1.1); }
    
    .checkpoint-tag {
        position: absolute; bottom: -28px; left: 50%; transform: translateX(-50%);
        font-size: 0.6rem; padding: 2px 10px; border-radius: 20px; font-weight: 800; letter-spacing: 0.5px; white-space: nowrap; color: #fff;
    }

    .checkpoint-popup-card {
        position: absolute; top: -75px; left: 50%; transform: translateX(-50%) scale(0.9);
        width: 210px; opacity: 0; pointer-events: none; transition: all 0.2s ease; z-index: 200; border-radius: 12px; background: #fff;
    }
    .candy-checkpoint-bubble:hover .checkpoint-popup-card { opacity: 1; transform: translateX(-50%) scale(1); }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script>
    // 🟢 SECURE VECTOR GENERATION ROUTINE BOUNDED ON DOM INITIALIZATION
    document.addEventListener("DOMContentLoaded", function() {
        calculateIslandSpineVectors();
        
        // Redraw vector alignments if viewport undergoes desktop layout scaling shifts
        window.addEventListener('resize', calculateIslandSpineVectors);
    });

    function calculateIslandSpineVectors() {
        const bubbles = document.querySelectorAll('.d-node-bubble');
        const parent = document.getElementById('candyCrushSpineBox').getBoundingClientRect();
        const line = document.getElementById('roadmapSpineVector');

        if(bubbles.length < 2) {
            line.setAttribute('d', '');
            return;
        }

        let pathString = "";
        bubbles.forEach((bubble, index) => {
            const rect = bubble.getBoundingClientRect();
            const bX = (rect.left + rect.width/2) - parent.left;
            const bY = (rect.top + rect.height/2) - parent.top;

            if (index === 0) {
                pathString += `M ${bX} ${bY}`;
            } else {
                pathString += ` L ${bX} ${bY}`;
            }
        });

        line.setAttribute('d', pathString);
    }

    // ✅ PIE CHART METRIC LOGIC - RENDER CHANNELS STABILIZED
    const pieCtx = document.getElementById('performancePieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode(array_keys($subjectPerformance)) !!},
            datasets: [{
                data: {!! json_encode(array_values($subjectPerformance)) !!},
                backgroundColor: ['#2962FF', '#F59E0B', '#10B981', '#D93025', '#8B5CF6', '#EC4899'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right', labels: { usePointStyle: true, padding: 12, font: { size: 10 } } }
            }
        }
    });

    // Clock Engine Block
    function initLiveClock() {
        function updateClock() {
            const timeString = moment().format('HH:mm:ss');
            const element = document.getElementById('dashCurrentTime');
            if (element) element.innerText = timeString;
        }
        setInterval(updateClock, 1000);
        updateClock();
    }

    function fetchPrayerTimes() {
        const apiUrl = `https://api.aladhan.com/v1/timings?latitude=2.2775&longitude=102.1466&method=3&fajrAngle=20&ishaAngle=18`;
        fetch(apiUrl).then(res => res.json()).then(data => {
            const t = data.data.timings;
            document.getElementById('time-Fajr').innerText = t.Fajr;
            document.getElementById('time-Dhuhr').innerText = t.Dhuhr;
            document.getElementById('time-Asr').innerText = t.Asr;
            document.getElementById('time-Maghrib').innerText = t.Maghrib;
            document.getElementById('time-Isha').innerText = t.Isha;
            
            document.getElementById('nextPrayerName').innerText = "Subuh";
            document.getElementById('nextPrayerTime').innerText = t.Fajr;
        }).catch(err => console.error("Network interface sync exception:", err));
    }

    initLiveClock();
    fetchPrayerTimes();
    function togglePrayerList() { document.getElementById('prayerList').classList.toggle('d-none'); }
</script>
@endsection