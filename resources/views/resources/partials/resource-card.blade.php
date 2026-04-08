<div class="card border-0 h-100" style="border-radius: 12px 12px 0 0; background: #fff; position: relative;">
    @if($res->type == 'video')
        <div class="ratio ratio-16x9 bg-dark" style="border-radius: 12px 12px 0 0; overflow: hidden;">
            <iframe src="https://www.youtube.com/embed/{{ $res->file_url }}" allowfullscreen style="width: 100%; border: none; aspect-ratio: 16/9;"></iframe>
        </div>
        <div class="card-body p-3 d-flex flex-column">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <h6 class="font-weight-bold text-dark mb-0 text-truncate" style="max-width: 85%;">{{ $res->title }}</h6>
                <div class="dropdown">
                    <button class="btn btn-sm text-muted p-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
                    <div class="dropdown-menu dropdown-menu-right shadow border-0" style="z-index: 9999;">
                        <form action="{{ route('resources.destroy', $res->id) }}" method="POST">@csrf @method('DELETE')<button type="submit" class="dropdown-item text-danger" onclick="return confirm('Delete video?')"><i class="fas fa-trash mr-2"></i> Delete</button></form>
                    </div>
                </div>
            </div>
            <div class="mb-2"><span class="badge bg-light text-dark border">{{ $res->subject->subject_name }}</span> <span class="badge text-white" style="background:#be185d;">Video</span></div>
            <div class="mt-auto pt-2 border-top d-flex justify-content-between align-items-center"><small class="text-muted"><i class="fab fa-youtube text-danger mr-1"></i> YouTube</small><small class="text-muted">{{ $res->created_at->format('d M') }}</small></div>
        </div>
    @else
        <div class="card-body p-4 d-flex flex-column h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
                @php $ext = pathinfo($res->file_url, PATHINFO_EXTENSION); $bg = ($ext == 'pdf') ? 'bg-danger text-white' : 'bg-primary text-white'; @endphp
                <div class="rounded d-flex align-items-center justify-content-center {{ $bg }}" style="width: 48px; height: 48px;"><i class="fas {{ $ext == 'pdf' ? 'fa-file-pdf' : 'fa-file-alt' }} fa-lg"></i></div>
                <div class="dropdown">
                    <button class="btn btn-sm text-muted p-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
                    <div class="dropdown-menu dropdown-menu-right shadow border-0" style="z-index: 9999;">
                        <a class="dropdown-item" href="{{ route('resources.preview', $res->id) }}" target="_blank"><i class="fas fa-eye mr-2"></i> Preview</a>
                        <a class="dropdown-item" href="{{ route('resources.download', $res->id) }}"><i class="fas fa-download mr-2"></i> Download</a>
                        <div class="dropdown-divider"></div>
                        <form action="{{ route('resources.destroy', $res->id) }}" method="POST">@csrf @method('DELETE')<button type="submit" class="dropdown-item text-danger" onclick="return confirm('Delete resource?')"><i class="fas fa-trash mr-2"></i> Delete</button></form>
                    </div>
                </div>
            </div>
            <h6 class="font-weight-bold text-dark mb-1 text-truncate">{{ $res->title }}</h6>
            <div class="mb-3"><span class="badge bg-light text-dark border">{{ $res->subject->subject_name ?? 'General' }}</span> <span class="badge {{ $res->type == 'note' ? 'bg-info text-white' : 'bg-warning text-dark' }}">{{ ucfirst($res->type) }}</span></div>
            <div class="mt-auto d-flex justify-content-between align-items-center pt-3 border-top"><small class="text-muted font-weight-bold text-uppercase">{{ $ext }}</small><small class="text-muted">{{ $res->created_at->format('d M') }}</small></div>
        </div>
    @endif
</div>