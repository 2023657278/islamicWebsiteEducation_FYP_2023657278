@extends('users.students')

@section('content')
<div class="container-fluid p-0">
    <div class="mb-4">
        <a href="{{ route('student.quizzes.topics', $subject->id) }}" class="text-muted text-decoration-none mb-2 d-inline-block">
            <i class="fas fa-arrow-left"></i> Back to Topics
        </a>
        <h2 class="fw-bold">{{ $topic ?? 'General' }} <span class="text-muted fw-light">/ Quizzes</span></h2>
    </div>

    {{-- Tabs for Difficulty --}}
    <ul class="nav nav-pills mb-4 gap-2" id="pills-tab" role="tablist">
        @foreach(['Easy', 'Medium', 'Hard'] as $level)
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 {{ $loop->first ? 'active' : '' }} {{ $level == 'Easy' ? 'btn-outline-success' : ($level == 'Medium' ? 'btn-outline-warning' : 'btn-outline-danger') }}" 
                    id="pills-{{ $level }}-tab" 
                    data-bs-toggle="pill" 
                    data-bs-target="#pills-{{ $level }}" 
                    type="button" 
                    role="tab">
                {{ $level }}
            </button>
        </li>
        @endforeach
    </ul>

    <div class="tab-content" id="pills-tabContent">
        @foreach(['Easy', 'Medium', 'Hard'] as $level)
        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="pills-{{ $level }}" role="tabpanel">
            <div class="row g-4">
                @if(isset($groupedQuizzes[$level]) && $groupedQuizzes[$level]->count() > 0)
                    @foreach($groupedQuizzes[$level] as $quiz)
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative">
                            {{-- Difficulty Stripe --}}
                            <div class="position-absolute top-0 start-0 w-100" style="height: 5px; background: {{ $level == 'Easy' ? '#22c55e' : ($level == 'Medium' ? '#f59e0b' : '#ef4444') }};"></div>
                            
                            <div class="card-body p-4 d-flex flex-column">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="badge {{ $level == 'Easy' ? 'bg-success' : ($level == 'Medium' ? 'bg-warning' : 'bg-danger') }} bg-opacity-10 text-dark border">
                                        {{ $level }}
                                    </span>
                                    @if($quiz->is_completed)
                                        <i class="fas fa-check-circle text-success fa-lg"></i>
                                    @endif
                                </div>

                                <h5 class="fw-bold text-dark mb-2">{{ $quiz->title }}</h5>
                                <p class="text-muted small mb-4 line-clamp-2">{{ $quiz->description }}</p>

                                <div class="mt-auto">
                                    @if($quiz->is_completed)
                                        <div class="d-flex justify-content-between align-items-center mb-3 bg-light p-2 rounded">
                                            <small class="fw-bold text-muted">Best Score</small>
                                            <span class="fw-bold text-success">{{ $quiz->my_score }}%</span>
                                        </div>
                                        <a href="{{ route('student.quizzes.take', $quiz->id) }}" class="btn btn-outline-dark w-100 rounded-pill">Retake</a>
                                    @else
                                        <a href="{{ route('student.quizzes.take', $quiz->id) }}" class="btn btn-primary w-100 rounded-pill">Start Quiz</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="col-12 text-center py-5">
                        <p class="text-muted">No {{ $level }} quizzes available for this topic yet.</p>
                    </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection