@extends('users.students')

@section('content')
<div class="container-fluid p-0">
    
    {{-- BACK BUTTON --}}
    <a href="{{ route('student.resources.index') }}" class="text-muted text-decoration-none mb-4 d-inline-block">
        <i class="fas fa-arrow-left me-2"></i> Back to Teachers
    </a>

    {{-- HEADER PROFILE --}}
    <div class="d-flex align-items-center mb-5">
        <div class="me-4" style="width: 80px; height: 80px; border-radius: 50%; background: #fff; border: 4px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.1); overflow: hidden; display: flex; align-items: center; justify-content: center;">
            @if($teacher->profile_image)
                <img src="{{ asset('storage/' . $teacher->profile_image) }}" style="width: 100%; height: 100%; object-fit: cover;">
            @else
                <h2 class="fw-bold text-secondary m-0">{{ substr($teacher->name, 0, 2) }}</h2>
            @endif
        </div>
        <div>
            <h2 class="fw-bold text-dark mb-1">{{ $teacher->name }}</h2>
            <p class="text-muted mb-0">{{ $subjectName }}</p>
        </div>
    </div>

    {{-- TABS SECTION --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-bottom pt-3">
            <ul class="nav nav-tabs card-header-tabs border-0" id="myTab" role="tablist">
                <li class="nav-item me-4" role="presentation">
                    <button class="nav-link active border-0 bg-transparent fw-bold text-danger border-bottom border-3 border-danger pb-3" id="videos-tab" data-bs-toggle="tab" data-bs-target="#videos" type="button" role="tab">
                        <i class="fas fa-video me-2"></i> Videos ({{ $videos->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link border-0 bg-transparent fw-bold text-muted pb-3" id="notes-tab" data-bs-toggle="tab" data-bs-target="#notes" type="button" role="tab">
                        <i class="fas fa-file-alt me-2"></i> Notes ({{ $notes->count() }})
                    </button>
                </li>
            </ul>
        </div>
        
        <div class="card-body p-4">
            <div class="tab-content" id="myTabContent">
                
                {{-- VIDEOS TAB --}}
                <div class="tab-pane fade show active" id="videos" role="tabpanel">
                    <div class="row g-4">
                        @forelse($videos as $video)
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="ratio ratio-16x9">
                                    <iframe src="https://www.youtube.com/embed/{{ $video->file_url }}" allowfullscreen></iframe>
                                </div>
                                <div class="card-body">
                                    <h6 class="fw-bold text-dark mb-2">{{ $video->title }}</h6>
                                    <small class="text-muted">{{ $video->created_at->format('d M Y') }}</small>
                                </div>
                            </div>
                        </div>
                        @empty
                            <div class="col-12 text-center py-5 text-muted">No videos uploaded yet.</div>
                        @endforelse
                    </div>
                </div>

                {{-- NOTES TAB --}}
                <div class="tab-pane fade" id="notes" role="tabpanel">
                    <div class="list-group list-group-flush">
                        @forelse($notes as $note)
                        <div class="list-group-item border-0 border-bottom py-3 px-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="me-3 p-3 rounded bg-light text-danger">
                                        <i class="fas fa-file-pdf fa-lg"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1">{{ $note->title }}</h6>
                                        <small class="text-muted">Uploaded {{ $note->created_at->format('d M Y') }}</small>
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('resources.preview', $note->id) }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3 me-2">View</a>
                                    <a href="{{ route('resources.download', $note->id) }}" class="btn btn-outline-success btn-sm rounded-pill px-3">Download</a>
                                </div>
                            </div>
                        </div>
                        @empty
                            <div class="col-12 text-center py-5 text-muted">No notes uploaded yet.</div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection