@extends('admin.adminhome')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card-header bg-primary text-white">
    <table style="width: 100%;">
        <tr>
            <td style="text-align: left;">
                <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i> Edit Teacher Profile</h5>
            </td>
            <td style="text-align: right;">
                <span class="badge bg-white text-primary fw-bold">TEACHER ID: #{{ $teacher->id }}</span>
            </td>
        </tr>
    </table>
</div>

                <div class="card-body bg-light">
                    <form action="{{ route('teachers.update', $teacher->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="small font-weight-bold text-dark">Full Name</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $teacher->name) }}" required>
                                @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="small font-weight-bold text-dark">Email Address</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ old('email', $teacher->email) }}" required>
                                @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="small font-weight-bold text-dark">Phone Number</label>
                                <input type="text" name="phone_number" class="form-control" 
                                       value="{{ old('phone_number', $teacher->phone_number) }}" placeholder="e.g. 012-3456789">
                            </div>
                        </div>

                        <div class="card border-warning mb-4 mt-2">
                            <div class="card-header bg-warning-subtle py-2">
                                <span class="small fw-bold text-dark">
                                    <i class="fas fa-key me-1"></i> Security Update 
                                    <span class="text-muted fw-normal">(Leave blank to keep current password)</span>
                                </span>
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
                            <a href="{{ route('teachers.index') }}" class="btn btn-secondary shadow-sm">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-maroon shadow-sm px-4">
                                <i class="fas fa-save me-1"></i> Update Teacher Record
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* MRSM Terendak Theme */
.card { border-radius: 12px; border: 1px solid #000; overflow: hidden; }
.btn-maroon { background-color: #800000; border-color: #800000; color: white; }
.btn-maroon:hover { background-color: #660000; color: white; }
.bg-warning-subtle { background-color: #fff3cd; }
.border-warning { border: 1px solid #ffc107 !important; }
.form-control:focus { border-color: #800000; box-shadow: 0 0 0 0.2rem rgba(128, 0, 0, 0.1); }
</style>
@endsection