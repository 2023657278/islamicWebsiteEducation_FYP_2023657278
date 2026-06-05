@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-dark text-white rounded-top-4">{{ __('Authenticating Account Dashboard Session...') }}</div>

                <div class="card-body text-center py-5">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="spinner-border text-secondary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    
                    <p class="text-muted mb-0">Synchronizing authorization clearance footprint... Redirecting immediately.</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 🟢 FIXED ROLE FOOTPRINT ROUTER GATEWAY --}}
<script>
    window.onload = function() {
        @auth
            const role = "{{ Auth::user()->role }}";
            
            if (role === 'admin') {
                window.location.href = "{{ route('adminreal.dashboard') }}";
            } else if (role === 'teacher') {
                window.location.href = "{{ route('admin.dashboard') }}";
            } else if (role === 'student') {
                window.location.href = "{{ route('student.homepage') }}";
            } else {
                window.location.href = "/";
            }
        @else
            window.location.href = "/login";
        @endauth
    };
</script>
@endsection