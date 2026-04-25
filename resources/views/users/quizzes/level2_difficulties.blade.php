@extends('users.students')

@section('content')
<div class="container-fluid p-0 text-start">
    {{-- Header --}}
    <div class="mb-5">
        <a href="{{ route('student.quizzes.index') }}" class="text-muted text-decoration-none">
            <i class="fas fa-arrow-left me-1"></i> Back to Subjects
        </a>
        <h2 class="fw-bold mt-2">{{ $subject->subject_name }} <span class="text-muted fw-light">/ Skill Levels</span></h2>
        <p class="text-muted">Master each level to unlock the next challenge.</p>
    </div>

    <div class="row g-4">
        @foreach(['Easy', 'Medium', 'Hard'] as $level)
            @php 
                $isAllowed = in_array($level, $allowed); 
                $color = $level == 'Easy' ? 'success' : ($level == 'Medium' ? 'warning' : 'danger');
                $levelStats = $stats[$level];
            @endphp
            
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden {{ !$isAllowed ? 'bg-light' : '' }}" style="transition: transform 0.3s;">
                    {{-- Top Color Stripe --}}
                    <div style="height: 6px; background-color: var(--bs-{{ $color }});"></div>
                    
                    <div class="card-body p-4 text-center d-flex flex-column">
                        <div class="mb-3">
                            @if($isAllowed)
                                <div class="bg-{{ $color }} bg-opacity-10 text-{{ $color }} rounded-circle d-inline-flex p-3">
                                    <i class="fas fa-medal fa-2x"></i>
                                </div>
                            @else
                                <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle d-inline-flex p-3">
                                    <i class="fas fa-lock fa-2x"></i>
                                </div>
                            @endif
                        </div>

                        <h3 class="fw-bold text-dark mb-1">{{ $level }}</h3>
                        
                        {{-- Stats Row --}}
                        <div class="mt-3 mb-4">
                            <div class="d-flex justify-content-between mb-1 small text-muted font-weight-bold">
                                <span>Progress</span>
                                <span>{{ $levelStats['done'] }}/{{ $levelStats['total'] }} Quizzes</span>
                            </div>
                            <div class="progress" style="height: 10px; border-radius: 5px;">
                                @php 
                                    $percent = ($levelStats['total'] > 0) ? ($levelStats['done'] / $levelStats['total']) * 100 : 0;
                                @endphp
                                <div class="progress-bar bg-{{ $color }}" role="progressbar" style="width: {{ $percent }}%"></div>
                            </div>
                            @if($isAllowed && $levelStats['done'] > 0)
                                <small class="text-{{ $color }} fw-bold d-block mt-2">Avg Score: {{ $levelStats['avg'] }}%</small>
                            @endif
                        </div>

                        <div class="mt-auto">
                            @if($isAllowed)
                                <a href="{{ route('student.quizzes.topics_diff', [$subject->id, $level]) }}" 
                                   class="btn btn-{{ $color }} w-100 rounded-pill fw-bold py-2 shadow-sm">
                                   Enter Level
                                </a>
                            @else
                                <div class="p-3 bg-white border rounded-4 small text-muted">
                                    <i class="fas fa-info-circle text-warning me-1"></i> 
                                    Clear <b>{{ $level == 'Medium' ? 'Easy' : 'Medium' }}</b> with all quizzes done and avg > 50% to unlock.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<style>
    .card:hover { transform: translateY(-5px); }
</style>
@endsection