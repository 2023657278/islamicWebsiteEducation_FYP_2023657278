@extends('admin.adminhome')

@section('content')

<div class="card">
    <div class="card-header bg-primary text-white position-relative">
        <h5 class="mb-0"><i class="fas fa-user-graduate mr-2"></i>Students (MRSM Terendak)</h5>
        <a href="{{ route('students.create') }}" class="btn btn-maroon btn-sm position-absolute top-right-btn">
            <i class="fas fa-plus-circle me-1"></i> Add Student
        </a>
    </div>

    <div class="card-body bg-light border-bottom">
        <form action="{{ route('students.index') }}" method="GET" class="row g-2">
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Search Student Name..." 
                           value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="fas fa-users text-muted"></i></span>
                    <select name="group" class="form-control">
                        <option value="">All Groups</option>
                        @foreach($groups as $g)
                            <option value="{{ $g->id }}" {{ request('group') == $g->id ? 'selected' : '' }}>
                                {{ $g->group_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="fas fa-calendar-alt text-muted"></i></span>
                    <input type="number" name="year" class="form-control" 
                           placeholder="Reg. Year (e.g. 2025)" 
                           value="{{ request('year') }}">
                </div>
            </div>

            <div class="col-md-2 d-flex">
                <button type="submit" class="btn btn-primary btn-sm w-100 me-1">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                @if(request('search') || request('group') || request('year'))
                <a href="{{ route('students.index') }}" class="btn btn-secondary btn-sm" title="Clear Filters">
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
                        <th class="ps-3" style="width: 80px;">ID</th>
                        <th>Name</th>
                        <th>Group</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th class="text-center">Joined</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($students as $s)
                    <tr onclick="window.location='{{ route('students.show', $s->id) }}'" class="clickable-row">
                        <td class="ps-3 fw-bold text-dark">{{ $s->id }}</td>
                        <td>
                            <div class="fw-bold text-primary">{{ $s->name }}</div>
                        </td>
                        <td>
                            <span class="badge bg-secondary text-white px-2">
                                {{ $s->group->group_name ?? 'No Group' }}
                            </span>
                        </td>
                        <td>{{ $s->phone_number ?? '-' }}</td>
                        <td><small class="text-muted">{{ $s->email }}</small></td>
                        <td class="text-center small">
                            {{ $s->created_at->format('d/m/Y') }}
                        </td>
                        <td class="text-center" onclick="event.stopPropagation();">
                            <div class="btn-group">
                                <a href="{{ route('students.show', $s->id) }}" class="btn btn-sm btn-info text-white shadow-sm" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('students.edit', $s->id) }}" class="btn btn-sm btn-warning text-white shadow-sm" title="Edit Student">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('students.destroy', $s->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger shadow-sm" onclick="return confirm('Delete this student?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="fas fa-user-graduate fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No students found matching your criteria.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="card-footer bg-white py-2">
        <small class="text-muted">Showing <strong>{{ $students->count() }}</strong> registered students.</small>
    </div>
</div>

<style>
    /* Consistent Styles across all tables */
    .card { border-radius: 8px; border: 1px solid #000; overflow: hidden; }
    .btn-maroon { background-color: #800000; border-color: #800000; color: white; }
    .btn-maroon:hover { background-color: #660000; color: white; }
    .top-right-btn { right: 15px; top: 50%; transform: translateY(-50%); }

    /* Interactive Row Hover Effect */
    .clickable-row {
        cursor: pointer;
        transition: background-color 0.15s ease-in-out;
    }
    .clickable-row:hover {
        background-color: #e9ecef !important;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .row.g-2 > div { margin-bottom: 10px; }
    }
</style>

@endsection