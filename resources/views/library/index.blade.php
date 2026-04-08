@extends('admin.adminhome')
@section('content')
<div class="container py-4">
    <h3 class="fw-bold mb-4">📚 Public Library</h3>
    
    <form class="mb-4 d-flex gap-2">
        <input type="text" name="search" class="form-control" placeholder="Search notes..." value="{{ $search ?? '' }}">
        <button class="btn btn-dark">Search</button>
    </form>

    <div class="row g-3">
        @foreach($notes as $note)
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <span class="badge bg-warning text-dark mb-2">{{ $note->subject_tag }}</span>
                    <h5 class="fw-bold">{{ $note->title }}</h5>
                    <p class="small text-muted">Uploaded on {{ $note->created_at->format('d M Y') }}</p>
                    
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <a href="#" class="btn btn-sm btn-outline-primary">Read Note</a>
                        
                        <form action="{{ route('library.bookmark', $note->id) }}" method="POST">
                            @csrf
                            <button class="btn btn-sm {{ in_array($note->id, $myBookmarks) ? 'btn-danger' : 'btn-outline-secondary' }}">
                                <i class="fas fa-bookmark"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection