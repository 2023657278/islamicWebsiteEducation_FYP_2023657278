@extends('admin.adminhome')
@section('content')
<div class="container py-4">
    <h3 class="fw-bold mb-4">📝 Exam Hall & Revision</h3>
    
    <div class="list-group shadow-sm">
        @foreach($papers as $paper)
        <div class="list-group-item d-flex justify-content-between align-items-center p-3">
            <div>
                <h5 class="mb-1 fw-bold">{{ $paper->title }}</h5>
                <small class="text-muted">{{ $paper->subject }} • {{ $paper->year }}</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('exams.flashcards', $paper->id) }}" class="btn btn-info text-white btn-sm">
                    <i class="fas fa-layer-group me-1"></i> Flashcards
                </a>
                <a href="{{ route('exams.take', $paper->id) }}" class="btn btn-success btn-sm">
                    <i class="fas fa-pen-alt me-1"></i> Take Exam
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection