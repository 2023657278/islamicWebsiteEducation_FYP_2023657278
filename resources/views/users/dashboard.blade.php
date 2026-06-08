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
                        <span class="fw-bold small" id="activeZoneDisplay">Synchronizing Location...</span>
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

    {{-- MIDDLE ROW WORKSPACE LAYOUT --}}
    <div class="row g-4 mb-5">
        
        {{-- AL-FALAH CURRICULUM PANEL (CONTAINS INTEGRATED ROADMAP INNER CONSOLE) --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100 position-relative" style="overflow: hidden;">
                
                {{-- DECORATIVE RADIAL GRID BACKGROUND (ONLY VISIBLE IN ROADMAP MODE) --}}
                <div id="innerRoadmapBgDesign" class="roadmap-grid-bg d-none"></div>

                <div class="card-header bg-white border-0 pt-4 px-4 pb-1 d-flex justify-content-between align-items-center text-start position-relative" style="z-index: 10;">
                    <div>
                        <h5 class="fw-bold text-dark mb-0" id="curriculumPanelTitle">Al-Falah Module Curriculum</h5>
                        <small class="text-muted" id="curriculumPanelSubtitle">Track your performance mapping logs across 6 core Islamic study fields.</small>
                    </div>
                    {{-- Return view button shown in roadmap mode --}}
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold d-none" id="closeRoadmapViewBtn" onclick="hideSubjectRoadmap()">
                        <i class="fas fa-arrow-left me-1"></i> Back to List
                    </button>
                </div>

                <div class="card-body px-4 pb-4 pt-2 position-relative" style="z-index: 5;">
                    
                    {{-- VIEW A: STANDARD OVERVIEW CARD LIST GRID --}}
                    <div id="subjectGridListView" class="row g-3">
                        @foreach($subjectProgress as $sub)
                        <div class="col-md-6">
                            <div class="card border border-light shadow-sm rounded-3 h-100 island-trigger-card" 
                                 onclick="showSubjectRoadmap('{{ addslashes($sub->name) }}', '{{ $sub->color }}', '{{ $sub->icon }}')"
                                 style="background: #ffffff; cursor: pointer; transition: transform 0.2s;">
                                <div class="card-body p-3 text-start">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center" style="max-width: 65%;">
                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2 shadow-sm" style="width: 36px; height: 36px; flex-shrink:0;">
                                                <i class="fas {{ $sub->icon }} fa-md" style="color: {{ $sub->color }};"></i>
                                            </div>
                                            <h6 class="fw-bold text-dark mb-0 text-truncate">{{ $sub->name }}</h6>
                                        </div>
                                        <span class="badge {{ $sub->badge }} rounded-pill text-uppercase px-2 py-1 small fw-bold" style="font-size: 0.6rem;">
                                            {{ $sub->rank }}
                                        </span>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-end mb-1 mt-3 small text-muted">
                                        <span>Mastery Score</span>
                                        <span class="fw-bold text-dark">{{ $sub->avg_score }}%</span>
                                    </div>
                                    
                                    <div class="progress rounded-pill" style="height: 8px; background-color: rgba(0,0,0,0.04);">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated rounded-pill" 
                                             role="progressbar" 
                                             style="width: {{ max(5, $sub->avg_score) }}%; background-color: {{ $sub->color }};"
                                             aria-valuenow="{{ $sub->avg_score }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <small class="text-muted" style="font-size: 0.65rem;">Attempts: {{ $sub->attempts_count }} times</small>
                                        <span class="text-primary font-weight-bold" style="font-size: 0.7rem;"><i class="fas fa-gamepad me-1"></i> Open Roadmap</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- VIEW B: INTEGRATED SAGA ROADMAP INTERFACE (LOADS INSIDE CARD ON-CLICK) --}}
                    <div id="subjectRoadmapTrackView" class="d-none py-4 position-relative">
                        <div class="candy-crush-spine-box">
                            
                            {{-- SVG Pathway Line Connector Layer --}}
                            <svg class="spine-svg-layer">
                                <path id="roadmapSpineVector" d="" fill="none" stroke="#e0e0e0" stroke-width="6" stroke-dasharray="10,10" />
                            </svg>

                            {{-- NODE 1: EASY CHECKPOINT --}}
                            <div class="candy-node-row node-left-align">
                                <div class="candy-checkpoint-bubble shadow" id="checkpointNodeEasy">
                                    <i class="fas fa-star text-white"></i>
                                    <div class="checkpoint-tag shadow-sm">EASY LEVEL</div>
                                    <div class="checkpoint-popup-card card border-0 shadow p-2 text-start">
                                        <h6 class="fw-bold text-dark mb-1" style="font-size: 0.75rem;"><i class="fas fa-graduation-cap me-1 text-success"></i>Basic Foundations</h6>
                                        <p class="text-muted mb-0 small" style="font-size: 0.65rem;">Contains direct fundamental questions from Modul Al-Falah.</p>
                                    </div>
                                </div>
                            </div>

                            {{-- NODE 2: MEDIUM CHECKPOINT --}}
                            <div class="candy-node-row node-center-align">
                                <div class="candy-checkpoint-bubble shadow" id="checkpointNodeMedium">
                                    <i class="fas fa-shield-alt text-white"></i>
                                    <div class="checkpoint-tag shadow-sm">MEDIUM LEVEL</div>
                                    <div class="checkpoint-popup-card card border-0 shadow p-2 text-start">
                                        <h6 class="fw-bold text-dark mb-1" style="font-size: 0.75rem;"><i class="fas fa-book-open me-1 text-warning"></i>Application Cases</h6>
                                        <p class="text-muted mb-0 small" style="font-size: 0.65rem;">Includes context translations and comprehensive textbook application scenarios.</p>
                                    </div>
                                </div>
                            </div>

                            {{-- NODE 3: HARD CHECKPOINT --}}
                            <div class="candy-node-row node-right-align">
                                <div class="candy-checkpoint-bubble shadow" id="checkpointNodeHard">
                                    <i class="fas fa-crown text-white"></i>
                                    <div class="checkpoint-tag shadow-sm">HARD LEVEL</div>
                                    <div class="checkpoint-popup-card card border-0 shadow p-2 text-start">
                                        <h6 class="fw-bold text-dark mb-1" style="font-size: 0.75rem;"><i class="fas fa-brain me-1 text-danger"></i>KBAT Challenges</h6>
                                        <p class="text-muted mb-0 small" style="font-size: 0.65rem;">Features high-level synthesis questions and complex conceptual analysis prompts.</p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- PIE CHART AND ACCOUNT PERFORMANCE SUMMARY DOCKS --}}
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

    {{-- GOOGLE CALENDAR FRAME CONTAINER ROW --}}
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

{{-- PRAYER OVERLAYS --}}
<div class="prayer-widget-container no-print">
    <div id="prayerList" class="card border-0 shadow mb-3 d-none prayer-list-card">
        <div class="card-body p-3 text-start">
            <h6 class="fw-bold text-center border-bottom pb-2 mb-2" id="widgetZoneHeader">Waktu Solat (JAKIM)</h6>
            <div class="d-flex justify-content-between small mb-1"><span>Subuh</span> <span id="time-Fajr" class="fw-bold">--:--</span></div>
            <div class="d-flex justify-content-between small mb-1"><span>Zohor</span> <span id="time-Dhuhr" class="fw-bold">--:--</span></div>
            <div class="d-flex justify-content-between small mb-1"><span>Asar</span> <span id="time-Asr" class="fw-bold">--:--</span></div>
            <div class="d-flex justify-content-between small mb-1"><span>Maghrib</span> <span id="time-Maghrib" class="fw-bold">--:--</span></div>
            <div class="d-flex justify-content-between small mb-1"><span>Isyak</span> <span id="time-Isha" class="fw-bold">--:--</span></div>
        </div>
    </div>

    <div class="d-flex align-items-center justify-content-end" onclick="togglePrayerList()" style="cursor: pointer;">
        <div class="bg-white px-4 py-2 rounded-pill shadow-sm border me-3">
            <div class="fw-bold small text-muted text-uppercase" id="nextPrayerName">Loading...</div>
            <div class="fw-bold text-danger" id="nextPrayerTime">--:--</div>
        </div>
        <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center shadow-lg prayer-icon">
            <i class="far fa-clock fa-lg"></i>
        </div>
    </div>
</div>

<style>
    .hover-scale { transition: transform 0.2s; }
    .hover-scale:hover { transform: translateY(-5px); }
    .island-trigger-card:hover { transform: translateY(-4px); box-shadow: 0 8px 16px rgba(0,0,0,0.08) !important; }
    .prayer-widget-container { position: fixed; bottom: 30px; right: 30px; z-index: 999; display: flex; flex-direction: column; align-items: end; }
    .prayer-icon { width: 60px; height: 60px; }
    .prayer-list-card { width: 220px; }

    /* 🎮 IN-CARD RADIAL PATTERN GRID LINES */
    .roadmap-grid-bg {
        position: absolute; top:0; left:0; width:100%; height:100%;
        background-color: #ffffff;
        background-image: radial-gradient(rgba(0, 0, 0, 0.03) 1.5px, transparent 0);
        background-size: 20px 20px; opacity: 0.9; pointer-events: none; z-index: 1;
    }

    .candy-crush-spine-box { position: relative; width: 100%; max-width: 460px; height: 380px; margin: 0 auto; display: flex; flex-direction: column; justify-content: space-between; padding: 10px 0; }
    .spine-svg-layer { position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 2; }
    
    .candy-node-row { display: flex; width: 100%; position: relative; z-index: 10; }
    .node-left-align { justify-content: flex-start; padding-left: 12%; }
    .node-center-align { justify-content: center; }
    .node-right-align { justify-content: flex-end; padding-right: 12%; }

    .candy-checkpoint-bubble {
        width: 60px; height: 60px; border-radius: 50%; border: 4px solid #fff;
        display: flex; align-items: center; justify-content: center; position: relative;
        transition: transform 0.2s ease, box-shadow 0.2s ease; cursor: pointer; z-index: 5;
    }
    .candy-checkpoint-bubble:hover { transform: scale(1.15); box-shadow: 0 0 20px rgba(0,0,0,0.15) !important; }
    
    .checkpoint-tag {
        position: absolute; bottom: -26px; left: 50%; transform: translateX(-50%);
        font-size: 0.6rem; padding: 2px 8px; border-radius: 20px; font-weight: 800; letter-spacing: 0.5px; background: #333; color: #fff; white-space: nowrap;
    }

    /* Checkpoint Preview Cards on Hover */
    .checkpoint-popup-card {
        position: absolute; top: -85px; left: 50%; transform: translateX(-50%) scale(0.9);
        width: 180px; opacity: 0; pointer-events: none; transition: all 0.2s ease; z-index: 200; border-radius: 12px;
    }
    .candy-checkpoint-bubble:hover .checkpoint-popup-card { opacity: 1; transform: translateX(-50%) scale(1); }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script>
    // 🟢 DYNAMIC IN-CARD ROADMAP MODAL SWITCH ENGINE
    function showSubjectRoadmap(subjectName, subjectColor, subjectIcon) {
        // Toggle titles to match island scope context
        document.getElementById('curriculumPanelTitle').innerText = `${subjectName} Roadmap`;
        document.getElementById('curriculumPanelSubtitle').innerText = `Separate quest tracks for foundation tasks inside the field.`;
        
        document.getElementById('subjectGridListView').classList.add('d-none');
        document.getElementById('subjectRoadmapTrackView').classList.remove('d-none');
        document.getElementById('closeRoadmapViewBtn').classList.remove('d-none');
        document.getElementById('innerRoadmapBgDesign').classList.remove('d-none');

        // Apply dynamic thematic node gradients onto checking markers
        const checkpoints = [
            document.getElementById('checkpointNodeEasy'),
            document.getElementById('checkpointNodeMedium'),
            document.getElementById('checkpointNodeHard')
        ];
        checkpoints.forEach(cp => {
            cp.style.background = `linear-gradient(135deg, ${subjectColor}, #222)`;
            cp.style.boxShadow = `0 0 15px ${subjectColor}55`;
        });

        setTimeout(calculateInnerRoadmapVectors, 100);
    }

    function hideSubjectRoadmap() {
        document.getElementById('curriculumPanelTitle').innerText = 'Al-Falah Module Curriculum';
        document.getElementById('curriculumPanelSubtitle').innerText = 'Track your performance mapping logs across 6 core Islamic study fields.';
        
        document.getElementById('subjectGridListView').classList.remove('d-none');
        document.getElementById('subjectRoadmapTrackView').classList.add('d-none');
        document.getElementById('closeRoadmapViewBtn').classList.add('d-none');
        document.getElementById('innerRoadmapBgDesign').classList.add('d-none');
    }

    function calculateInnerRoadmapVectors() {
        const easy = document.getElementById('checkpointNodeEasy').getBoundingClientRect();
        const medium = document.getElementById('checkpointNodeMedium').getBoundingClientRect();
        const hard = document.getElementById('checkpointNodeHard').getBoundingClientRect();
        const frame = document.querySelector('.candy-crush-spine-box').getBoundingClientRect();
        const vectorLine = document.getElementById('roadmapSpineVector');

        const eX = (easy.left + easy.width/2) - frame.left;
        const eY = (easy.top + easy.height/2) - frame.top;
        const mX = (medium.left + medium.width/2) - frame.left;
        const mY = (medium.top + medium.height/2) - frame.top;
        const hX = (hard.left + hard.width/2) - frame.left;
        const hY = (hard.top + hard.height/2) - frame.top;

        vectorLine.setAttribute('d', `M ${eX} ${eY} L ${mX} ${mY} L ${hX} ${hY}`);
    }

    // ✅ PIE CHART LOGIC - MAPS 6 VIBRANT THEME COLOR CHANNELS SIMULTANEOUSLY
    const pieCtx = document.getElementById('performancePieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode(array_keys($subjectPerformance)) !!},
            datasets: [{
                data: {!! json_encode(array_values($subjectPerformance)) !!},
                // 🟢 DISTINCT THEMATIC COLOR BANDS MAPPED FOR EACH SUB-SECTION INDEX
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

    // Live clock scripts
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
        });
    }

    initLiveClock();
    fetchPrayerTimes();
    function togglePrayerList() { document.getElementById('prayerList').classList.toggle('d-none'); }
</script>
@endsection