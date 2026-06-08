@extends('users.students')

@section('content')
<div class="container-fluid p-0 pb-5">

    {{-- DASHBOARD WELCOME HEADER --}}
    <div class="mb-4 text-start">
        <h3 class="fw-bold text-dark">Quest Hub Dashboard</h3>
        <p class="text-muted">Selamat kembali! Pantau latihan, kutipan pangkat merit, dan analisis prestasi anda.</p>
    </div>

    {{-- SYSTEM CLOCK BANNER --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="background: linear-gradient(135deg, #4a0404, #800000);">
        <div class="card-body p-4 text-white position-relative text-start">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <p class="text-uppercase small fw-bold opacity-75 mb-1" id="dashTimeLabel">Masa Sistem Aktif (Asia/Kuala_Lumpur)</p>
                    <h1 class="fw-bold mb-2" id="dashCurrentTime" style="font-size: 3.2rem; font-family: 'Inter', sans-serif; letter-spacing: -1px;">00:00:00</h1>
                    
                    <div class="d-inline-flex align-items-center bg-white bg-opacity-25 rounded-pill px-3 py-1">
                        <i class="fas fa-map-marker-alt me-2 text-warning"></i>
                        <span class="fw-bold small" id="activeZoneDisplay">Menghubungkan Lokasi...</span>
                    </div>
                </div>
                <div class="col-md-4 text-end d-none d-md-block opacity-10">
                    <i class="fas fa-kaaba fa-7x"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- INTERACTIVE CONTAINER SPLIT --}}
    <div class="row g-4">
        
        {{-- 🟢 KIRI: INTERACTIVE PROGRESS MILESTONE TREE (WIDTH: 7) --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(10px);">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-1 text-start">
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-scroll text-maroon me-2"></i>Kurikulum Modul Al-Falah</h5>
                    <small class="text-muted">Peta penguasaan 6 bidang utama Pendidikan Islam anda.</small>
                </div>
                <div class="card-body px-4 pb-4 pt-2">
                    <div class="row g-3">
                        @foreach($subjectProgress as $sub)
                        <div class="col-md-6">
                            <div class="card border border-light shadow-sm rounded-3 h-100 card-hover-effect" style="background: rgba(255,255,255,0.9); transition: transform 0.2s;">
                                <div class="card-body p-3 text-start">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center" style="max-width: 65%;">
                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2 shadow-sm" style="width: 38px; height: 38px; flex-shrink: 0;">
                                                <i class="fas {{ $sub->icon }} fa-lg" style="color: {{ $sub->color }};"></i>
                                            </div>
                                            <h6 class="fw-bold text-dark mb-0 text-truncate" title="{{ $sub->name }}">{{ $sub->name }}</h6>
                                        </div>
                                        <span class="badge {{ $sub->badge }} rounded-pill text-uppercase px-2 py-1 small fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                            {{ $sub->rank }}
                                        </span>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-end mb-1 mt-3 small text-muted">
                                        <span>Tahap Ilmu</span>
                                        <span class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $sub->avg_score }}%</span>
                                    </div>
                                    
                                    <div class="progress rounded-pill shadow-sm" style="height: 10px; background-color: rgba(0,0,0,0.04);">
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

        {{-- 🟢 KANAN: PIE CHART + STUDY SUMMARY DOCK STACK (WIDTH: 5) --}}
        <div class="col-lg-5">
            <div class="d-flex flex-column h-100">
                
                {{-- SUBJECT-WISE PERFORMANCE PIE CHART (LECTURER CORE REQUIREMENT) --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4 flex-grow-1" style="background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(10px);">
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

                {{-- STUDY SUMMARY STATS DOCK CARD --}}
                <div class="card border-0 shadow-sm rounded-4" style="background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(10px);">
                    <div class="card-body p-4 text-start">
                        <h5 class="fw-bold text-dark mb-3"><i class="fas fa-shield-alt text-warning me-2"></i>Ringkasan Prestasi</h5>
                        
                        <div class="row align-items-center g-0 mb-3">
                            <div class="col-4 border-end pe-2">
                                <h1 class="fw-bold mb-0 text-dark" style="font-size: 2.5rem; line-height:1;">{{ $totalQuizzes ?? 0 }}</h1>
                                <p class="text-muted mb-0 small text-uppercase font-weight-bold" style="font-size: 0.65rem;">Kuiz Selesai</p>
                            </div>
                            <div class="col-8 ps-3">
                                <div class="d-flex justify-content-between align-items-end mb-1">
                                    <h3 class="fw-bold mb-0 text-dark" style="line-height:1;">{{ round($averageScore ?? 0, 1) }}%</h3>
                                    <span class="text-muted small">Purata Skor</span>
                                </div>
                                <div class="progress rounded-pill shadow-sm" style="height: 12px; background-color: rgba(0,0,0,0.05);">
                                    <div class="progress-bar bg-success progress-bar-striped progress-bar-animated rounded-pill" role="progressbar" style="width: {{ $averageScore ?? 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-light border-0 rounded-3 mb-0 p-2 d-flex align-items-center bg-light bg-opacity-50">
                            <i class="fas fa-info-circle text-info me-2"></i>
                            <small class="text-muted">Status Semasa: <strong class="text-dark">{{ $status ?? 'Stable' }}</strong> dengan garis trend pertumbuhan halaju kuiz.</small>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    {{-- GOOGLE CALENDAR COMPONENT --}}
    <div class="card border-0 shadow-sm rounded-4 mt-4" style="background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(10px);">
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

{{-- FLOATING PRAYER TIME WIDGET --}}
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script>
    // ✅ CHART.JS ENGINE FOR THE REQUIRED PIE CHART
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
                legend: { position: 'right', labels: { usePointStyle: true, padding: 12, font: { size: 11 } } }
            }
        }
    });

    // ✅ LIVE DASHBOARD CLOCK ENGINES
    function initLiveClock() {
        function updateClock() {
            const timeString = moment().format('HH:mm:ss');
            const element = document.getElementById('dashCurrentTime');
            if (element) element.innerText = timeString;
        }
        setInterval(updateClock, 1000);
        updateClock();
    }

    const zoneLabels = {
        '2.2775,102.1466': 'Melaka (Sg. Udang MRSM)',
        '3.1319,101.6841': 'Kuala Lumpur / Putrajaya',
        '1.4927,103.7414': 'Johor (Johor Bahru)',
        '6.1184,100.3686': 'Kedah (Alor Setar)',
        '6.1254,102.2386': 'Kelantan (Kota Bharu)',
        '2.7258,101.9424': 'Negeri Sembilan (Seremban)',
        '3.8126,103.3256': 'Pahang (Kuantan)',
        '4.5921,101.0901': 'Perak (Ipoh)',
        '6.4449,100.2048': 'Perlis (Kangar)',
        '5.4141,100.3288': 'Pulau Pinang (George Town)',
        '1.5533,110.3592': 'Sarawak (Kuching)',
        '5.9788,116.0753': 'Sabah (Kota Kinabalu)',
        '3.0738,101.5183': 'Selangor (Shah Alam)',
        '5.3302,103.1408': 'Terengganu (Kuala Terengganu)',
        '5.2831,115.2443': 'Labuan'
    };

    function fetchPrayerTimes() {
        const savedLoc = localStorage.getItem('prayerLoc') || '2.2775,102.1466';
        const [lat, long] = savedLoc.split(',');
        
        const locationName = zoneLabels[savedLoc] || 'Melaka (Sg. Udang MRSM)';
        document.getElementById('activeZoneDisplay').innerText = locationName;
        document.getElementById('widgetZoneHeader').innerText = `Solat: ${locationName.split(' ')[0]}`;

        const apiUrl = `https://api.aladhan.com/v1/timings?latitude=${lat}&longitude=${long}&method=3&fajrAngle=20&ishaAngle=18&tune=0,3,0,0,0,0,0,0,0`;

        fetch(apiUrl).then(res => res.json()).then(data => {
            const t = data.data.timings;
            const prayers = { "Subuh": t.Fajr, "Zohor": t.Dhuhr, "Asar": t.Asr, "Maghrib": t.Maghrib, "Isyak": t.Isha };

            document.getElementById('time-Fajr').innerText = t.Fajr;
            document.getElementById('time-Dhuhr').innerText = t.Dhuhr;
            document.getElementById('time-Asr').innerText = t.Asr;
            document.getElementById('time-Maghrib').innerText = t.Maghrib;
            document.getElementById('time-Isha').innerText = t.Isha;

            updateNextPrayer(prayers);
            if (window.dashboardPrayerInterval) clearInterval(window.dashboardPrayerInterval);
            window.dashboardPrayerInterval = setInterval(() => updateNextPrayer(prayers), 1000);
        }).catch(err => console.error("Error connecting network parameters:", err));
    }

    function updateNextPrayer(prayers) {
        const now = moment();
        let nextName = "";
        let nextTime = null;

        for (let name in prayers) {
            let time = moment(prayers[name], "HH:mm");
            if (time.isAfter(now)) { nextName = name; nextTime = time; break; }
        }
        if (!nextTime) { nextName = "Subuh"; nextTime = moment(prayers["Subuh"], "HH:mm").add(1, 'days'); }

        document.getElementById('nextPrayerName').innerText = nextName;
        document.getElementById('nextPrayerTime').innerText = nextTime.format("HH:mm");
    }

    initLiveClock();
    fetchPrayerTimes();
    function togglePrayerList() { document.getElementById('prayerList').classList.toggle('d-none'); }
</script>
@endsection