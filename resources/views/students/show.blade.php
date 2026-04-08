@extends('admin.adminhome')

@section('content')
<div class="container-fluid">
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-primary"><i class="fas fa-chart-line me-2"></i>Student Analytics</h2>
            <p class="text-muted mb-0">Detailed performance report for {{ $student->name }}</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('students.index') }}" class="btn btn-secondary shadow-sm"><i class="fas fa-arrow-left me-1"></i> Back</a>
            <a href="{{ route('students.edit', $student->id) }}" class="btn btn-warning shadow-sm"><i class="fas fa-edit me-1"></i> Edit</a>
        </div>
    </div>

    <div class="row">
        {{-- LEFT COLUMN: PROFILE & SUMMARY --}}
        <div class="col-lg-4">
            
            {{-- Profile Card --}}
            <div class="card shadow-sm mb-4 border-0 rounded-4">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                         @if($student->profile_photo_path)
                            <img src="{{ asset('storage/' . $student->profile_photo_path) }}" class="rounded-circle shadow-sm" width="100" height="100" style="object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center text-primary" style="width: 80px; height: 80px;">
                                <i class="fas fa-user-graduate fa-3x"></i>
                            </div>
                        @endif
                    </div>
                    <h4 class="fw-bold text-dark mb-1">{{ $student->name }}</h4>
                    <span class="badge bg-primary px-3 py-2 mb-3 rounded-pill">
                        {{ $student->group->group_name ?? 'No Group' }} ({{ $student->group->year->year ?? '-' }})
                    </span>
                    
                    <div class="d-flex justify-content-between text-start bg-light p-3 rounded-3 small">
                        <div>
                            <p class="mb-1"><strong>ID:</strong> #{{ $student->id }}</p>
                            <p class="mb-0"><strong>Email:</strong> {{ $student->email }}</p>
                        </div>
                        <div class="text-end">
                            <p class="mb-1"><strong>Phone:</strong> {{ $student->phone_number ?? '-' }}</p>
                            <p class="mb-0"><strong>Joined:</strong> {{ $student->created_at->format('M Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Performance Status Card --}}
            <div class="card shadow-sm border-0 rounded-4 text-white" style="background: linear-gradient(135deg, #0d9488, #115e59);">
                <div class="card-body p-4 text-center">
                    <h5 class="opacity-75 text-uppercase small ls-1">Overall Status</h5>
                    <h2 class="fw-bold mb-1">{{ $cluster }}</h2>
                    <p class="small opacity-75 mb-0">Avg Score: {{ round($currentAvg, 1) }}%</p>
                </div>
            </div>

             {{-- Predicted Score Card --}}
             <div class="card shadow-sm border-0 rounded-4 mt-3 text-white" style="background: linear-gradient(135deg, #4f46e5, #4338ca);">
                <div class="card-body p-4 text-center">
                    <h5 class="opacity-75 text-uppercase small ls-1">Predicted Next Score</h5>
                    <h1 class="fw-bold mb-0 display-4">{{ $predictedNextScore }}%</h1>
                    <p class="small opacity-75 mb-0">
                        Based on slope: {{ number_format($slope, 2) }}
                        @if($slope > 0) <i class="fas fa-arrow-up"></i> @elseif($slope < 0) <i class="fas fa-arrow-down"></i> @endif
                    </p>
                </div>
            </div>

        </div>

        {{-- RIGHT COLUMN: CHARTS & SUBJECTS --}}
        <div class="col-lg-8">
            
            {{-- 1. TREND CHART --}}
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold m-0 text-dark"><i class="fas fa-chart-area me-2 text-primary"></i>Performance Trend & Prediction</h6>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- 2. SUBJECT MASTERY GRID --}}
            <h6 class="fw-bold text-dark mb-3"><i class="fas fa-tasks me-2 text-success"></i>Subject Completion</h6>
            <div class="row g-3">
                @forelse($subjectProgress as $sub)
                    @php
                        $barColor = $sub->progress >= 100 ? 'success' : ($sub->progress >= 50 ? 'primary' : 'warning');
                        $scoreColor = $sub->avg_score >= 80 ? 'text-success' : ($sub->avg_score >= 50 ? 'text-primary' : 'text-danger');
                    @endphp
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-3 h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold mb-0">{{ $sub->name }}</h6>
                                    <span class="small fw-bold {{ $scoreColor }}">{{ $sub->avg_score }}% Avg</span>
                                </div>
                                
                                <div class="d-flex justify-content-between small text-muted mb-1">
                                    <span>Progress</span>
                                    <span>{{ $sub->completed }}/{{ $sub->total }} Quizzes</span>
                                </div>

                                <div class="progress rounded-pill" style="height: 8px;">
                                    <div class="progress-bar bg-{{ $barColor }}" role="progressbar" style="width: {{ $sub->progress }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted py-3">No subject data available.</div>
                @endforelse
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('trendChart').getContext('2d');
    
    // PHP Data injection
    const labels = @json($dates);
    const actualScores = @json($scores);
    const trendScores = @json($trendPoints);

    // Add Prediction Point
    labels.push('Next (Predicted)');
    trendScores.push({{ $predictedNextScore }});

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Actual Score',
                    data: actualScores,
                    borderColor: '#4f46e5', // Indigo
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    borderWidth: 3,
                    pointRadius: 5,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#4f46e5',
                    pointBorderWidth: 2,
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Trend Prediction',
                    data: trendScores,
                    borderColor: '#f59e0b', // Amber
                    borderWidth: 2,
                    borderDash: [5, 5],
                    pointRadius: 4,
                    pointBackgroundColor: '#f59e0b',
                    tension: 0,
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' },
                tooltip: { 
                    backgroundColor: '#1f2937',
                    padding: 10,
                    cornerRadius: 8
                }
            },
            scales: {
                y: { 
                    beginAtZero: true, 
                    max: 100,
                    grid: { borderDash: [2, 2] }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
</script>
@endsection