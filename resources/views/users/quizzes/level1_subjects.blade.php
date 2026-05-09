@extends('users.students')

@section('content')
<div class="container-fluid p-0">
    {{-- Header with Stats --}}
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-bold text-dark">Quiz Center</h2>
            <p class="text-muted">Select a subject to begin.</p>
        </div>
        <div class="d-flex gap-3">
            <div class="bg-white p-3 rounded-4 shadow-sm text-center">
                <h4 class="fw-bold mb-0 text-success">{{ $completedCount }}</h4>
                <small class="text-muted">Completed</small>
            </div>
            <div class="bg-white p-3 rounded-4 shadow-sm text-center">
                <h4 class="fw-bold mb-0 text-primary">{{ round($avgScore) }}%</h4>
                <small class="text-muted">Avg Score</small>
            </div>
        </div>
    </div>

    {{-- Subject Grid --}}
    <div class="row g-4">
        @foreach($subjects as $sub)
        <div class="col-md-4 col-lg-3">
            {{-- 🟢 UPDATED: Pointing to the difficulties route instead of topics --}}
            <a href="{{ route('student.quizzes.select_mode', $sub->id) }}" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm rounded-4 hover-scale transition-all">
                    <div class="card-body text-center p-5">
                        <div class="bg-light rounded-circle d-inline-flex p-3 mb-3 text-danger">
                            <i class="fas fa-book fa-2x"></i> 
                        </div>
                        <h4 class="fw-bold text-dark mb-1">{{ $sub->subject_name }}</h4>
                        {{-- Change this line --}}
<small class="text-muted">{{ $sub->solo_quizzes_count }} Quizzes Available</small>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>

<style>
    .hover-scale { transition: transform 0.2s; }
    .hover-scale:hover { transform: translateY(-5px); }
</style>
@endsection