@extends('admin.adminhome')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Editing: <span class="text-primary">{{ $paper->title }}</span></h4>
        <a href="{{ route('repo.exams.index') }}" class="btn btn-secondary btn-sm">Back to Exams</a>
    </div>
    
    <div class="card p-4 mb-4 shadow-sm border-0 bg-white">
        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Add New Question</h6>
        <form action="{{ route('repo.questions.store', $paper->id) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-bold">Question Text</label>
                <textarea name="question_text" class="form-control" rows="2" required></textarea>
            </div>
            
            <div class="row g-2 mb-3">
                <div class="col-md-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text fw-bold">A</span>
                        <input name="option_a" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text fw-bold">B</span>
                        <input name="option_b" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text fw-bold">C</span>
                        <input name="option_c" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text fw-bold">D</span>
                        <input name="option_d" class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="row g-2">
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Correct Answer</label>
                    <select name="correct_option" class="form-select form-select-sm" required>
                        <option value="option_a">Option A</option>
                        <option value="option_b">Option B</option>
                        <option value="option_c">Option C</option>
                        <option value="option_d">Option D</option>
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label small fw-bold">Explanation (For Flashcards)</label>
                    <input name="explanation" class="form-control form-control-sm" placeholder="Why is this answer correct?" required>
                </div>
            </div>
            
            <div class="mt-3 text-end">
                <button class="btn btn-dark btn-sm px-4">Save Question</button>
            </div>
        </form>
    </div>

    <h6 class="fw-bold mb-3">Existing Questions ({{ $paper->questions->count() }})</h6>
    <ul class="list-group">
        @foreach($paper->questions as $q)
            <li class="list-group-item d-flex justify-content-between align-items-start">
                <div class="me-auto">
                    <div class="fw-bold mb-1">{{ $q->question_text }}</div>
                    <div class="small text-muted mb-1">
                        <span class="badge bg-light text-dark border">Ans: {{ strtoupper(substr($q->correct_option, -1)) }}</span>
                        <span class="ms-2">Expl: {{ $q->explanation }}</span>
                    </div>
                </div>
                <form action="{{ route('repo.questions.destroy', $q->id) }}" method="POST" onsubmit="return confirm('Delete question?');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-outline-danger btn-sm border-0"><i class="fas fa-times"></i></button>
                </form>
            </li>
        @endforeach
    </ul>
</div>
@endsection