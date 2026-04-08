@extends('admin.adminhome')

@section('content')

<div class="card">
    <div class="card-header bg-primary text-white position-relative">
        <h5 class="mb-0"><i class="fas fa-layer-group me-2"></i>Class Groups</h5>
        <a href="{{ route('groups.create') }}" class="btn btn-maroon btn-sm position-absolute top-right-btn">
            <i class="fas fa-plus-circle me-1"></i> Add New Group
        </a>
    </div>

    <div class="card-body bg-light border-bottom">
        <form action="{{ route('groups.index') }}" method="GET" class="row g-2">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="fas fa-users text-muted"></i></span>
                    <input type="text" name="search_name" class="form-control" 
                           placeholder="Search Class Name (e.g. 4 Amanah)" 
                           value="{{ request('search_name') }}">
                </div>
            </div>

            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="fas fa-calendar-check text-muted"></i></span>
                    <input type="text" name="search_year" class="form-control" 
                           placeholder="Search Year (e.g. 2025)" 
                           value="{{ request('search_year') }}">
                </div>
            </div>

            <div class="col-md-2 d-flex">
                <button type="submit" class="btn btn-primary btn-sm w-100 me-1">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                @if(request('search_name') || request('search_year'))
                <a href="{{ route('groups.index') }}" class="btn btn-secondary btn-sm" title="Clear Filters">
                    <i class="fas fa-undo"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success m-3 py-2 small">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-3" style="width: 80px;">ID</th>
                        <th>Group Name</th>
                        <th>Academic Year</th>
                        <th class="text-center">Enrolled Students</th>
                        <th class="text-center">Created At</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($groups as $group)
                    <tr>
                        <td class="ps-3 fw-bold text-dark">#{{ $group->id }}</td>
                        <td>
                            <div class="fw-bold">{{ $group->group_name }}</div>
                        </td>
                        <td>
                            <span class="badge bg-info text-dark px-2">
                                {{ $group->year->year ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border px-3">
                                <i class="fas fa-user-graduate text-primary me-1"></i> {{ $group->students_count }}
                            </span>
                        </td>
                        <td class="text-center small text-muted">
                            {{ $group->created_at ? $group->created_at->format('d M Y') : 'No Date Set' }}
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('groups.show', $group->id) }}" class="btn btn-sm btn-info text-white shadow-sm">
                                    <i class="fas fa-chart-pie"></i>
                                </a>
                                <a href="{{ route('groups.edit', $group->id) }}" class="btn btn-sm btn-warning text-white shadow-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('groups.destroy', $group->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger shadow-sm" onclick="return confirm('Delete this group?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="fas fa-layer-group fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No groups found for the selected criteria.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white py-2">
        <small class="text-muted">Results matching filters: <strong>{{ $groups->count() }}</strong> groups.</small>
    </div>
</div>

<style>
    /* Consistent Styles with Teacher Table */
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