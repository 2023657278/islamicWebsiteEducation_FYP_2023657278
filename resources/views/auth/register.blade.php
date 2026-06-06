@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-8 col-sm-10">
            <div class="card glass-card shadow-2xl p-4 border-0">
                <div class="text-center mb-4">
                    <div class="mb-2">
                        <i class="fas fa-user-plus text-success fa-2x"></i>
                    </div>
                    <h3 class="fw-bold text-white mb-1">{{ __('Create Account') }}</h3>
                    <p class="text-white-50 small">Register your parameters to gain institutional entry.</p>
                </div>

                <div class="card-body p-0">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="form-floating glass-input-group mb-3">
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                                name="name" value="{{ old('name') }}" required autocomplete="name" autofocus 
                                placeholder="Full Name">
                            <label for="name"><i class="fas fa-user mr-2"></i>{{ __('Full Name') }}</label>
                            @error('name')
                                <span class="invalid-feedback font-weight-bold text-xs mt-1" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating glass-input-group mb-3">
                                    <input id="no_maktab" type="text" class="form-control @error('no_maktab') is-invalid @enderror" 
                                        name="no_maktab" value="{{ old('no_maktab') }}" required 
                                        placeholder="No. Maktab">
                                    <label for="no_maktab"><i class="fas fa-id-card mr-2"></i>{{ __('No. Maktab') }}</label>
                                    @error('no_maktab')
                                        <span class="invalid-feedback font-weight-bold text-xs mt-1" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating glass-input-group mb-3">
                                    <input id="phone_number" type="text" class="form-control @error('phone_number') is-invalid @enderror" 
                                        name="phone_number" value="{{ old('phone_number') }}" required autocomplete="phone_number" 
                                        placeholder="e.g. 0123456789">
                                    <label for="phone_number"><i class="fas fa-phone mr-2"></i>{{ __('Phone Number') }}</label>
                                    @error('phone_number')
                                        <span class="invalid-feedback font-weight-bold text-xs mt-1" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-floating glass-input-group mb-3">
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                                name="email" value="{{ old('email') }}" required autocomplete="email" 
                                placeholder="name@example.com">
                            <label for="email"><i class="fas fa-envelope mr-2"></i>{{ __('Email Address') }}</label>
                            @error('email')
                                <span class="invalid-feedback font-weight-bold text-xs mt-1" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-floating glass-input-group mb-3">
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                                name="password" required autocomplete="new-password"
                                placeholder="Password">
                            <label for="password"><i class="fas fa-lock mr-2"></i>{{ __('Password') }}</label>
                            @error('password')
                                <span class="invalid-feedback font-weight-bold text-xs mt-1" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-floating glass-input-group mb-3">
                            <input id="password-confirm" type="password" class="form-control" 
                                name="password_confirmation" required autocomplete="new-password"
                                placeholder="Confirm Password">
                            <label for="password-confirm"><i class="fas fa-check-double mr-2"></i>{{ __('Confirm Password') }}</label>
                        </div>

                        <div class="p-3 mb-4 rounded border border-secondary text-xs" style="background: rgba(255,255,255,0.02); color: rgba(255,255,255,0.6); line-height: 1.5;">
                            <i class="fas fa-info-circle mr-1 text-info"></i> Security policy: Password must be <b>at least 9 characters</b>, containing combinations of <b>A-Z</b>, <b>a-z</b>, <b>0-9</b>, and special symbols.
                        </div>

                        <div class="d-grid mb-2">
                            <button type="submit" class="btn btn-success btn-lg font-weight-bold shadow-sm" style="border-radius: 10px; background-color: #10b981; border: none; font-size: 1rem; py: 12px;">
                                {{ __('Register') }} <i class="fas fa-user-check ml-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <p class="text-center mt-4 text-white-50 small">
                Already have an account? <a href="{{ route('login') ?? '#' }}" class="text-decoration-none text-white font-weight-bold ml-1 hover-underline">Sign In</a>
            </p>
        </div>
    </div>
</div>
@endsection