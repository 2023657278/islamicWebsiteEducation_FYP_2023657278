@extends('admin.adminhome')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0" style="border-radius: 20px;">
                <div class="card-header bg-dark text-white p-4" style="border-radius: 20px 20px 0 0;">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-user-shield me-2"></i>Register New Administrator</h5>
                    <p class="mb-0 small text-white-50">Create a new account with full system access privileges.</p>
                </div>
                <div class="card-body p-5">
                    <form action="{{ route('admins.store') }}" method="POST">
                        @csrf
                        
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="fw-bold text-muted small text-uppercase">Full Name</label>
                                <input type="text" name="name" class="form-control form-control-lg bg-light border-0" required>
                            </div>

                            <div class="col-md-6">
                                <label class="fw-bold text-muted small text-uppercase">Email Address</label>
                                <input type="email" name="email" class="form-control form-control-lg bg-light border-0" required>
                            </div>

                            <div class="col-md-6">
                                <label class="fw-bold text-muted small text-uppercase">Phone Number</label>
                                <input type="text" name="phone_number" class="form-control form-control-lg bg-light border-0">
                            </div>

                            <div class="col-md-6">
                                <label class="fw-bold text-muted small text-uppercase">Password</label>
                                <input type="password" name="password" class="form-control form-control-lg bg-light border-0" required>
                            </div>

                            <div class="col-md-6">
                                <label class="fw-bold text-muted small text-uppercase">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control form-control-lg bg-light border-0" required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-5">
                            <a href="{{ route('admins.index') }}" class="text-decoration-none text-muted fw-bold">Cancel</a>
                            <button type="submit" class="btn btn-dark btn-lg px-5 shadow-sm" style="border-radius: 50px;">Create Admin</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection