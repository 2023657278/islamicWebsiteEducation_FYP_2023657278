@extends('users.students')

@section('content')
<div class="container-fluid p-0">
    <div class="mb-4">
        <a href="{{ route('student.quizzes.index') }}" class="text-muted text-decoration-none mb-2 d-inline-block">
            <i class="fas fa-arrow-left"></i> Back to Subjects
        </a>
        <h2 class="fw-bold">{{ $subject->subject_name }} <span class="text-muted fw-light">/ Topics</span></h2>
    </div>

    <div class="row g-3">
        @forelse($topics as $topic)
        <div class="col-12">
            {{-- ✅ FIXED LINE BELOW: Added ?: 'General' to handle empty topics --}}
            <a href="{{ route('student.quizzes.list', ['subject_id' => $subject->id, 'topic' => urlencode($topic ?: 'General')]) }}" 
               class="card border-0 shadow-sm rounded-4 text-decoration-none p-4 hover-bg">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-3">
                            <i class="fas fa-layer-group fa-lg"></i>
                        </div>
                        <div>
                            {{-- Display 'General Knowledge' if topic is empty --}}
                            <h5 class="fw-bold text-dark mb-0">{{ $topic ?: 'General Knowledge' }}</h5>
                            <small class="text-muted">Click to view difficulty levels</small>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-muted"></i>
                </div>
            </a>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486754.png" width="100" class="mb-3 opacity-50">
            <p class="text-muted">No topics found for this subject.</p>
        </div>
        @endforelse
    </div>
</div>

<style>
    .hover-bg:hover { background-color: #f8f9fa; transform: translateX(5px); transition: all 0.2s; }
</style>
@endsection