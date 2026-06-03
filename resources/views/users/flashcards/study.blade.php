@extends('users.students')

@section('content')
<div class="container text-center py-4" style="max-width: 800px;">
    
    {{-- TOP NAVIGATION & PROGRESS --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('student.flashcards.index') }}" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm">
            <i class="fas fa-times me-2"></i> Exit
        </a>
        <div class="flex-grow-1 mx-4">
            {{-- Satisfying Progress Bar --}}
            <div class="progress" style="height: 10px; background-color: #e9ecef; border-radius: 10px;">
                <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" 
                     role="progressbar" 
                     style="width: 70%; border-radius: 10px;"></div>
            </div>
        </div>
        <span class="badge bg-primary bg-opacity-10 text-primary fs-6 px-3 py-2 rounded-pill">
            {{ $remaining }} Remaining
        </span>
    </div>

    {{-- FLIP CARD SCENE --}}
    <div class="scene mx-auto" style="height: 420px; width: 100%;">
        <div class="flashcard shadow-lg" id="card" onclick="flipCard()">
            
            {{-- FRONT (Question) --}}
            <div class="card-face card-front bg-white rounded-5 border d-flex flex-column align-items-center justify-content-center p-5">
                <span class="text-uppercase text-muted fw-bold small mb-4 tracking-widest" style="letter-spacing: 2px;">Question</span>
                <h2 class="fw-bold text-dark lh-base px-3" style="font-size: 2.2rem;">{{ $card->question }}</h2>
                <div class="mt-auto text-muted small">
                    <i class="fas fa-hand-pointer me-2 animate-bounce"></i> Tap or press <b>Space</b> to flip
                </div>
            </div>

            {{-- BACK (Answer) --}}
            <div class="card-face card-back bg-light rounded-5 border d-flex flex-column align-items-center justify-content-center p-5">
                <span class="text-uppercase text-muted fw-bold small mb-4 tracking-widest" style="letter-spacing: 2px;">Answer</span>
                <h2 class="fw-bold text-success lh-base px-3" style="font-size: 2.2rem;">{{ $card->answer }}</h2>
                @if($card->topic)
                    <span class="badge bg-white text-dark border mt-4 px-3 py-2 shadow-sm">{{ $card->topic }}</span>
                @endif
            </div>

        </div>
    </div>

    {{-- RATING CONTROLS --}}
    <div id="controls" class="mt-5" style="opacity: 0; pointer-events: none; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); transform: translateY(20px);">
        <p class="text-muted mb-3 small fw-bold text-uppercase">How easy was this? <span class="ms-2 badge bg-light text-dark">Use keys 1-4</span></p>
        
        <form action="{{ route('student.flashcards.update') }}" method="POST" id="srsForm" class="row g-3 justify-content-center">
            @csrf
            <input type="hidden" name="card_id" value="{{ $card->id }}">
            <input type="hidden" name="subject_id" value="{{ $subjectId }}">

            <div class="col-6 col-md-3">
                <button type="submit" name="rating" value="1" class="btn btn-danger w-100 py-3 rounded-4 shadow-sm hover-scale border-0">
                    <div class="fw-bold fs-5">Again</div>
                    <small class="opacity-75">Next: <b>Now</b></small>
                </button>
            </div>
            <div class="col-6 col-md-3">
                <button type="submit" name="rating" value="2" class="btn btn-warning text-white w-100 py-3 rounded-4 shadow-sm hover-scale border-0">
                    <div class="fw-bold fs-5">Hard</div>
                    <small class="opacity-75">Next: <b>2d</b></small>
                </button>
            </div>
            <div class="col-6 col-md-3">
                <button type="submit" name="rating" value="3" class="btn btn-primary w-100 py-3 rounded-4 shadow-sm hover-scale border-0">
                    <div class="fw-bold fs-5">Good</div>
                    <small class="opacity-75">Next: <b>4d</b></small>
                </button>
            </div>
            <div class="col-6 col-md-3">
                <button type="submit" name="rating" value="4" class="btn btn-success w-100 py-3 rounded-4 shadow-sm hover-scale border-0">
                    <div class="fw-bold fs-5">Easy</div>
                    <small class="opacity-75">Next: <b>7d</b></small>
                </button>
            </div>
        </form>
    </div>

</div>

<style>
    /* Premium 3D Flip System */
    .scene { perspective: 1200px; }
    .flashcard { 
        width: 100%; 
        height: 100%; 
        transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease; 
        transform-style: preserve-3d; 
        cursor: pointer; 
        position: relative; 
        border-radius: 2rem;
    }
    .flashcard.is-flipped { 
        transform: rotateY(180deg); 
        box-shadow: 0 20px 40px rgba(40, 167, 69, 0.15) !important;
    }
    .card-face { 
        position: absolute; 
        width: 100%; 
        height: 100%; 
        backface-visibility: hidden; 
        -webkit-backface-visibility: hidden; 
        border: 2px solid rgba(0,0,0,0.03);
    }
    .card-back { transform: rotateY(180deg); }
    
    /* Dynamic Hover Effects */
    .hover-scale { transition: all 0.2s ease-in-out; }
    .hover-scale:hover { transform: translateY(-5px) scale(1.03); filter: brightness(1.1); }
    
    @keyframes bounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-8px); } }
    .animate-bounce { animation: bounce 2.5s infinite; }
</style>

<script>
    let isFlipped = false;

    function flipCard() {
        if (isFlipped) return;
        
        const card = document.getElementById('card');
        const controls = document.getElementById('controls');
        
        card.classList.add('is-flipped');
        
        // Reveal controls with a slight spring animation
        setTimeout(() => {
            controls.style.opacity = '1';
            controls.style.pointerEvents = 'auto';
            controls.style.transform = 'translateY(0)';
        }, 150);
        
        isFlipped = true;
    }

    // Advanced Flow Control: Keyboard Hotkeys
    document.addEventListener('keydown', function(event) {
        // Space or Enter to Reveal Answer
        if ((event.code === 'Space' || event.code === 'Enter') && !isFlipped) {
            event.preventDefault();
            flipCard();
        } 
        
        // Number Keys (1-4) to Rate Answer
        if (isFlipped) {
            if (event.key === '1') document.querySelector('button[value="1"]').click();
            if (event.key === '2') document.querySelector('button[value="2"]').click();
            if (event.key === '3') document.querySelector('button[value="3"]').click();
            if (event.key === '4') document.querySelector('button[value="4"]').click();
        }
    });
</script>
@endsection