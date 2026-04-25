@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-lg-4 col-md-6">
            <div class="card shadow-lg p-4 border-0">
                <div class="card-title text-center mb-4">
                    <h2 class="fw-bold text-primary">{{ __('Welcome') }}</h2>
                    <img src="{{ asset('image/logoMRSM.png') }}" alt="My Picture" width="100">
                    <hr>
                    <p class="text-muted">Sign in to continue to your account.</p>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-floating mb-3">
                            <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" 
                                name="email" value="{{ old('email') }}" required autocomplete="email" autofocus 
                                placeholder="Email or No. Maktab">
                            <label for="email">{{ __('Email Address or No. Maktab') }}</label>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                                name="password" required autocomplete="current-password"
                                placeholder="Password">
                            <label for="password">{{ __('Password') }}</label>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label text-muted" for="remember">
                                    {{ __('Remember Me') }}
                                </label>
                            </div>
                            @if (Route::has('password.request'))
                                <a class="text-decoration-none small" href="{{ route('password.request') }}">
                                    {{ __('Forgot Your Password?') }}
                                </a>
                            @endif
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                {{ __('Login') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <p class="text-center mt-3 text-white">
    Don't have an account? <a href="{{ route('register') ?? '#' }}" class="ttext-decoration-none text-white font-weight-bold hover:text-light">Sign Up</a>
</p>
        </div>
    </div>
</div>
@endsection