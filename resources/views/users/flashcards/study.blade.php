@extends('users.students')

@section('content')
<div class="container text-center py-4" style="max-width: 800px;">
    
    {{-- TOP BAR --}}
    <div class="d-flex justify-content-between align-items-center mb-5">
        <a href="{{ route('student.flashcards.index') }}" class="btn btn-light rounded-pill px-4 fw-bold">
            <i class="fas fa-arrow-left me-2"></i> Exit
        </a>
        <span class="badge bg-primary bg-opacity-10 text-primary fs-6 px-3 py-2 rounded-pill">
            {{ $remaining }} Cards Remaining
        </span>
    </div>

    {{-- FLIP CARD CONTAINER --}}
    <div class="scene mx-auto" style="height: 400px; width: 100%;">
        <div class="flashcard" id="card" onclick="flipCard()">
            
            {{-- FRONT (Question) --}}
            <div class="card-face card-front bg-white rounded-5 shadow-sm border d-flex flex-column align-items-center justify-content-center p-5">
                <span class="text-uppercase text-muted fw-bold small mb-4 letter-spacing-2">Question</span>
                <h2 class="fw-bold text-dark lh-base">{{ $card->question }}</h2>
                <div class="mt-auto text-muted small">
                    <i class="fas fa-hand-pointer me-2 animate-bounce"></i> Tap card to flip
                </div>
            </div>

            {{-- BACK (Answer) --}}
            <div class="card-face card-back bg-light rounded-5 shadow-sm border d-flex flex-column align-items-center justify-content-center p-5">
                <span class="text-uppercase text-muted fw-bold small mb-4 letter-spacing-2">Answer</span>
                <h2 class="fw-bold text-success lh-base">{{ $card->answer }}</h2>
                @if($card->topic)
                    <span class="badge bg-white text-dark border mt-4 px-3 py-2">{{ $card->topic }}</span>
                @endif
            </div>

        </div>
    </div>

    {{-- RATING CONTROLS (Hidden Initially) --}}
    <div id="controls" class="mt-5" style="opacity: 0; pointer-events: none; transition: opacity 0.3s ease;">
        <p class="text-muted mb-3 small fw-bold text-uppercase">How well did you know this?</p>
        
        <form action="{{ route('student.flashcards.update') }}" method="POST" class="row g-2 justify-content-center">
            @csrf
            <input type="hidden" name="card_id" value="{{ $card->id }}">
            <input type="hidden" name="subject_id" value="{{ $subjectId }}">

            <div class="col-6 col-md-3">
                <button type="submit" name="rating" value="1" class="btn btn-danger w-100 py-3 rounded-4 shadow-sm hover-scale">
                    <div class="fw-bold">Again</div>
                    <small class="opacity-75">< 1 min</small>
                </button>
            </div>
            <div class="col-6 col-md-3">
                <button type="submit" name="rating" value="2" class="btn btn-warning text-white w-100 py-3 rounded-4 shadow-sm hover-scale">
                    <div class="fw-bold">Hard</div>
                    <small class="opacity-75">2 days</small>
                </button>
            </div>
            <div class="col-6 col-md-3">
                <button type="submit" name="rating" value="3" class="btn btn-primary w-100 py-3 rounded-4 shadow-sm hover-scale">
                    <div class="fw-bold">Good</div>
                    <small class="opacity-75">4 days</small>
                </button>
            </div>
            <div class="col-6 col-md-3">
                <button type="submit" name="rating" value="4" class="btn btn-success w-100 py-3 rounded-4 shadow-sm hover-scale">
                    <div class="fw-bold">Easy</div>
                    <small class="opacity-75">7 days</small>
                </button>
            </div>
        </form>
    </div>

</div>

<style>
    /* 3D FLIP CSS */
    .scene { perspective: 1000px; }
    .flashcard { width: 100%; height: 100%; transition: transform 0.6s; transform-style: preserve-3d; cursor: pointer; position: relative; }
    .flashcard.is-flipped { transform: rotateY(180deg); }
    .card-face { position: absolute; width: 100%; height: 100%; backface-visibility: hidden; -webkit-backface-visibility: hidden; }
    .card-back { transform: rotateY(180deg); }
    
    /* ANIMATIONS */
    .hover-scale:hover { transform: scale(1.05); transition: transform 0.2s; }
    @keyframes bounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-5px); } }
    .animate-bounce { animation: bounce 2s infinite; }
</style>

<script>
    let isFlipped = false;
    function flipCard() {
        if (isFlipped) return;
        document.getElementById('card').classList.add('is-flipped');
        
        // Reveal controls smoothly
        setTimeout(() => {
            const controls = document.getElementById('controls');
            controls.style.opacity = '1';
            controls.style.pointerEvents = 'auto';
        }, 200);
        
        isFlipped = true;
    }
</script>
@endsection