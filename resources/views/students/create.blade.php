@extends('admin.adminhome')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-dark">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i> Register New Student</h5>
                </div>

                <div class="card-body bg-light">
                    <form action="{{ route('students.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="small font-weight-bold">Full Name</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Enter student name" value="{{ old('name') }}" required>
                                @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            {{-- ADDED: No. Maktab Field --}}
                            <div class="col-md-4 mb-3">
                                <label class="small font-weight-bold">No. Maktab</label>
                                <input type="text" name="no_maktab" class="form-control @error('no_maktab') is-invalid @enderror" placeholder="e.g. TB12345" value="{{ old('no_maktab') }}" required>
                                @error('no_maktab') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="small font-weight-bold">Email Address</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="student@mrsm.edu.my" value="{{ old('email') }}" required>
                                @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="small font-weight-bold">Initial Password</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Min. 6 characters" required>
                                @error('password') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="small font-weight-bold">Group / Class (Academic Year)</label>
                                <select name="group_id" class="form-control @error('group_id') is-invalid @enderror" required>
                                    <option value="">-- Select Group --</option>
                                    @foreach($groups as $g)
                                        <option value="{{ $g->id }}" {{ old('group_id') == $g->id ? 'selected' : '' }}>
                                            {{ $g->group_name }} ({{ $g->year->year ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Example: 4 Amanah (2025)</small>
                                @error('group_id') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="small font-weight-bold">Phone Number</label>
                                <input type="text" name="phone_number" class="form-control" placeholder="012-3456789" value="{{ old('phone_number') }}">
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('students.index') }}" class="btn btn-secondary shadow-sm">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-maroon shadow-sm px-4">
                                <i class="fas fa-check me-1"></i> Register Student
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card { border-radius: 12px; border: 1px solid #000; overflow: hidden; }
.btn-maroon { background-color: #800000; border-color: #800000; color: white; }
.btn-maroon:hover { background-color: #660000; color: white; }
</style>
@endsection