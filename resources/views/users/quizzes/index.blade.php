@extends('users.students')

@section('content')
<div class="container-fluid p-0">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Quiz Center</h2>
            <p class="text-muted">Test your knowledge and track your progress</p>
        </div>
        <div class="d-flex gap-3">
            <div class="bg-white p-3 rounded-4 shadow-sm text-center" style="min-width: 120px;">
                <h4 class="fw-bold mb-0">{{ $completedCount }}</h4>
                <small class="text-muted">Completed</small>
            </div>
            <div class="bg-white p-3 rounded-4 shadow-sm text-center" style="min-width: 120px;">
                <h4 class="fw-bold mb-0">{{ round($avgScore) }}%</h4>
                <small class="text-muted">Avg Score</small>
            </div>
        </div>
    </div>

    {{-- SUBJECT TABS --}}
    <div class="mb-4 d-flex gap-2 overflow-auto pb-2">
        <a href="{{ route('student.quizzes.index', ['subject_id' => 'all']) }}" 
           class="btn {{ request('subject_id') == 'all' || !request('subject_id') ? 'btn-danger' : 'btn-light bg-white border' }} rounded-pill px-4">
           All Subjects
        </a>
        @foreach($subjects as $sub)
        <a href="{{ route('student.quizzes.index', ['subject_id' => $sub->id]) }}" 
           class="btn {{ request('subject_id') == $sub->id ? 'btn-danger' : 'btn-light bg-white border' }} rounded-pill px-4 text-nowrap">
           {{ $sub->subject_name }}
        </a>
        @endforeach
    </div>

    {{-- QUIZ LIST --}}
    <div class="row g-4">
        @forelse($quizzes as $quiz)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                {{-- Dynamic Background: Light Green if Completed, Teal if New --}}
                <div class="card-body p-4 d-flex flex-column" style="background: {{ $quiz->is_completed ? '#e6fffa' : '#0f766e' }};">
                    
                    {{-- Badge Header --}}
                    <div class="d-flex justify-content-between mb-3">
                        <div class="bg-white bg-opacity-25 p-2 rounded text-{{ $quiz->is_completed ? 'success' : 'white' }}">
                            <i class="fas fa-book-open"></i>
                        </div>
                        @if($quiz->is_completed)
                            <span class="badge bg-success rounded-pill px-3 py-2">Completed</span>
                        @else
                            <span class="badge bg-info bg-opacity-25 text-white border border-white rounded-pill px-3 py-2">New</span>
                        @endif
                    </div>

                    {{-- Title --}}
                    <h5 class="fw-bold {{ $quiz->is_completed ? 'text-dark' : 'text-white' }} mb-2">{{ $quiz->title }}</h5>
                    
                    {{-- Info Row --}}
                    <div class="d-flex gap-3 {{ $quiz->is_completed ? 'text-dark' : 'text-white-50' }} small mb-4">
                        <span><i class="fas fa-question-circle me-1"></i> {{ $quiz->questions->count() }} Q's</span>
                        <span><i class="fas fa-clock me-1"></i> {{ $quiz->duration_minutes }} min</span>
                    </div>

                    {{-- Bottom Actions --}}
                    <div class="mt-auto">
                        @if($quiz->is_completed)
                            {{-- SHOW SCORE IF DONE --}}
                            <div class="bg-white rounded-3 p-3 mb-3 border">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="text-muted fw-bold">Best Score</small>
                                    <h5 class="fw-bold text-success mb-0">{{ $quiz->my_score }}%</h5>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-warning" style="width: {{ $quiz->my_score }}%"></div>
                                </div>
                            </div>
                            <a href="{{ route('student.quizzes.take', $quiz->id) }}" class="btn btn-outline-success w-100 fw-bold rounded-pill">Retake Quiz</a>
                        @else
                            {{-- START BUTTON IF NEW --}}
                            <a href="{{ route('student.quizzes.take', $quiz->id) }}" class="btn btn-light w-100 fw-bold rounded-pill text-teal">Start Quiz</a>
                        @endif
                    </div>

                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5 text-muted">No quizzes found.</div>
        @endforelse
    </div>
</div>
@endsection