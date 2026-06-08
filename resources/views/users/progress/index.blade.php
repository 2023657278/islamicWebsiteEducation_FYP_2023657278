@extends('users.students')

@section('content')
<div class="container-fluid p-4 pb-5 text-start" style="min-height: 100vh;">

    {{-- HEADER --}}
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold text-dark">📈 My Learning Analytics</h3>
            <p class="text-muted">Track your mastery and unlock new difficulty levels.</p>
        </div>
        <a href="{{ route('student.dashboard') }}" class="btn btn-light border rounded-pill px-4 fw-bold shadow-sm">
            <i class="fas fa-arrow-left me-2"></i> Dashboard
        </a>
    </div>

    {{-- 1. KPI CARDS --}}
    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 text-white h-100 hover-scale" style="background: linear-gradient(135deg, #00C853, #64DD17);">
                <div class="card-body p-4 text-white">
                    <h5 class="opacity-75 mb-1 text-white">Current Average</h5>
                    <h1 class="fw-bold mb-0 text-white">{{ round($currentAvg, 1) }}%</h1>
                    <small class="opacity-75">All subjects combined</small>
                </div>
            </div>
        </div>
        
        {{-- 🟢 PRETTY OPTION B KPI CARD INSIDE ORIGINAL POSITION --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 text-white h-100 hover-scale" 
                 style="background: {{ str_contains($momentumStatus, 'Breakthrough') ? 'linear-gradient(135deg, #FF6F00, #FFB300)' : 'linear-gradient(135deg, #2962FF, #448AFF)' }}; transition: background 0.3s ease;">
                <div class="card-body p-4 text-white">
                    <h5 class="opacity-75 mb-1 text-white">Recent Momentum</h5>
                    <h1 class="fw-bold mb-0 text-white" style="font-size: 1.45rem; line-height: 2.4rem;">{{ $momentumStatus }}</h1>
                    <small class="opacity-75">Based on last 3 attempts</small>
                </div>
            </div>
        </div>

        @php
            $statusColor = '#455A64';
            if ($slope >= 1.0) $statusColor = 'linear-gradient(135deg, #2962FF, #82B1FF)';
            elseif ($slope >= -1.0) $statusColor = 'linear-gradient(135deg, #00C853, #69F0AE)';
            else $statusColor = 'linear-gradient(135deg, #D50000, #FF5252)';
        @endphp
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 text-white h-100 hover-scale" style="background: {{ $statusColor }};">
                <div class="card-body p-4 text-white">
                    <h5 class="opacity-75 mb-1 text-white">Growth Status</h5>
                    <h3 class="fw-bold mb-0 text-white">{{ $status }}</h3>
                    <small class="opacity-75">Performance trend</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 text-white h-100 hover-scale" style="background: #455A64;">
                <div class="card-body p-4 text-white">
                    <h5 class="opacity-75 mb-1 text-white">Total Quizzes</h5>
                    <h1 class="fw-bold mb-0 text-white">{{ $totalQuizzes }}</h1>
                    <small class="opacity-75">Completed attempts</small>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. SUBJECT MASTERY --}}
    <h5 class="fw-bold text-dark mb-4"><i class="fas fa-gamepad me-2 text-primary"></i>Subject Mastery</h5>
    <div class="row g-4 mb-5">
        @foreach($subjectProgress as $index => $sub)
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 position-relative">
                    <span class="badge rounded-pill position-absolute top-0 end-0 mt-3 me-3 px-3 py-2 text-white" style="background-color: {{ $sub->color }};">
                        {{ $sub->rank }}
                    </span>
                    <h5 class="fw-bold mb-4">{{ $sub->name }}</h5>
                    <div class="row align-items-center">
                        <div class="col-5">
                            <div style="height: 100px; position: relative;">
                                <canvas id="chart-{{ $index }}"></canvas>
                                <div class="position-absolute top-50 start-50 translate-middle fw-bold small">{{ $sub->avg_score }}%</div>
                            </div>
                        </div>
                        <div class="col-7 ps-0">
                            <p class="mb-1 small fw-bold text-muted">Unlock Progress</p>
                            <div class="d-flex justify-content-between align-items-center bg-light rounded p-2 border">
                                <i class="fas fa-unlock text-success"></i>
                                <div style="width: 10px; height: 1px; background: #ccc;"></div>
                                <i class="fas {{ $sub->avg_score >= 40 ? 'fa-unlock text-primary' : 'fa-lock text-muted opacity-50' }}"></i>
                                <div style="width: 10px; height: 1px; background: #ccc;"></div>
                                <i class="fas {{ $sub->avg_score >= 80 ? 'fa-unlock text-danger' : 'fa-lock text-muted opacity-50' }}"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- 3. TREND CHART --}}
    <div class="card border-0 shadow-sm rounded-4 mb-5">
        <div class="card-body p-4">
            <h5 class="fw-bold text-dark mb-4 text-start"><i class="fas fa-chart-line me-2 text-info"></i>Performance History</h5>
            <div style="height: 350px;"><canvas id="trendChart"></canvas></div>
        </div>
    </div>

    {{-- 4. DETAILED LOG --}}
    <div class="card border-0 shadow-sm rounded-4" id="logTableSection">
        <div class="card-body p-4">
            <h5 class="fw-bold text-dark mb-4 text-start"><i class="fas fa-history me-2 text-secondary"></i>Detailed Log</h5>

            <form id="filterForm" action="{{ route('student.progress.index') }}" method="GET" class="row g-2 mb-4 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 filter-input" placeholder="Search quiz title..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="result_status" class="form-select filter-input">
                        <option value="">All Status</option>
                        <option value="pass" {{ request('result_status') == 'pass' ? 'selected' : '' }}>Passed</option>
                        <option value="fail" {{ request('result_status') == 'fail' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="subject_filter" class="form-select filter-input">
                        <option value="all">All Subjects</option>
                        @foreach($filterSubjects as $sub)
                            <option value="{{ $sub }}" {{ request('subject_filter') == $sub ? 'selected' : '' }}>{{ $sub }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" name="filter_date" class="form-control filter-input" value="{{ request('filter_date') }}">
                </div>
            </form>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr class="text-muted small text-uppercase">
                            <th class="py-3 ps-3">Date</th>
                            <th class="py-3">Subject</th>
                            <th class="py-3">Quiz Title</th>
                            <th class="py-3">Score</th>
                            <th class="py-3">Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attempts as $attempt)
                        <tr>
                            <td class="ps-3 text-muted">{{ \Carbon\Carbon::parse($attempt->created_at)->format('d M Y') }}</td>
                            <td><span class="badge bg-light text-dark border px-2 py-1">{{ $attempt->subject_name }}</span></td>
                            <td class="fw-bold text-start text-dark">{{ $attempt->quiz_title }}</td>
                            <td><span class="fw-bold {{ $attempt->score >= 50 ? 'text-success' : 'text-danger' }}">{{ $attempt->score }}%</span></td>
                            <td>
                                @if($attempt->score >= 50)
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Passed</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">Failed</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-5 text-muted">No records found matching filters.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-scale { transition: transform 0.2s; } .hover-scale:hover { transform: translateY(-5px); }
    .text-start { text-align: left !important; }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Persistence Logic
    const filterForm = document.getElementById('filterForm');
    const handleSubmission = () => { localStorage.setItem('scrollPos', window.scrollY); filterForm.submit(); };
    window.addEventListener('load', () => { 
        const scrollPos = localStorage.getItem('scrollPos'); 
        if (scrollPos) { window.scrollTo(0, parseInt(scrollPos)); localStorage.removeItem('scrollPos'); } 
    });
    document.querySelectorAll('.filter-input').forEach(input => { input.addEventListener('change', handleSubmission); });

    // Doughnut Mastery Logic
    @json($subjectProgress).forEach((sub, index) => {
        const ctx = document.getElementById(`chart-${index}`).getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: { datasets: [{ data: [sub.avg_score, 100-sub.avg_score], backgroundColor: [sub.color, '#f0f0f0'], borderWidth: 0 }] },
            options: { cutout: '75%', responsive: true, maintainAspectRatio: false, plugins: { legend: false } }
        });
    });

    // Trend Chart with Goal Line
    const dates = @json($dates);
    const scores = @json($scores);
    const slope = {{ $slope }};
    const predicted = {{ $predictedNextScore }};

    const intercept = scores[0]; 
    const regressionData = dates.map((_, index) => Math.max(0, Math.min(100, intercept + (slope * index))));
    regressionData.push(predicted);

    new Chart(document.getElementById('trendChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: [...dates, 'Predicted Next'],
            datasets: [
                {
                    label: 'Score History',
                    data: scores,
                    borderColor: '#2962FF',
                    backgroundColor: 'rgba(41, 98, 255, 0.1)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 5
                },
                {
                    label: 'Overall Trend (Slope)',
                    data: regressionData,
                    borderColor: '#FF5252',
                    borderDash: [8, 4],
                    fill: false,
                    pointRadius: (c) => c.dataIndex === regressionData.length - 1 ? 7 : 0,
                    pointStyle: 'star'
                },
                {
                    label: 'Expert Goal (80%)',
                    data: Array(dates.length + 1).fill(80),
                    borderColor: '#00C853',
                    borderWidth: 2,
                    borderDash: [2, 2],
                    fill: false,
                    pointRadius: 0
                }
            ]
        },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, max: 100 } } }
    });
</script>
@endsection