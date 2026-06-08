@extends('users.students')

@section('content')
<div class="container-fluid p-4 pb-5 text-start" style="min-height: 100vh;">

    {{-- HEADER --}}
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold text-dark">📈 My Learning Analytics</h3>
            <p class="text-muted">Track your mastery and focus on areas needing improvement.</p>
        </div>
        <a href="{{ route('student.dashboard') }}" class="btn btn-light border rounded-pill px-4 fw-bold shadow-sm">
            <i class="fas fa-arrow-left me-2"></i> Dashboard
        </a>
    </div>

    {{-- 🟢 NEW: MOMENTUM & FOCUS AREA MODULE --}}
    <div class="row g-4 mb-5">
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 text-white" 
                 style="background: {{ str_contains($momentumStatus, 'Breakthrough') ? 'linear-gradient(135deg, #FF6F00, #FFB300)' : '#455A64' }};">
                <div class="card-body p-4">
                    <h5 class="opacity-75 mb-1 text-white">Recent Momentum</h5>
                    <h5 class="fw-bold mb-0 text-white">{{ $momentumStatus }}</h5>
                    <small class="opacity-75">Based on last 3 attempts</small>
                </div>
            </div>
        </div>
        <div class="col-xl-8 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-4" style="background: #FFF8E1;">
                <h5 class="fw-bold mb-3"><i class="fas fa-bullseye text-warning me-2"></i>Targeted Focus Areas</h5>
                <div class="d-flex gap-3 flex-wrap">
                    @forelse($weakTopics as $topic)
                        <div class="bg-white border p-3 rounded-3 shadow-sm" style="min-width: 180px;">
                            <small class="text-muted d-block" style="font-size: 10px;">Needs Review</small>
                            <strong class="text-dark d-block">{{ $topic->title }}</strong>
                            <span class="text-danger small fw-bold">{{ $topic->score }}%</span>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No weak spots detected! Keep up the great work.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- 1. KPI CARDS --}}
    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 text-white h-100 hover-scale" style="background: linear-gradient(135deg, #00C853, #64DD17);">
                <div class="card-body p-4">
                    <h5 class="opacity-75 mb-1 text-white">Current Average</h5>
                    <h1 class="fw-bold mb-0 text-white">{{ round($currentAvg, 1) }}%</h1>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 text-white h-100 hover-scale" style="background: linear-gradient(135deg, #2962FF, #448AFF);">
                <div class="card-body p-4">
                    <h5 class="opacity-75 mb-1 text-white">Predicted Score</h5>
                    <h1 class="fw-bold mb-0 text-white">{{ $predictedNextScore }}%</h1>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 text-white h-100 hover-scale" style="background: #455A64;">
                <div class="card-body p-4">
                    <h5 class="opacity-75 mb-1 text-white">Growth Status</h5>
                    <h3 class="fw-bold mb-0 text-white">{{ $status }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 text-white h-100 hover-scale" style="background: #455A64;">
                <div class="card-body p-4">
                    <h5 class="opacity-75 mb-1 text-white">Total Quizzes</h5>
                    <h1 class="fw-bold mb-0 text-white">{{ $totalQuizzes }}</h1>
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
                    <span class="badge rounded-pill position-absolute top-0 end-0 mt-3 me-3 px-3 py-2 text-white" style="background-color: {{ $sub->color }};">{{ $sub->rank }}</span>
                    <h5 class="fw-bold mb-4">{{ $sub->name }}</h5>
                    <div style="height: 100px; position: relative;">
                        <canvas id="chart-{{ $index }}"></canvas>
                        <div class="position-absolute top-50 start-50 translate-middle fw-bold small">{{ $sub->avg_score }}%</div>
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

    {{-- 4. LOG TABLE --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            {{-- Your existing table code remains here... --}}
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
                        <tr><td colspan="5" class="text-center py-5 text-muted">No records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-scale { transition: transform 0.2s; } .hover-scale:hover { transform: translateY(-5px); }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Doughnut Charts
    @json($subjectProgress).forEach((sub, index) => {
        const ctx = document.getElementById(`chart-${index}`).getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: { datasets: [{ data: [sub.avg_score, 100-sub.avg_score], backgroundColor: [sub.color, '#f0f0f0'], borderWidth: 0 }] },
            options: { cutout: '75%', responsive: true, maintainAspectRatio: false, plugins: { legend: false } }
        });
    });

    // Trend Chart
    const dates = @json($dates);
    const scores = @json($scores);
    new Chart(document.getElementById('trendChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'Score History',
                data: scores,
                borderColor: '#2962FF',
                tension: 0.3,
                fill: true,
                backgroundColor: 'rgba(41, 98, 255, 0.1)'
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
</script>
@endsection