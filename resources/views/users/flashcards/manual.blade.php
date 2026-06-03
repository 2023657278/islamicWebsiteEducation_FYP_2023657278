@extends('users.students')

@section('content')
<div class="container text-center py-4" style="max-width: 800px;">
    
    {{-- TOP NAVIGATION --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('student.flashcards.index') }}" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm">
            <i class="fas fa-times me-2"></i> Exit
        </a>
        <div class="text-center">
            <h4 class="fw-bold text-dark mb-0">{{ $subject->subject_name }}</h4>
            <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-1 rounded-pill mt-1">
                <i class="fas fa-eye me-1"></i> Preview Mode
            </span>
        </div>
        <span class="badge bg-primary bg-opacity-10 text-primary fs-6 px-3 py-2 rounded-pill">
            {{ $current }} / {{ $total }} Cards
        </span>
    </div>

    {{-- DYNAMIC PROGRESS BAR --}}
    <div class="progress mb-5" style="height: 8px; background-color: #e9ecef; border-radius: 10px;">
        <div class="progress-bar bg-primary progress-bar-striped" 
             role="progressbar" 
             style="width: {{ $progress }}%; border-radius: 10px; transition: width 0.4s ease;"></div>
    </div>

    {{-- CARD DISPLAY SYSTEM --}}
    {{-- We check if $cards is a valid collection/paginator to avoid the errors you encountered --}}
    @if(isset($cards) && count($cards) > 0)
        @php $card = $cards->first(); @endphp
        <div class="scene mx-auto" style="height: 420px; width: 100%;">
            <div class="flashcard shadow-lg" id="card" onclick="this.classList.toggle('is-flipped')">
                
                {{-- FRONT (Question) --}}
                <div class="card-face card-front bg-white rounded-5 border d-flex flex-column align-items-center justify-content-center p-5">
                    <span class="text-uppercase text-muted fw-bold small mb-4 tracking-widest" style="letter-spacing: 2px;">Question</span>
                    <h2 class="fw-bold text-dark lh-base px-3" style="font-size: 2.2rem;">{{ $card->question }}</h2>
                    <div class="mt-auto text-muted small">
                        <i class="fas fa-hand-pointer me-2 animate-bounce"></i> Tap card or press <b>Space</b> to reveal answer
                    </div>
                </div>

                {{-- BACK (Answer) --}}
                <div class="card-face card-back bg-light rounded-5 border d-flex flex-column align-items-center justify-content-center p-5">
                    <span class="text-uppercase text-muted fw-bold small mb-4 tracking-widest" style="letter-spacing: 2px;">Answer</span>
                    <h2 class="fw-bold text-primary lh-base px-3" style="font-size: 2.2rem;">{{ $card->answer }}</h2>
                    @if($card->topic)
                        <span class="badge bg-white text-dark border mt-4 px-3 py-2 shadow-sm">{{ $card->topic }}</span>
                    @endif
                </div>

            </div>
        </div>

        {{-- PAGINATION NAVIGATION CONTROLS --}}
        <div class="d-flex justify-content-between align-items-center mt-5 px-md-5">
            {{-- Navigation logic using Laravel Paginator methods --}}
            @if($cards->onFirstPage())
                <button class="btn btn-light rounded-pill px-5 py-2.5 fw-bold text-muted shadow-sm border" disabled>
                    <i class="fas fa-chevron-left me-2"></i> Prev
                </button>
            @else
                <a href="{{ $cards->previousPageUrl() }}" class="btn btn-outline-primary rounded-pill px-5 py-2.5 fw-bold shadow-sm">
                    <i class="fas fa-chevron-left me-2"></i> Prev
                </a>
            @endif

            <span class="text-muted small d-none d-md-inline-block">
                Use <b>Left</b> and <b>Right</b> arrow keys to browse deck
            </span>

            @if($cards->hasMorePages())
                <a href="{{ $cards->nextPageUrl() }}" class="btn btn-primary rounded-pill px-5 py-2.5 fw-bold shadow-sm">
                    Next <i class="fas fa-chevron-right ms-2"></i>
                </a>
            @else
                <button class="btn btn-success rounded-pill px-5 py-2.5 fw-bold shadow-sm border-0" disabled>
                    End <i class="fas fa-check-double ms-2"></i>
                </button>
            @endif
        </div>
    @else
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
            <div class="text-muted mb-3">
                <i class="fas fa-folder-open fa-3x text-opacity-50"></i>
            </div>
            <h4 class="fw-bold text-dark mb-1">No Cards Found</h4>
            <p class="text-muted mb-0">There are no study cards loaded under this subject category yet.</p>
        </div>
    @endif
</div>

<style>
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
        box-shadow: 0 20px 40px rgba(13, 110, 253, 0.12) !important;
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
    
    @keyframes bounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-6px); } }
    .animate-bounce { animation: bounce 2.5s infinite; }
</style>

<script>
    document.addEventListener('keydown', function(event) {
        const card = document.getElementById('card');
        
        if ((event.code === 'Space' || event.code === 'Enter') && card) {
            event.preventDefault();
            card.classList.toggle('is-flipped');
        }
        
        if (event.key === 'ArrowLeft') {
            const prevLink = document.querySelector('a[href*="page="]');
            if (prevLink) prevLink.click();
        }
        if (event.key === 'ArrowRight') {
            const links = document.querySelectorAll('a[href*="page="]');
            const nextLink = links[links.length - 1];
            if (nextLink) nextLink.click();
        }
    });
</script>
@endsection