@extends('users.students')

@section('content')
@php $userPts = Auth::user()->pvp_points; @endphp

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="fas fa-radar me-2 text-primary"></i> Active Missions</h2>
        <a href="{{ route('student.quizzes.select_mode', $subject->id) }}" class="btn btn-light rounded-pill">Back</a>
    </div>

    <div class="row g-3">
        @forelse($rooms as $room)
        @php
            $diff = strtolower($room->quiz->difficulty);
            // 🔒 Define lock logic
            $isLocked = ($diff === 'medium' && $userPts < 100) || ($diff === 'hard' && $userPts < 300);
            $color = $diff == 'easy' ? 'success' : ($diff == 'medium' ? 'warning' : 'danger');
        @endphp

        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 mb-2 {{ $isLocked ? 'opacity-75' : '' }}">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h5 class="fw-bold mb-0">{{ $room->quiz->title }}</h5>
                            <span class="badge bg-{{ $color }} text-uppercase small" style="font-size: 0.6rem;">{{ $diff }}</span>
                        </div>
                        <small class="text-muted">Host: {{ $room->host->name ?? 'Warrior Leader' }}</small>
                    </div>
                    
                    <div class="text-end">
                        <span class="badge bg-light text-dark mb-2 d-block border">
                            {{ $room->participants_count }}/20 Players
                        </span>

                        @if($isLocked)
                            {{-- 🔒 Locked State --}}
                            <button disabled class="btn btn-secondary btn-sm px-4 rounded-pill">
                                <i class="fas fa-lock me-1"></i> {{ $diff === 'medium' ? '100' : '300' }} PTS
                            </button>
                        @else
                            {{-- ✅ Joinable State --}}
                            <form action="{{ route('student.quizzes.join') }}" method="POST">
                                @csrf
                                <input type="hidden" name="room_code" value="{{ $room->room_code }}">
                                <button type="submit" class="btn btn-primary btn-sm px-4 rounded-pill">JOIN</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="text-muted mb-3"><i class="fas fa-ghost fa-3x"></i></div>
            <h5 class="text-muted">No public missions found. Start one yourself!</h5>
        </div>
        @endforelse
    </div>
</div>
@endsection