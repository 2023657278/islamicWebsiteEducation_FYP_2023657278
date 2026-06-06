@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-4 col-md-6 col-sm-10">
            <div class="card glass-card shadow-2xl p-4 border-0">
                <div class="text-center mb-4">
                    <div class="mb-3">
                        <img src="{{ asset('image/logoMRSM.png') }}" alt="MRSM Logo" width="85" class="img-fluid drop-shadow">
                    </div>
                    <h3 class="fw-bold text-white mb-1">{{ __('Welcome Back') }}</h3>
                    <p class="text-white-50 small">Sign in to continue to your platform dashboard account.</p>
                </div>

                <div class="card-body p-0">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-floating glass-input-group mb-3">
                            <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" 
                                name="email" value="{{ old('email') }}" required autocomplete="email" autofocus 
                                placeholder="Email or No. Maktab">
                            <label for="email"><i class="fas fa-user-shield mr-2"></i>{{ __('Email Address or No. Maktab') }}</label>
                            @error('email')
                                <span class="invalid-feedback font-weight-bold text-xs mt-1" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-floating glass-input-group mb-4">
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                                name="password" required autocomplete="current-password"
                                placeholder="Password">
                            <label for="password"><i class="fas fa-key mr-2"></i>{{ __('Password') }}</label>
                            @error('password')
                                <span class="invalid-feedback font-weight-bold text-xs mt-1" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg font-weight-bold shadow-sm" style="border-radius: 10px; background-color: #3b82f6; border: none; font-size: 1rem; py: 12px;">
                                {{ __('Login') }} <i class="fas fa-sign-in-alt ml-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <p class="text-center mt-4 text-white-50 small">
                Don't have an academic profile? <a href="{{ route('register') ?? '#' }}" class="text-decoration-none text-white font-weight-bold ml-1 hover-underline">Sign Up Now</a>
            </p>
        </div>
    </div>
</div>
@endsection