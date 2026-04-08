@extends('admin.adminhome')
@section('content')
<div class="container py-5 text-center">
    <div class="card shadow border-0 d-inline-block p-5">
        <h2 class="mb-3">Exam Completed</h2>
        
        <div class="display-1 fw-bold mb-3 text-primary">
            {{ $result->score }} / {{ $result->total_questions }}
        </div>
        
        <p class="text-muted mb-4">You have finished {{ $result->paper->title }}</p>

        <div class="d-flex gap-2 justify-content-center">
            <a href="{{ route('exams.index') }}" class="btn btn-outline-secondary">Back to List</a>
            <a href="{{ route('exams.flashcards', $result->paper->id) }}" class="btn btn-primary">
                <i class="fas fa-sync me-2"></i> Review with Flashcards
            </a>
        </div>
    </div>
</div>
@endsection