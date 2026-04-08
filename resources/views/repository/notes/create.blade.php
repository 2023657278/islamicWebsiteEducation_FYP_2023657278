@extends('admin.adminhome')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold m-0">Upload Public Note</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('repo.notes.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold">Note Title</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. History Chapter 4 Summary" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Subject / Tag</label>
                            <input type="text" name="subject_tag" class="form-control" placeholder="e.g. History Form 5" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">PDF / Document File</label>
                            <input type="file" name="file" class="form-control" accept=".pdf,.docx,.doc" required>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('repo.notes.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-success px-4">Upload Note</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection