@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-xl-5 col-lg-6 col-md-8 col-sm-10">
            <div class="card edu-card p-4 border-0">
                <div class="text-center mb-4">
                    <div class="mb-2">
                        <span class="p-3 bg-light d-inline-block rounded-circle" style="background-color: #f0fdf4 !important;">
                            <i class="fas fa-user-graduate text-success fa-xl"></i>
                        </span>
                    </div>
                    <h3 class="fw-extrabold text-dark mb-1" style="letter-spacing: -0.5px;">{{ __('Create Student Account') }}</h3>
                    <p class="text-muted small">Join your classmates and start tracking your quiz revision progress.</p>
                </div>

                <div class="card-body p-0">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="form-floating edu-input-group mb-3">
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                                name="name" value="{{ old('name') }}" required autocomplete="name" autofocus 
                                placeholder="Full Name">
                            <label for="name"><i class="fas fa-user text-muted mr-2"></i>{{ __('Full Name') }}</label>
                            @error('name')
                                <span class="invalid-feedback font-weight-bold small mt-1" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating edu-input-group mb-3">
                                    <input id="no_maktab" type="text" class="form-control @error('no_maktab') is-invalid @enderror" 
                                        name="no_maktab" value="{{ old('no_maktab') }}" required 
                                        placeholder="No. Maktab">
                                    <label for="no_maktab"><i class="fas fa-id-card text-muted mr-2"></i>{{ __('No. Maktab') }}</label>
                                    @error('no_maktab')
                                        <span class="invalid-feedback font-weight-bold small mt-1" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating edu-input-group mb-3">
                                    <input id="phone_number" type="text" class="form-control @error('phone_number') is-invalid @enderror" 
                                        name="phone_number" value="{{ old('phone_number') }}" required autocomplete="phone_number" 
                                        placeholder="e.g. 0123456789">
                                    <label for="phone_number"><i class="fas fa-phone text-muted mr-2"></i>{{ __('Phone Number') }}</label>
                                    @error('phone_number')
                                        <span class="invalid-feedback font-weight-bold small mt-1" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-floating edu-input-group mb-3">
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                                name="email" value="{{ old('email') }}" required autocomplete="email" 
                                placeholder="name@example.com">
                            <label for="email"><i class="fas fa-envelope text-muted mr-2"></i>{{ __('Email Address') }}</label>
                            @error('email')
                                <span class="invalid-feedback font-weight-bold small mt-1" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-floating edu-input-group mb-3">
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                                name="password" required autocomplete="new-password"
                                placeholder="Password">
                            <label for="password"><i class="fas fa-lock text-muted mr-2"></i>{{ __('Password') }}</label>
                            @error('password')
                                <span class="invalid-feedback font-weight-bold small mt-1" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-floating edu-input-group mb-3">
                            <input id="password-confirm" type="password" class="form-control" 
                                name="password_confirmation" required autocomplete="new-password"
                                placeholder="Confirm Password">
                            <label for="password-confirm"><i class="fas fa-circle-check text-muted mr-2"></i>{{ __('Confirm Password') }}</label>
                        </div>

                        <div class="p-3 mb-4 rounded border text-xs text-secondary" style="background: #f8fafc; border-color: #e2e8f0 !important; line-height: 1.5;">
                            <i class="fas fa-shield-halved mr-1 text-success"></i> <b>Password Security Criteria:</b> Minimum 9 characters with uppercase, lowercase, numbers, and symbols.
                        </div>

                        <div class="d-grid mb-2">
                            <button type="submit" class="btn btn-success btn-lg font-weight-bold shadow-sm py-3" style="border-radius: 12px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none; font-size: 0.95rem;">
                                {{ __('Complete Registration') }} <i class="fas fa-check-circle ml-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <p class="text-center mt-4 text-secondary small">
                Already have an academic account? <a href="{{ route('login') ?? '#' }}" class="text-decoration-none text-primary font-weight-bold ml-1">Sign In instead</a>
            </p>
        </div>
    </div>
</div>
@endsection