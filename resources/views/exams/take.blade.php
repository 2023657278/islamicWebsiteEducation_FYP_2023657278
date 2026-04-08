@extends('admin.adminhome')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>{{ $paper->title }}</h4>
        <span class="badge bg-danger">Exam Mode</span>
    </div>

    <form action="{{ route('exams.submit', $paper->id) }}" method="POST">
        @csrf
        @foreach($paper->questions as $index => $q)
        <div class="card mb-3 border-0 shadow-sm">
            <div class="card-body">
                <p class="fw-bold">{{ $index + 1 }}. {{ $q->question_text }}</p>
                
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="answers[{{ $q->id }}]" value="option_a" required>
                    <label class="form-check-label">A. {{ $q->option_a }}</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="answers[{{ $q->id }}]" value="option_b">
                    <label class="form-check-label">B. {{ $q->option_b }}</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="answers[{{ $q->id }}]" value="option_c">
                    <label class="form-check-label">C. {{ $q->option_c }}</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="answers[{{ $q->id }}]" value="option_d">
                    <label class="form-check-label">D. {{ $q->option_d }}</label>
                </div>
            </div>
        </div>
        @endforeach

        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold">Submit Answers</button>
    </form>
</div>
@endsection