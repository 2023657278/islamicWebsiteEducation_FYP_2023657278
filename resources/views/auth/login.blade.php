@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center align-items-center" style="min-height: 75vh;">
        <div class="col-lg-4 col-md-6 col-sm-10">
            
            <div class="rainbow-card-wrapper">
                <div class="card p-4">
                    <div class="card-title text-center mb-4">
                        <h2 class="fw-bold text-primary mb-3">{{ __('Welcome') }}</h2>
                        <img src="{{ asset('image/logoMRSM.png') }}" alt="MRSM Logo" width="100" class="img-fluid mb-2">
                        <hr>
                        <p class="text-muted small">Sign in to continue to your account.</p>
                    </div>

                    <div class="card-body p-0">
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

                            <div class="form-floating mb-4">
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

                            <div class="d-grid mb-2">
                                <button type="submit" class="btn btn-primary btn-lg font-weight-bold shadow-sm" style="border-radius: 10px;">
                                    {{ __('Login') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div> <p class="text-center mt-4 text-white font-weight-bold">
                Don't have an account? <a href="{{ route('register') ?? '#' }}" class="text-decoration-none text-warning">Sign Up</a>
            </p>
        </div>
    </div>
</div>
@endsection