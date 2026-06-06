@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center align-items-center" style="min-height: 75vh;">
        <div class="col-lg-5 col-md-7 col-sm-10">
            
            <div class="rainbow-card-wrapper">
                <div class="card p-4">
                    
                    <div class="text-center mb-4">
                        <div class="mb-2">
                            <span class="p-3 d-inline-block rounded-circle" style="background-color: #f0fdf4 !important;">
                                <i class="fas fa-user-graduate text-success fa-xl"></i>
                            </span>
                        </div>
                        <h3 class="fw-extrabold text-dark mb-1" style="font-weight: 800; letter-spacing: -0.5px;">Join the Classroom! ✏️</h3>
                        <p class="text-muted small">Register your parameters to start your learning journey.</p>
                        <hr style="border-color: #e2e8f0; opacity: 0.8;">
                    </div>

                    <div class="card-body p-0">
                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <div class="form-floating mb-3">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                                    name="name" value="{{ old('name') }}" required autocomplete="name" autofocus 
                                    placeholder="Full Name"
                                    style="border-radius: 12px; padding: 14px 16px; border: 1px solid #cbd5e1; background-color: #f8fafc;">
                                <label for="name" style="color: #64748b; padding-left: 16px;">
                                    <i class="fas fa-user text-muted mr-2" style="opacity: 0.7;"></i> Full Name
                                </label>
                                @error('name')
                                    <span class="invalid-feedback font-weight-bold small mt-1" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-floating mb-3">
                                <input id="no_maktab" type="text" class="form-control @error('no_maktab') is-invalid @enderror" 
                                    name="no_maktab" value="{{ old('no_maktab') }}" required 
                                    placeholder="No. Maktab"
                                    style="border-radius: 12px; padding: 14px 16px; border: 1px solid #cbd5e1; background-color: #f8fafc;">
                                <label for="no_maktab" style="color: #64748b; padding-left: 16px;">
                                    <i class="fas fa-id-card text-muted mr-2" style="opacity: 0.7;"></i> No. Maktab
                                </label>
                                @error('no_maktab')
                                    <span class="invalid-feedback font-weight-bold small mt-1" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-floating mb-3">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                                    name="email" value="{{ old('email') }}" required autocomplete="email" 
                                    placeholder="name@example.com"
                                    style="border-radius: 12px; padding: 14px 16px; border: 1px solid #cbd5e1; background-color: #f8fafc;">
                                <label for="email" style="color: #64748b; padding-left: 16px;">
                                    <i class="fas fa-envelope text-muted mr-2" style="opacity: 0.7;"></i> Email Address
                                </label>
                                @error('email')
                                    <span class="invalid-feedback font-weight-bold small mt-1" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-floating mb-3">
                                <input id="phone_number" type="text" class="form-control @error('phone_number') is-invalid @enderror" 
                                    name="phone_number" value="{{ old('phone_number') }}" required autocomplete="phone_number" 
                                    placeholder="Phone Number"
                                    style="border-radius: 12px; padding: 14px 16px; border: 1px solid #cbd5e1; background-color: #f8fafc;">
                                <label for="phone_number" style="color: #64748b; padding-left: 16px;">
                                    <i class="fas fa-phone text-muted mr-2" style="opacity: 0.7;"></i> Phone Number
                                </label>
                                @error('phone_number')
                                    <span class="invalid-feedback font-weight-bold small mt-1" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-floating mb-3">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                                    name="password" required autocomplete="new-password"
                                    placeholder="Password"
                                    style="border-radius: 12px; padding: 14px 16px; border: 1px solid #cbd5e1; background-color: #f8fafc;">
                                <label for="password" style="color: #64748b; padding-left: 16px;">
                                    <i class="fas fa-lock text-muted mr-2" style="opacity: 0.7;"></i> Password
                                </label>
                                @error('password')
                                    <span class="invalid-feedback font-weight-bold small mt-1" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-floating mb-3">
                                <input id="password-confirm" type="password" class="form-control" 
                                    name="password_confirmation" required autocomplete="new-password"
                                    placeholder="Confirm Password"
                                    style="border-radius: 12px; padding: 14px 16px; border: 1px solid #cbd5e1; background-color: #f8fafc;">
                                <label for="password-confirm" style="color: #64748b; padding-left: 16px;">
                                    <i class="fas fa-check-double text-muted mr-2" style="opacity: 0.7;"></i> Confirm Password
                                </label>
                            </div>

                            <div class="p-3 mb-4 rounded border text-xs text-secondary" style="background: #f8fafc; border-color: #cbd5e1 !important; line-height: 1.5; border-radius: 10px;">
                                <i class="fas fa-shield-halved mr-1 text-success"></i> <b>Password Criteria:</b> Minimum 9 characters containing combinations of uppercase, lowercase, numbers, and symbols.
                            </div>

                            <div class="d-grid mb-2">
                                <button type="submit" class="btn btn-success btn-lg font-weight-bold text-white shadow-sm py-3" 
                                    style="border-radius: 12px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none; font-size: 0.95rem;">
                                    Complete Registration <i class="fas fa-check-circle ml-2"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div> <p class="text-center mt-4 text-white font-weight-bold">
                Already have an account? <a href="{{ route('login') ?? '#' }}" class="text-decoration-none text-warning">Sign In</a>
            </p>
        </div>
    </div>
</div>
@endsection