@extends('users.students')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4">
                
                {{-- Icon Header --}}
                <div class="mb-3">
                    <div class="p-3 rounded-circle bg-primary bg-opacity-10 d-inline-block">
                        <i class="fab fa-telegram-plane text-primary fa-4x"></i>
                    </div>
                </div>

                <h3 class="fw-bold">Telegram Notifications</h3>
                <p class="text-muted">Get quiz results and reminders directly on your phone.</p>

                <hr class="my-4">

                {{-- ✅ ALERTS: Success & Error Messages --}}
                @if(session('success'))
                    <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success fw-bold mb-4">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger fw-bold mb-4">
                        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    </div>
                @endif

                {{-- ✅ MAIN CONTENT LOGIC --}}
                @if($user->telegram_chat_id)
                    
                    {{-- STATE: CONNECTED --}}
                    <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success fw-bold">
                        <i class="fas fa-check-circle me-2"></i> Account Connected
                    </div>
                    <p class="text-muted small mb-4">Chat ID: {{ $user->telegram_chat_id }}</p>
                    
                    <form action="{{ route('telegram.unlink') }}" method="POST">
                        @csrf
                        <button class="btn btn-outline-danger rounded-pill px-4">
                            <i class="fas fa-unlink me-2"></i> Disconnect
                        </button>
                    </form>

                @else
                    
                    {{-- STATE: DISCONNECTED --}}
                    <div class="mb-4">
                        <p class="mb-1 fw-bold text-dark">Step 1: Open the Bot</p>
                        
                        {{-- 
                            ✅ FIXED: I have HARDCODED your correct bot name here.
                            This bypasses any config errors.
                        --}}
                        <a href="https://t.me/PAI_MRSM_bot" 
                           target="_blank" 
                           class="btn btn-primary rounded-pill btn-sm px-4 mb-3 shadow-sm">
                            Open Telegram Bot <i class="fas fa-external-link-alt ms-1"></i>
                        </a>

                        <p class="mb-1 fw-bold text-dark mt-2">Step 2: Send this Code</p>
                        <div class="bg-light p-3 rounded-3 border fw-bold fs-4 letter-spacing-2 text-dark mb-2 font-monospace">
                            {{ $code }}
                        </div>
                        <small class="text-muted d-block">Type this code into the bot chat and send it.</small>
                    </div>

                    <form action="{{ route('telegram.verify') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-dark w-100 rounded-pill py-2 fw-bold shadow-sm">
                            I Have Sent The Code
                        </button>
                    </form>

                @endif
            </div>
        </div>
    </div>
</div>
@endsection