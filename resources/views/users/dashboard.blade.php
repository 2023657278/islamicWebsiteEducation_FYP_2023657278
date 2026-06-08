@extends('users.students')

@section('content')
<div class="container-fluid p-0 pb-5 position-relative">

    {{-- HEADER - KEPT EXACTLY AS PER YOUR IMAGE_A52904.JPG --}}
    <div class="mb-4 text-start">
        <h3 class="fw-bold text-dark">Quest Hub Dashboard</h3>
        <p class="text-muted">Selamat kembali! Pantau latihan, kutipan pangkat merit, dan analisis prestasi anda.</p>
    </div>

    {{-- LIVE SYSTEM CLOCK CARD - KEPT EXACTLY AS PER YOUR IMAGE_A52904.JPG --}}
    <div class="card border-0 shadow-sm rounded-4 mb-5 overflow-hidden" style="background: linear-gradient(135deg, #4a0404, #800000);">
        <div class="card-body p-4 text-white position-relative text-start">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <p class="text-uppercase small fw-bold opacity-75 mb-1" id="dashTimeLabel">MASA SISTEM AKTIF (ASIA/KUALA_LUMPUR)</p>
                    <h1 class="fw-bold mb-2" id="dashCurrentTime" style="font-size: 3.5rem; font-family: 'Inter', sans-serif; tracking-wide">00:00:00</h1>
                    
                    <div class="d-inline-flex align-items-center bg-white bg-opacity-25 rounded-pill px-3 py-1">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        <span class="fw-bold small" id="activeZoneDisplay">Melaka (Sg. Udang MRSM)</span>
                    </div>
                </div>
                <div class="col-md-4 text-end d-none d-md-block opacity-25">
                    <i class="fas fa-kaaba fa-7x"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- CORE MIDDLE GRID SECTION --}}
    <div class="row g-4 mb-5">
        
        {{-- LEFT COLUMN: KURIKULUM MODUL AL-FALAH (WIDTH: 7) --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-1 d-flex justify-content-between align-items-center text-start">
                    <div>
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="fas fa-book-open me-2 text-dark"></i>Kurikulum Modul Al-Falah
                        </h5>
                        <small class="text-muted">Peta penguasaan 6 bidang utama Pendidikan Islam anda.</small>
                    </div>
                    {{-- 🟢 TARGETED ADDITION: BUTTON TO LAUNCH THE FULLSCREEN CANDY CRUSH QUEST MAP --}}
                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold shadow-sm" onclick="openSagaMap()">
                        <i class="fas fa-map-marked-alt me-1"></i> Peta Saga Game
                    </button>
                </div>
                <div class="card-body px-4 pb-4 pt-2">
                    <div class="row g-3">
                        @foreach($subjectProgress as $sub)
                        <div class="col-md-6">
                            <div class="card border border-light shadow-sm rounded-3 h-100">
                                <div class="card-body p-3 text-start">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center" style="max-width: 65%;">
                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2 shadow-sm" style="width: 36px; height: 36px; flex-shrink:0;">
                                                <i class="fas {{ $sub->icon }} fa-md" style="color: {{ $sub->color }};"></i>
                                            </div>
                                            <h6 class="fw-bold text-dark mb-0 text-truncate" title="{{ $sub->name }}">{{ $sub->name }}</h6>
                                        </div>
                                        <span class="badge {{ $sub->badge }} rounded-pill text-uppercase px-2 py-1 small fw-bold" style="font-size: 0.6rem; letter-spacing: 0.5px;">
                                            {{ $sub->rank }}
                                        </span>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-end mb-1 mt-3 small text-muted">
                                        <span>Tahap Ilmu</span>
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
                                    <div class="text-end mt-2">
                                        <small class="text-muted" style="font-size: 0.65rem;">Siri Percubaan: {{ $sub->attempts_count }} kali</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: SUBJECT-WISE PERFORMANCE & SUMMARY STATS (WIDTH: 5) --}}
        <div class="col-lg-5">
            <div class="d-flex flex-column h-100">
                
                {{-- PIE CHART CARD --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4 flex-grow-1">
                    <div class="card-body p-4">
                        <div class="text-start mb-3">
                            <h5 class="fw-bold text-dark mb-0">Subject-wise Performance</h5>
                            <small class="text-muted">Analisis agihan data masa nyata</small>
                        </div>
                        <div style="height: 240px; position: relative;">
                            <canvas id="performancePieChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- STUDY SUMMARY CARD --}}
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 text-start">
                        <h5 class="fw-bold text-dark mb-3"><i class="fas fa-medal text-warning me-2"></i>Ringkasan Prestasi</h5>
                        <div class="row align-items-center g-0">
                            <div class="col-4 border-end">
                                <h1 class="fw-bold mb-0 text-dark" style="font-size: 2.8rem;">{{ $totalQuizzes ?? 0 }}</h1>
                                <p class="text-muted small mb-0">Kuiz Selesai</p>
                            </div>
                            <div class="col-8 ps-3">
                                <div class="d-flex justify-content-between align-items-end mb-1">
                                    <h3 class="fw-bold mb-0 text-dark">{{ round($averageScore ?? 0, 1) }}%</h3>
                                    <small class="text-muted">Purata Skor</small>
                                </div>
                                <div class="progress rounded-pill" style="height: 10px;">
                                    <div class="progress-bar bg-success rounded-pill" role="progressbar" style="width: {{ $averageScore ?? 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="text-start mt-2 small text-muted pt-2 border-top">
                            <i class="fas fa-info-circle text-info me-1"></i> Status Semasa: <strong>{{ $status ?? 'Stable' }}</strong> dengan garis trend pertumbuhan halaju kuiz.
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    {{-- GOOGLE CALENDAR AT bottom FRAME --}}
    <div class="card border-0 shadow-sm rounded-4 mb-5">
        <div class="card-header bg-white border-0 pt-4 px-4 text-start">
            <h5 class="fw-bold text-dark"><i class="far fa-calendar-alt me-2 text-danger"></i>Calendar Events</h5>
        </div>
        <div class="card-body p-4">
            <div style="width: 100%; height: 450px; overflow: hidden; border-radius: 12px; border: 1px solid #eee;">
                <iframe src="https://calendar.google.com/calendar/embed?height=600&wkst=1&bgcolor=%23ffffff&ctz=Asia%2FKuala_Lumpur&src=ZW4ubWFsYXlzaWEjaG9saWRheUBncm91cC52LmNhbGVuZGFyLmdvb2dsZS5jb20&color=%230B8043&showTitle=0&showNav=1&showDate=1&showPrint=0&showTabs=1&showCalendars=0&showTz=0" 
                    style="border:0" width="100%" height="450" frameborder="0" scrolling="no">
                </iframe>
            </div>
        </div>
    </div>

    {{-- 🎮 10/10 FULL-PAGE CANDY CRUSH SAGA QUEST OVERLAY VIEW SCREEN --}}
    <div id="fullscreenSagaContainer" class="saga-overlay d-none">
        
        {{-- Map Dashboard Header Control Panel Row --}}
        <div class="saga-header d-flex justify-content-between align-items-center px-4 py-3">
            <div class="text-start">
                <h4 class="fw-bold text-white mb-0"><i class="fas fa-map-marked-alt text-warning me-2"></i>Peta Perjalanan Al-Falah</h4>
                <p class="text-white-50 mb-0 small">Klik mana-mana siri penanda bidang untuk melihat tugasan kuiz</p>
            </div>
            <button type="button" class="btn btn-light rounded-pill px-4 fw-bold shadow" onclick="closeSagaMap()">
                <i class="fas fa-times me-1 text-danger"></i> Tutup Peta
            </button>
        </div>

        {{-- Winding Level Path Stream --}}
        <div class="saga-map-viewport">
            <div class="saga-scrollable-track">
                
                {{-- Dynamic SVG Layer drawing vector connections behind nodes --}}
                <svg class="saga-connector-svg">
                    <path id="sagaPathLine" d="" fill="none" stroke="rgba(255,255,255,0.4)" stroke-width="6" stroke-dasharray="12,12" />
                </svg>

                @php
                    // Map horizontal offsetting pattern sequence indices to layout a winding puzzle path string shape
                    $alignments = ['node-left', 'node-center-left', 'node-center-right', 'node-right', 'node-center-right', 'node-center-left'];
                @endphp

                @foreach($subjectProgress as $idx => $sub)
                    @php $alignmentClass = $alignments[$idx % count($alignments)]; @endphp
                    
                    <div class="saga-node-wrapper {{ $alignmentClass }}">
                        <div class="saga-level-checkpoint-bubble hover-scale-up text-white" 
                             style="box-shadow: 0 0 20px {{ $sub->color }}; border: 5px solid #fff; background: linear-gradient(135deg, {{ $sub->color }}, #111);"
                             data-bs-toggle="tooltip" 
                             data-bs-placement="top" 
                             title="{{ $sub->name }} ({{ $sub->rank }} - {{ $sub->avg_score }}%)">
                            
                            <i class="fas {{ $sub->icon }} fa-lg"></i>
                            <span class="saga-level-badge shadow-sm">{{ $idx + 1 }}</span>
                            
                            {{-- Interactive floating level flag plate container --}}
                            <div class="saga-flag-plate card border-0 shadow py-1 px-2">
                                <span class="fw-bold text-dark small text-truncate d-block" style="max-width: 110px;">{{ $sub->name }}</span>
                                <span class="badge {{ $sub->badge }} py-0 font-weight-bold" style="font-size:0.55rem;">{{ $sub->avg_score }}%</span>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>

</div>

{{-- PRAYER WIDGET (FLOATING) --}}
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
    .prayer-widget-container { position: fixed; bottom: 30px; right: 30px; z-index: 999; display: flex; flex-direction: column; align-items: end; }
    .prayer-icon { width: 60px; height: 60px; }
    .prayer-list-card { width: 220px; }
    .text-start { text-align: left !important; }

    /* 🎨 STUNNING OVERLAY DESIGN MODULES FOR THE RPG SAGA ENVIRONMENT MAP */
    .saga-overlay {
        position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
        background: radial-gradient(circle, #104c3a 0%, #051a14 100%);
        background-image: radial-gradient(rgba(255, 255, 255, 0.15) 1px, transparent 0);
        background-size: 24px 24px;
        z-index: 99999; overflow: hidden; display: flex; flex-direction: column;
        animation: fadeInOverlay 0.4s ease-out forwards;
    }
    .saga-header { background: rgba(0,0,0,0.4); backdrop-filter: blur(8px); border-bottom: 1px solid rgba(255,255,255,0.1); }
    .saga-map-viewport { flex-grow: 1; overflow-y: auto; overflow-x: hidden; padding: 60px 20px; position: relative; }
    .saga-scrollable-track { position: relative; max-width: 500px; margin: 0 auto; display: flex; flex-direction: column; gap: 85px; }
    
    /* Connection paths vectors layer mapping shapes boundaries */
    .saga-connector-svg { position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 2; }
    
    /* Positioning matrix tags mapping components into horizontal trails */
    .saga-node-wrapper { display: flex; width: 100%; position: relative; z-index: 5; }
    .node-left { justify-content: flex-start; }
    .node-center-left { justify-content: cubic-bezier(0.1, 0.9, 0.2, 1); padding-left: 20%; }
    .node-center-right { justify-content: cubic-bezier(0.1, 0.9, 0.2, 1); padding-left: 55%; }
    .node-right { justify-content: flex-end; }

    .saga-level-checkpoint-bubble {
        width: 70px; height: 70px; rounded-circle: 100%; position: relative;
        display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s ease;
    }
    .hover-scale-up:hover { transform: scale(1.18); box-shadow: 0 0 35px #fff !important; }
    
    .saga-level-badge {
        position: absolute; bottom: -8px; right: -8px; background: #fff; color: #111;
        font-weight: 900; width: 24px; height: 24px; border-radius: 50%; font-size: 0.75rem;
        display: flex; align-items: center; justify-content: center;
    }
    .saga-flag-plate {
        position: absolute; top: 76px; left: 50%; transform: translateX(-50%);
        width: 130px; text-align: center; border-radius: 10px; background: rgba(255,255,255,0.95);
    }

    @keyframes fadeInOverlay { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: translateY(0); } }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script>
    // 🟢 SAGA MAP ACTIVE ROUTING HANDLER ACTIONS
    function openSagaMap() {
        document.getElementById('fullscreenSagaContainer').classList.remove('d-none');
        document.body.style.overflow = 'hidden'; // Lock scrolling bounds on back elements
        setTimeout(drawVectorConnectorLines, 100); // Triggers link recalculations
    }

    function closeSagaMap() {
        document.getElementById('fullscreenSagaContainer').classList.add('d-none');
        document.body.style.overflow = 'auto'; // Free viewport
    }

    // Dynamic path calculator tracks bubble offset heights to redraw matching connection line layouts
    function drawVectorConnectorLines() {
        const bubbles = document.querySelectorAll('.saga-level-checkpoint-bubble');
        const path = document.getElementById('sagaPathLine');
        const container = document.querySelector('.saga-scrollable-track').getBoundingClientRect();
        
        if(bubbles.length < 2) return;
        
        let pathString = "";
        bubbles.forEach((bubble, index) => {
            const rect = bubble.getBoundingClientRect();
            // Find center coordinate positions for nodes relative to core track dimensions
            const centerX = (rect.left + rect.width / 2) - container.left;
            const centerY = (rect.top + rect.height / 2) - container.top;
            
            if (index === 0) {
                pathString += `M ${centerX} ${centerY}`;
            } else {
                pathString += ` L ${centerX} ${centerY}`;
            }
        });
        path.setAttribute('d', pathString);
    }

    // Keep vectors clean across desktop window scaling shifts
    window.addEventListener('resize', () => {
        if(!document.getElementById('fullscreenSagaContainer').classList.contains('d-none')) {
            drawVectorConnectorLines();
        }
    });

    // ✅ PIE CHART LOGIC - UNTOUCHED, RENDER RULES PRESERVED
    const pieCtx = document.getElementById('performancePieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode(array_keys($subjectPerformance)) !!},
            datasets: [{
                data: {!! json_encode(array_values($subjectPerformance)) !!},
                backgroundColor: ['#2962FF', '#F59E0B', '#10B981', '#D93025', '#8B5CF6', '#FF6B6B'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right', labels: { usePointStyle: true, padding: 20 } }
            }
        }
    });

    // ✅ LIVE SYSTEM CLOCK ENGINES
    function initLiveClock() {
        function updateClock() {
            const timeString = moment().format('HH:mm:ss');
            const element = document.getElementById('dashCurrentTime');
            if (element) element.innerText = timeString;
        }
        setInterval(updateClock, 1000);
        updateClock();
    }

    // ✅ CALIBRATED PRAYER WIDGET FETCH ENGINES
    function fetchPrayerTimes() {
        const apiUrl = `https://api.aladhan.com/v1/timings?latitude=2.2775&longitude=102.1466&method=3&fajrAngle=20&ishaAngle=18`;
        fetch(apiUrl).then(res => res.json()).then(data => {
            const t = data.data.timings;
            document.getElementById('time-Fajr').innerText = t.Fajr;
            document.getElementById('time-Dhuhr').innerText = t.Dhuhr;
            document.getElementById('time-Asr').innerText = t.Asr;
            document.getElementById('time-Maghrib').innerText = t.Maghrib;
            document.getElementById('time-Isha').innerText = t.Isha;
            
            // Static setup mappings
            document.getElementById('nextPrayerName').innerText = "Subuh";
            document.getElementById('nextPrayerTime').innerText = t.Fajr;
        });
    }

    initLiveClock();
    fetchPrayerTimes();
    function togglePrayerList() { document.getElementById('prayerList').classList.toggle('d-none'); }
</script>
@endsection