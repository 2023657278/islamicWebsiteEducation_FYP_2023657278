@extends('admin.adminhome')

@section('content')

<div class="card">
    <div class="card-header bg-primary text-white position-relative">
        <h5 class="mb-0"><i class="fas fa-calendar-alt mr-2"></i>Master Timetable</h5>
        <a href="{{ route('timetables.create') }}" class="btn btn-maroon btn-sm position-absolute top-right-btn">
            <i class="fas fa-plus-circle me-1"></i> Add Assignment
        </a>
    </div>

    <div class="card-body bg-light border-bottom">
        <form action="{{ route('timetables.index') }}" method="GET" class="row g-2">
            
            <div class="col-md-2">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="fas fa-users text-muted"></i></span>
                    <input type="text" name="group_name" class="form-control" placeholder="Class..." value="{{ request('group_name') }}">
                </div>
            </div>
            
            <div class="col-md-2">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="fas fa-calendar text-muted"></i></span>
                    <input type="text" name="academic_year" class="form-control" placeholder="Year..." value="{{ request('academic_year') }}">
                </div>
            </div>

            <div class="col-md-2">
                <input type="text" name="teacher" class="form-control form-control-sm" placeholder="Instructor..." value="{{ request('teacher') }}">
            </div>
            
            <div class="col-md-2">
                <input type="text" name="subject" class="form-control form-control-sm" placeholder="Subject..." value="{{ request('subject') }}">
            </div>
            
            <div class="col-md-2">
                <select name="day" class="form-control form-control-sm">
                    <option value="">Any Day</option>
                    @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $d)
                        <option value="{{ $d }}" {{ request('day') == $d ? 'selected' : '' }}>{{ $d }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-2 d-flex">
                <button type="submit" class="btn btn-primary btn-sm w-100 me-1">
                    <i class="fas fa-filter me-1"></i>
                </button>
                @if(request()->anyFilled(['group_name', 'academic_year', 'teacher', 'subject', 'day']))
                <a href="{{ route('timetables.index') }}" class="btn btn-secondary btn-sm" title="Clear Filters">
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
                        <th class="ps-3">Instructor</th>
                        <th>Subject</th>
                        <th>Placement</th>
                        <th class="text-center">Year</th>
                        <th class="text-center">Schedule</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($timetables as $t)
                    <tr>
                        <td class="ps-3">
                            <div class="fw-bold text-dark">{{ $t->teacher->name }}</div>
                            <small class="text-muted">ID: #{{ $t->teacher->id }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border px-2 py-1">
                                {{ $t->subject->subject_name }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-secondary text-white">{{ $t->group->group_name }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info text-dark">{{ $t->group->year->year ?? 'N/A' }}</span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex flex-column align-items-center">
                                <span class="fw-bold text-dark">{{ $t->day->day_name }}</span>
                                <small class="text-muted">{{ $t->time_from }} - {{ $t->time_to }}</small>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('timetables.show', $t->id) }}" class="btn btn-sm btn-info text-white shadow-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('timetables.edit', $t->id) }}" class="btn btn-sm btn-warning text-white shadow-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('timetables.destroy', $t->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger shadow-sm" onclick="return confirm('Archive this record?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No timetable assignments found matching your criteria.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="card-footer bg-white py-2">
        <small class="text-muted">Showing <strong>{{ $timetables->count() }}</strong> assignments.</small>
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