@extends('admin.adminhome')

@section('content')

<div class="card">
    <div class="card-header bg-primary text-white position-relative">
        <h5 class="mb-0"><i class="fas fa-book mr-2"></i>Subjects</h5>
        <a href="{{ route('subjects.create') }}" class="btn btn-maroon btn-sm position-absolute top-right-btn">
            <i class="fas fa-plus-circle me-1"></i> Add Subject
        </a>
    </div>

    <div class="card-body bg-light border-bottom">
        <form action="{{ route('subjects.index') }}" method="GET" class="row g-2">
            <div class="col-md-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Search Subject Name..." 
                           value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="fas fa-barcode text-muted"></i></span>
                    <input type="text" name="code" class="form-control" 
                           placeholder="Subject Code..." 
                           value="{{ request('code') }}">
                </div>
            </div>

            <div class="col-md-2 d-flex">
                <button type="submit" class="btn btn-primary btn-sm w-100 me-1">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                @if(request('search') || request('code'))
                <a href="{{ route('subjects.index') }}" class="btn btn-secondary btn-sm" title="Clear Filters">
                    <i class="fas fa-undo"></i>
                </a>
                @endif
            </div>
        </form>
    </div>
    
    @if ($message = Session::get('success'))
        <div class="alert alert-success m-3 py-2 small">
            <i class="fas fa-check-circle me-1"></i> {{ $message }}
            <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-3" style="width: 150px;">Code</th>
                        <th>Subject Name</th>
                        <th class="text-center" style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($subjects as $s)
                    <tr>
                        <td class="ps-3">
                            <span class="badge bg-secondary text-white font-monospace">
                                {{ $s->subject_code }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $s->subject_name }}</div>
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('subjects.show', $s->id) }}" class="btn btn-sm btn-info text-white shadow-sm" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('subjects.edit', $s->id) }}" class="btn btn-sm btn-warning text-white shadow-sm" title="Edit Subject">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('subjects.destroy', $s->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger shadow-sm" onclick="return confirm('Delete this subject?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center py-5">
                            <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No subjects found matching your criteria.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="card-footer bg-white py-2">
        <small class="text-muted">Showing <strong>{{ $subjects->count() }}</strong> subjects.</small>
    </div>
</div>

<style>
    /* Consistent Styles */
    .card { border-radius: 8px; border: 1px solid #000; overflow: hidden; }
    .btn-maroon { background-color: #800000; border-color: #800000; color: white; }
    .btn-maroon:hover { background-color: #660000; color: white; }
    .top-right-btn { right: 15px; top: 50%; transform: translateY(-50%); }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .row.g-2 > div { margin-bottom: 10px; }
    }
</style>

@endsection