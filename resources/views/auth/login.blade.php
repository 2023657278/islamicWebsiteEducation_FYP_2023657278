@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center align-items-center" style="min-height: 75vh;">
        <div class="col-lg-4 col-md-6 col-sm-10">
            
            <div class="rainbow-card-wrapper">
                <div class="card p-4">
                    
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <img src="{{ asset('image/logoMRSM.png') }}" alt="MRSM Logo" width="85" class="img-fluid">
                        </div>
                        <h3 class="fw-extrabold text-dark mb-1" style="font-weight: 800; letter-spacing: -0.5px;">Selamat Datang! 👋</h3>
                        <p class="text-muted small">Sign in to access your quizzes, analytics, and class timetables.</p>
                        <hr style="border-color: #e2e8f0; opacity: 0.8;">
                    </div>

                    <div class="card-body p-0">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="form-floating mb-3 position-relative">
                                <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" 
                                    name="email" value="{{ old('email') }}" required autocomplete="email" autofocus 
                                    placeholder="Email or No. Maktab"
                                    style="border-radius: 12px; padding: 14px 16px; border: 1px solid #cbd5e1; background-color: #f8fafc;">
                                <label for="email" style="color: #64748b; padding-left: 16px;">
                                    <i class="fas fa-envelope-open mr-2 text-primary" style="opacity: 0.7;"></i> Email Address or No. Maktab
                                </label>
                                @error('email')
                                    <span class="invalid-feedback font-weight-bold small mt-1" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-floating mb-4 position-relative">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                                    name="password" required autocomplete="current-password"
                                    placeholder="Password"
                                    style="border-radius: 12px; padding: 14px 16px; border: 1px solid #cbd5e1; background-color: #f8fafc;">
                                <label for="password" style="color: #64748b; padding-left: 16px;">
                                    <i class="fas fa-lock mr-2 text-primary" style="opacity: 0.7;"></i> Password
                                </label>
                                @error('password')
                                    <span class="invalid-feedback font-weight-bold small mt-1" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="d-grid mb-2">
                                <button type="submit" class="btn btn-primary btn-lg font-weight-bold shadow-sm py-3" 
                                    style="border-radius: 12px; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); border: none; font-size: 0.95rem;">
                                    Enter Platform Portal <i class="fas fa-arrow-right ml-2 small"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div> <p class="text-center mt-4 text-white font-weight-bold">
                Don't have an account yet? <a href="{{ route('register') ?? '#' }}" class="text-decoration-none text-warning">Create one here</a>
            </p>
        </div>
    </div>
</div>
@endsection