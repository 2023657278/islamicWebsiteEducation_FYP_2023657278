@extends('admin.adminhome')
@section('content')
<div class="container py-4">
    <h3 class="fw-bold mb-4">📝 Manage Exam Repository</h3>

    <div class="card mb-4 border-0 shadow-sm bg-light">
        <div class="card-body">
            <h6 class="fw-bold mb-3">Create New Exam Paper</h6>
            <form action="{{ route('repo.exams.store') }}" method="POST" class="row g-2 align-items-end">
                @csrf
                <div class="col-md-5">
                    <label class="small text-muted">Exam Title</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. SPM Mathematics 2023" required>
                </div>
                <div class="col-md-3">
                    <label class="small text-muted">Subject</label>
                    <input type="text" name="subject" class="form-control" placeholder="e.g. Mathematics" required>
                </div>
                <div class="col-md-2">
                    <label class="small text-muted">Year</label>
                    <input type="text" name="year" class="form-control" placeholder="2023" required>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-dark w-100">Create</button>
                </div>
            </form>
        </div>
    </div>

    <div class="list-group shadow-sm">
        @foreach($papers as $paper)
        <div class="list-group-item d-flex justify-content-between align-items-center p-3">
            <div>
                <h5 class="mb-1 fw-bold">{{ $paper->title }}</h5>
                <small class="text-muted">{{ $paper->subject }} • {{ $paper->year }}</small>
                <span class="badge bg-secondary ms-2">{{ $paper->questions_count }} Questions</span>
            </div>
            <div>
                <a href="{{ route('repo.questions.index', $paper->id) }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-edit me-1"></i> Manage Questions
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection