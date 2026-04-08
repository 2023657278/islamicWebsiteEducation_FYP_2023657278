@extends('users.students')

@section('content')
<div class="container text-center py-4" style="max-width: 800px;">
    
    {{-- TOP NAV --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('student.flashcards.index') }}" class="btn btn-light rounded-pill px-4 fw-bold">
            <i class="fas fa-times me-2"></i> Exit
        </a>
        <div class="text-center">
            <h5 class="fw-bold mb-0">{{ $subject->subject_name }}</h5>
            <small class="text-muted">Preview Mode</small>
        </div>
        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
            {{ $current }} / {{ $total }}
        </span>
    </div>

    {{-- PROGRESS BAR --}}
    <div class="progress mb-5" style="height: 6px;">
        <div class="progress-bar bg-primary rounded-pill" role="progressbar" style="width: {{ $progress }}%"></div>
    </div>

    {{-- CARD --}}
    @if($cards->count() > 0)
        @php $card = $cards->first(); @endphp
        <div class="scene mx-auto" style="height: 400px; width: 100%;">
            <div class="flashcard" id="card" onclick="this.classList.toggle('is-flipped')">
                <div class="card-face card-front bg-white rounded-5 shadow-sm border d-flex flex-column align-items-center justify-content-center p-5">
                    <span class="text-uppercase text-muted fw-bold small mb-4">Question</span>
                    <h2 class="fw-bold text-dark lh-base">{{ $card->question }}</h2>
                    <div class="mt-auto text-muted small"><i class="fas fa-hand-pointer me-2"></i> Tap to flip</div>
                </div>
                <div class="card-face card-back bg-light rounded-5 shadow-sm border d-flex flex-column align-items-center justify-content-center p-5">
                    <span class="text-uppercase text-muted fw-bold small mb-4">Answer</span>
                    <h2 class="fw-bold text-primary lh-base">{{ $card->answer }}</h2>
                </div>
            </div>
        </div>

        {{-- NAVIGATION --}}
        <div class="d-flex justify-content-between align-items-center mt-5 px-5">
            @if($cards->onFirstPage())
                <button class="btn btn-light rounded-pill px-4 py-2" disabled>Prev</button>
            @else
                <a href="{{ $cards->previousPageUrl() }}" class="btn btn-outline-primary rounded-pill px-4 py-2">Prev</a>
            @endif

            <span class="text-muted small">Tap card to see answer</span>

            @if($cards->hasMorePages())
                <a href="{{ $cards->nextPageUrl() }}" class="btn btn-primary rounded-pill px-4 py-2">Next</a>
            @else
                <button class="btn btn-light rounded-pill px-4 py-2" disabled>End</button>
            @endif
        </div>
    @else
        <div class="alert alert-info">No cards found for this subject.</div>
    @endif
</div>

<style>
    .scene { perspective: 1000px; }
    .flashcard { width: 100%; height: 100%; transition: transform 0.6s; transform-style: preserve-3d; cursor: pointer; position: relative; }
    .flashcard.is-flipped { transform: rotateY(180deg); }
    .card-face { position: absolute; width: 100%; height: 100%; backface-visibility: hidden; -webkit-backface-visibility: hidden; }
    .card-back { transform: rotateY(180deg); }
</style>
@endsection