@extends('admin.adminhome')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary">
            <i class="fas fa-book-open me-2"></i>Subject Details
        </h2>
        <a href="{{ route('subjects.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-maroon text-white fw-bold">
                    Core Information
                </div>
                <div class="card-body bg-light">
                    <h3 class="fw-bold text-primary mb-1">{{ $subject->subject_name }}</h3>
                    <p class="text-muted mb-3"><i class="fas fa-hashtag me-1"></i> Code: <strong>{{ $subject->subject_code }}</strong></p>
                    <hr>
                    <p class="small text-muted">
                        This subject is part of the MRSM Terendak Form 4 core curriculum. 
                        Progress is tracked via student quiz scores and analyzed using K-Means clustering.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Active Teaching Assignments</h5>
                    <span class="badge bg-white text-primary">{{ $assignments->count() }} Classes</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small text-uppercase">
                                <tr>
                                    <th class="ps-3">Group Name</th>
                                    <th>Assigned Teacher</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assignments as $item)
                                <tr>
                                    <td class="ps-3 fw-bold">{{ $item->group->group_name }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user-tie me-2 text-muted"></i>
                                            {{ $item->teacher->name ?? 'Unassigned' }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('groups.show', $item->group_id) }}" class="btn btn-sm btn-outline-primary py-0">
                                            View Analytics
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">
                                        No teachers or groups assigned to this subject in the timetable yet.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-maroon { background-color: #800000; }
    .card { border-radius: 12px; border: 1px solid #ddd; overflow: hidden; }
</style>
@endsection