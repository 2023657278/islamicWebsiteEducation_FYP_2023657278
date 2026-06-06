@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-xl-4 col-lg-5 col-md-7 col-sm-10">
            <div class="card edu-card p-4 border-0">
                <div class="text-center mb-4">
                    <div class="mb-3">
                        <img src="{{ asset('image/logoMRSM.png') }}" alt="MRSM Logo" width="80" class="img-fluid">
                    </div>
                    <h3 class="fw-extrabold text-dark mb-1" style="letter-spacing: -0.5px;">Selamat Datang</h3>
                    <p class="text-muted small">Sign in to access your quizzes, analytics, and class timetables.</p>
                </div>

                <div class="card-body p-0">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-floating edu-input-group mb-3">
                            <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" 
                                name="email" value="{{ old('email') }}" required autocomplete="email" autofocus 
                                placeholder="Email or No. Maktab">
                            <label for="email"><i class="fas fa-envelope-open text-muted mr-2"></i>{{ __('Email Address or No. Maktab') }}</label>
                            @error('email')
                                <span class="invalid-feedback font-weight-bold small mt-1" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-floating edu-input-group mb-4">
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                                name="password" required autocomplete="current-password"
                                placeholder="Password">
                            <label for="password"><i class="fas fa-lock text-muted mr-2"></i>{{ __('Password') }}</label>
                            @error('password')
                                <span class="invalid-feedback font-weight-bold small mt-1" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="d-grid mb-2">
                            <button type="submit" class="btn btn-primary btn-lg font-weight-bold shadow-sm py-3" style="border-radius: 12px; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); border: none; font-size: 0.95rem;">
                                {{ __('Sign In to Portal') }} <i class="fas fa-arrow-right ml-2 small"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <p class="text-center mt-4 text-white font-weight-bold small drop-shadow" style="text-shadow: 0 2px 4px rgba(0,0,0,0.4);">
                Don't have an account yet? <a href="{{ route('register') ?? '#' }}" class="text-decoration-none text-warning font-weight-bold ml-1">Create an account</a>
            </p>
        </div>
    </div>
</div>
@endsection