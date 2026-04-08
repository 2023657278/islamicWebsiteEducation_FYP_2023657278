@extends('users.students')

@section('content')
<div class="container-fluid p-0">
    
    <div class="mb-4 text-start">
        <h2 class="fw-bold text-dark">Teacher Resources</h2>
        <p class="text-muted">Access videos and notes shared by your teachers</p>
    </div>

    {{-- STATS CARDS --}}
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 rounded-4">
                <div class="d-flex align-items-center text-start">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3">
                        <i class="fas fa-chalkboard-teacher text-primary fa-2x"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-0 text-dark">{{ $teachers->count() }}</h3>
                        <div class="text-muted small">Total Teachers</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 rounded-4">
                <div class="d-flex align-items-center text-start">
                    <div class="bg-info bg-opacity-10 p-3 rounded-circle me-3">
                        <i class="fas fa-video text-info fa-2x"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-0 text-dark">{{ $totalVideos }}</h3>
                        <div class="text-muted small">Total Videos</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 rounded-4">
                <div class="d-flex align-items-center text-start">
                    <div class="bg-danger bg-opacity-10 p-3 rounded-circle me-3">
                        <i class="fas fa-file-pdf text-danger fa-2x"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-0 text-dark">{{ $totalNotes }}</h3>
                        <div class="text-muted small">Total Notes</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SEARCH BAR --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form action="{{ route('student.resources.index') }}" method="GET">
                <div class="input-group">
                    <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-0" placeholder="Search teachers or subjects..." value="{{ request('search') }}">
                </div>
            </form>
        </div>
    </div>

    {{-- TEACHER LIST --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3">
            <div class="row fw-bold text-muted small text-uppercase text-start">
                <div class="col-md-5">Instructor</div>
                <div class="col-md-4">Subject</div>
                <div class="col-md-3 text-end">Resources</div>
            </div>
        </div>
        <div class="list-group list-group-flush">
            @forelse($teachers as $teacher)
            <a href="{{ route('student.resources.show', $teacher->id) }}" class="list-group-item list-group-item-action py-3 border-0 border-bottom">
                <div class="row align-items-center text-start">
                    <div class="col-md-5 d-flex align-items-center">
                        <div class="me-3" style="width: 45px; height: 45px; border-radius: 50%; background: #f0f2f5; display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0;">
                            {{-- ✅ FIXED: Added profile_images subfolder path --}}
                            @if($teacher->profile_image)
                                <img src="{{ asset('storage/profile_images/' . $teacher->profile_image) }}" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <span class="fw-bold text-secondary">{{ substr($teacher->name, 0, 2) }}</span>
                            @endif
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0">{{ $teacher->name }}</h6>
                            <small class="text-muted">Islamic Studies Teacher</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <span class="badge bg-warning text-dark bg-opacity-25 px-3 py-2 rounded-pill">
                            {{ $teacher->subject_name }}
                        </span>
                    </div>
                    <div class="col-md-3 text-end">
                        <span class="text-primary me-3"><i class="fas fa-video me-1"></i> {{ $teacher->video_count }}</span>
                        <span class="text-danger"><i class="fas fa-file-alt me-1"></i> {{ $teacher->note_count }}</span>
                    </div>
                </div>
            </a>
            @empty
                <div class="p-5 text-center text-muted">No teachers found for your class.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection