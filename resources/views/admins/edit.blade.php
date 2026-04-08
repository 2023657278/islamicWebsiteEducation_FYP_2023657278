@extends('admin.adminhome')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0" style="border-radius: 20px;">
                <div class="card-header bg-warning text-dark p-4" style="border-radius: 20px 20px 0 0;">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-edit me-2"></i>Edit Administrator</h5>
                    <p class="mb-0 small opacity-75">Update profile details for {{ $admin->name }}</p>
                </div>
                <div class="card-body p-5">
                    <form action="{{ route('admins.update', $admin->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="fw-bold text-muted small text-uppercase">Full Name</label>
                                <input type="text" name="name" value="{{ $admin->name }}" class="form-control form-control-lg bg-light border-0" required>
                            </div>

                            <div class="col-md-6">
                                <label class="fw-bold text-muted small text-uppercase">Email Address</label>
                                <input type="email" name="email" value="{{ $admin->email }}" class="form-control form-control-lg bg-light border-0" required>
                            </div>

                            <div class="col-md-6">
                                <label class="fw-bold text-muted small text-uppercase">Phone Number</label>
                                <input type="text" name="phone_number" value="{{ $admin->phone_number }}" class="form-control form-control-lg bg-light border-0">
                            </div>

                            <div class="col-12">
                                <div class="alert alert-light border mt-3">
                                    <i class="fas fa-lock me-2"></i> Leave password fields empty if you do not wish to change it.
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="fw-bold text-muted small text-uppercase">New Password</label>
                                <input type="password" name="password" class="form-control form-control-lg bg-light border-0">
                            </div>

                            <div class="col-md-6">
                                <label class="fw-bold text-muted small text-uppercase">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control form-control-lg bg-light border-0">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-5">
                            <a href="{{ route('admins.index') }}" class="text-decoration-none text-muted fw-bold">Cancel</a>
                            <button type="submit" class="btn btn-warning btn-lg px-5 shadow-sm fw-bold" style="border-radius: 50px;">Update Profile</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection