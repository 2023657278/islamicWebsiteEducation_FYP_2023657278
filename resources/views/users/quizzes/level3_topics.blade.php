@extends('users.students')

@section('content')
<div class="container-fluid py-4 text-start">
    {{-- Breadcrumb Navigation --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-white p-3 rounded-4 shadow-sm">
            <li class="breadcrumb-item"><a href="{{ route('student.quizzes.index') }}" class="text-decoration-none">Subjects</a></li>
            <li class="breadcrumb-item"><a href="{{ route('student.quizzes.difficulties', $subject->id) }}" class="text-decoration-none">{{ $subject->subject_name }}</a></li>
            <li class="breadcrumb-item active text-{{ $difficulty == 'Easy' ? 'success' : ($difficulty == 'Medium' ? 'warning' : 'danger') }} fw-bold" aria-current="page">{{ $difficulty }} Level</li>
        </ol>
    </nav>

    <div class="mb-5">
        <h2 class="fw-black text-dark mb-1">Select a Topic</h2>
        <p class="text-muted">Choose a specific area of study to view available challenges.</p>
    </div>

    {{-- Grid Layout for Topics --}}
    <div class="row g-4">
        @forelse($topics as $topic)
        <div class="col-md-6 col-xl-4">
            <a href="{{ route('student.quizzes.list', ['subject_id' => $subject->id, 'difficulty' => $difficulty, 'topic' => urlencode($topic ?: 'General')]) }}" 
               class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 h-100 topic-card transition-all">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 bg-primary bg-opacity-10 text-primary p-3 rounded-4 me-3">
                                <i class="fas fa-layer-group fa-2x"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h4 class="fw-bold text-dark mb-1">{{ $topic ?: 'General Knowledge' }}</h4>
                                <div class="d-flex align-items-center text-muted small">
                                    <span class="me-3"><i class="fas fa-star text-warning me-1"></i> {{ $difficulty }}</span>
                                    <span><i class="fas fa-chevron-circle-right me-1"></i> View Quizzes</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486754.png" width="120" class="opacity-25 mb-3">
            <h5 class="text-muted">No topics assigned to this level yet.</h5>
        </div>
        @endforelse
    </div>
</div>

<style>
    .fw-black { font-weight: 900; }
    .topic-card { border: 1px solid rgba(0,0,0,0.05) !important; background: #fff; }
    .topic-card:hover { 
        transform: translateY(-8px); 
        box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important; 
        background: #fdfdfd;
        border-color: var(--bs-primary) !important;
    }
</style>
@endsection