@extends('users.students')

@section('content')
<div class="container text-center py-4" style="max-width: 800px;">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('student.flashcards.index') }}" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm">
            <i class="fas fa-times me-2"></i> Exit
        </a>
        <span class="badge bg-primary bg-opacity-10 text-primary fs-6 px-3 py-2 rounded-pill">
            {{ $remaining }} Remaining
        </span>
    </div>

    <div class="scene mx-auto" style="height: 420px; width: 100%;">
        <div class="flashcard shadow-lg" id="card" onclick="flipCard()">
            <div class="card-face card-front bg-white rounded-5 border d-flex flex-column align-items-center justify-content-center p-5">
                <span class="text-uppercase text-muted fw-bold small mb-4 tracking-widest" style="letter-spacing: 2px;">Question</span>
                <h2 class="fw-bold text-dark lh-base px-3" style="font-size: 2.2rem;">{{ $card->question }}</h2>
                <div class="mt-auto text-muted small">
                    <i class="fas fa-hand-pointer me-2 animate-bounce"></i> Tap or press <b>Space</b> to flip
                </div>
            </div>
            <div class="card-face card-back bg-light rounded-5 border d-flex flex-column align-items-center justify-content-center p-5">
                <span class="text-uppercase text-muted fw-bold small mb-4 tracking-widest" style="letter-spacing: 2px;">Answer</span>
                <h2 class="fw-bold text-success lh-base px-3" style="font-size: 2.2rem;">{{ $card->answer }}</h2>
            </div>
        </div>
    </div>

    <div id="controls" class="mt-5" style="opacity: 0; pointer-events: none; transition: all 0.4s ease; transform: translateY(20px);">
        <p class="text-muted mb-3 small fw-bold text-uppercase">How easy was this? <span class="ms-2 badge bg-light text-dark">Keys 1-4</span></p>
        <form action="{{ route('student.flashcards.update') }}" method="POST" id="srsForm" class="row g-3 justify-content-center">
            @csrf
            <input type="hidden" name="card_id" value="{{ $card->id }}">
            <input type="hidden" name="subject_id" value="{{ $subjectId }}">

            <div class="col-6 col-md-3">
                <button type="submit" name="rating" value="1" class="btn btn-danger w-100 py-3 rounded-4 shadow-sm border-0">
                    <div class="fw-bold fs-5">Again</div>
                    <small class="opacity-75">Next: <b>1 min</b></small>
                </button>
            </div>
            <div class="col-6 col-md-3">
                <button type="submit" name="rating" value="2" class="btn btn-warning text-white w-100 py-3 rounded-4 shadow-sm border-0">
                    <div class="fw-bold fs-5">Hard</div>
                    <small class="opacity-75">Next: <b>2d</b></small>
                </button>
            </div>
            <div class="col-6 col-md-3">
                <button type="submit" name="rating" value="3" class="btn btn-primary w-100 py-3 rounded-4 shadow-sm border-0">
                    <div class="fw-bold fs-5">Good</div>
                    <small class="opacity-75">Next: <b>4d</b></small>
                </button>
            </div>
            <div class="col-6 col-md-3">
                <button type="submit" name="rating" value="4" class="btn btn-success w-100 py-3 rounded-4 shadow-sm border-0">
                    <div class="fw-bold fs-5">Easy</div>
                    <small class="opacity-75">Next: <b>7d</b></small>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .scene { perspective: 1200px; }
    .flashcard { width: 100%; height: 100%; transition: transform 0.6s ease, box-shadow 0.3s ease; transform-style: preserve-3d; cursor: pointer; position: relative; border-radius: 2rem; }
    .flashcard.is-flipped { transform: rotateY(180deg); box-shadow: 0 20px 40px rgba(40, 167, 69, 0.15) !important; }
    .card-face { position: absolute; width: 100%; height: 100%; backface-visibility: hidden; -webkit-backface-visibility: hidden; border: 2px solid rgba(0,0,0,0.03); }
    .card-back { transform: rotateY(180deg); }
    @keyframes bounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-8px); } }
    .animate-bounce { animation: bounce 2.5s infinite; }
</style>

<script>
    let isFlipped = false;
    function flipCard() {
        if (isFlipped) return;
        document.getElementById('card').classList.add('is-flipped');
        setTimeout(() => {
            const controls = document.getElementById('controls');
            controls.style.opacity = '1';
            controls.style.pointerEvents = 'auto';
            controls.style.transform = 'translateY(0)';
        }, 150);
        isFlipped = true;
    }
    document.addEventListener('keydown', function(event) {
        if ((event.code === 'Space' || event.code === 'Enter') && !isFlipped) {
            event.preventDefault();
            flipCard();
        } 
        if (isFlipped) {
            if (event.key === '1') document.querySelector('button[value="1"]').click();
            if (event.key === '2') document.querySelector('button[value="2"]').click();
            if (event.key === '3') document.querySelector('button[value="3"]').click();
            if (event.key === '4') document.querySelector('button[value="4"]').click();
        }
    });
</script>
@endsection