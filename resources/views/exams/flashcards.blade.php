@extends('admin.adminhome')
@section('content')
<style>
    .flashcard {
        cursor: pointer; height: 300px; position: relative;
        transform-style: preserve-3d; transition: transform 0.6s;
    }
    .flashcard.flipped { transform: rotateY(180deg); }
    .card-face {
        position: absolute; width: 100%; height: 100%;
        backface-visibility: hidden;
        display: flex; flex-direction: column; justify-content: center; align-items: center;
        border-radius: 15px; padding: 20px; text-align: center;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .front { background: white; border: 2px solid #0d6efd; color: #333; }
    .back { background: #0d6efd; color: white; transform: rotateY(180deg); }
</style>

<div class="container py-4 text-center">
    <h4 class="mb-4">{{ $paper->title }} - Flashcards</h4>

    <div id="cardCarousel" class="carousel slide" data-bs-interval="false">
        <div class="carousel-inner p-4">
            @foreach($paper->questions as $index => $q)
            <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="flashcard" onclick="this.classList.toggle('flipped')">
                            <div class="card-face front">
                                <h5 class="fw-bold">Q{{ $index + 1 }}</h5>
                                <p class="lead">{{ $q->question_text }}</p>
                                <small class="text-muted mt-3">(Click to Flip)</small>
                            </div>
                            <div class="card-face back">
                                <h5 class="fw-bold">Correct Answer: {{ strtoupper(substr($q->correct_option, -1)) }}</h5>
                                <p>{{ $q->explanation }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#cardCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon bg-dark rounded-circle"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#cardCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon bg-dark rounded-circle"></span>
        </button>
    </div>
</div>
@endsection