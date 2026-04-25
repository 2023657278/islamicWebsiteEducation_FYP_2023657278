@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center align-items-center">
        <div class="col-lg-5 col-md-7">
            <div class="card shadow-lg p-4 border-0">
                <div class="card-title text-center mb-4">
                    <h2 class="fw-bold text-success">{{ __('Create Account') }}</h2>
                    <p class="text-muted">Join our community in just a few simple steps.</p>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="form-floating mb-3">
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                                name="name" value="{{ old('name') }}" required autocomplete="name" autofocus 
                                placeholder="Full Name">
                            <label for="name">{{ __('Full Name') }}</label>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input id="no_maktab" type="text" class="form-control @error('no_maktab') is-invalid @enderror" 
                                name="no_maktab" value="{{ old('no_maktab') }}" required 
                                placeholder="No. Maktab">
                            <label for="no_maktab">{{ __('No. Maktab') }}</label>
                            @error('no_maktab')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                                name="email" value="{{ old('email') }}" required autocomplete="email" 
                                placeholder="name@example.com">
                            <label for="email">{{ __('Email Address') }}</label>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input id="phone_number" type="text" class="form-control @error('phone_number') is-invalid @enderror" 
                                name="phone_number" value="{{ old('phone_number') }}" required autocomplete="phone_number" 
                                placeholder="e.g. 123-456-7890">
                            <label for="phone_number">{{ __('Phone Number') }}</label>
                            @error('phone_number')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                                name="password" required autocomplete="new-password"
                                placeholder="Password">
                            <label for="password">{{ __('Password') }}</label>
                            <div class="form-text text-muted small px-2">
                                * Must be <strong>at least 9 characters</strong>, include <strong>A-Z</strong>, <strong>a-z</strong>, <strong>0-9</strong>, and a <strong>special symbol</strong>.
                            </div>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-floating mb-4">
                            <input id="password-confirm" type="password" class="form-control" 
                                name="password_confirmation" required autocomplete="new-password"
                                placeholder="Confirm Password">
                            <label for="password-confirm">{{ __('Confirm Password') }}</label>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-success btn-lg">
                                {{ __('Register') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <p class="text-center mt-3 text-white">
                Already have an account? <a href="{{ route('login') ?? '#' }}" class="ttext-decoration-none text-white font-weight-bold hover:text-light">Sign In</a>
            </p>
        </div>
    </div>
</div>
@endsection