@extends('admin.adminhome')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm border-dark">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i> Edit Student Profile</h5>
                        <span class="badge bg-white text-primary fw-bold px-3 py-2">STUDENT ID: #{{ $student->id }}</span>
                    </div>
                </div>

                <div class="card-body bg-light">
                    <form action="{{ route('students.update', $student->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="small font-weight-bold">Full Name</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $student->name) }}" required>
                                @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            {{-- ADDED: No. Maktab Field --}}
                            <div class="col-md-4 mb-3">
                                <label class="small font-weight-bold">No. Maktab</label>
                                <input type="text" name="no_maktab" class="form-control @error('no_maktab') is-invalid @enderror" 
                                       value="{{ old('no_maktab', $student->no_maktab) }}" required>
                                @error('no_maktab') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="small font-weight-bold">Email Address</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ old('email', $student->email) }}" required>
                                @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="small font-weight-bold">Group / Class (Academic Year)</label>
                                <select name="group_id" class="form-control @error('group_id') is-invalid @enderror" required>
                                    <option value="">-- Select Group --</option>
                                    @foreach($groups as $g)
                                        <option value="{{ $g->id }}" {{ (old('group_id', $student->group_id) == $g->id) ? 'selected' : '' }}>
                                            {{ $g->group_name }} ({{ $g->year->year ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('group_id') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label class="small font-weight-bold">Phone Number</label>
                                <input type="text" name="phone_number" class="form-control" 
                                       value="{{ old('phone_number', $student->phone_number) }}">
                            </div>
                        </div>

                        <div class="card border-warning mb-4 shadow-sm">
                            <div class="card-header bg-warning-subtle py-2">
                                <span class="small fw-bold text-dark"><i class="fas fa-key me-1"></i> Security Update </span>
                                <span class="small text-muted">(Leave blank to keep current password)</span>
                            </div>
                            <div class="card-body row py-3">
                                <div class="col-md-6">
                                    <label class="small font-weight-bold">New Password</label>
                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Min 6 characters">
                                    @error('password') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="small font-weight-bold">Confirm New Password</label>
                                    <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat password">
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mt-2">
                            <a href="{{ route('students.index') }}" class="btn btn-secondary shadow-sm">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-maroon shadow-sm px-4">
                                <i class="fas fa-save me-1"></i> Update Student Record
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
.bg-warning-subtle { background-color: #fff3cd; }
</style>
@endsection