@extends('users.students')

@section('content')
<div class="container-fluid p-0">
    <div class="mb-4">
        <h2 class="fw-bold text-dark"><i class="fas fa-layer-group me-2 text-primary"></i>Flashcards</h2>
        <p class="text-muted">Review your decks daily to keep your streak!</p>
    </div>

    <div class="row g-4">
        @foreach($subjects as $subject)
        @php 
            $theme = $subject->style['color']; 
            $icon  = $subject->style['icon'];
        @endphp
        
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 hover-card">
                <div class="card-body p-4 d-flex flex-column">
                    
                    {{-- HEADER --}}
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="p-3 rounded-circle bg-{{ $theme }} bg-opacity-10 text-{{ $theme }}">
                            <i class="fas {{ $icon }} fa-2x"></i>
                        </div>
                        @if($subject->due_cards > 0)
                            <span class="badge bg-danger rounded-pill px-3 py-2 shadow-sm">
                                {{ $subject->due_cards }} Due
                            </span>
                        @else
                            <span class="badge bg-success rounded-pill px-3 py-2 shadow-sm">
                                <i class="fas fa-check me-1"></i> Done
                            </span>
                        @endif
                    </div>
                    
                    {{-- TITLE --}}
                    <h4 class="fw-bold text-dark mb-1">{{ $subject->subject_name }}</h4>
                    <p class="text-muted small mb-4">Master key concepts for {{ $subject->subject_name }}</p>

                    {{-- ACTIONS --}}
                    <div class="mt-auto d-grid gap-2">
                        @if($subject->due_cards > 0)
                            <a href="{{ route('student.flashcards.study', $subject->id) }}" class="btn btn-primary rounded-pill fw-bold py-2">
                                <i class="fas fa-play me-2"></i> Review {{ $subject->due_cards }} Due
                            </a>
                        @else
                            <button class="btn btn-light text-muted border rounded-pill fw-bold w-100 py-2" disabled>
                                <i class="fas fa-check-circle me-2 text-success"></i> All Caught Up
                            </button>
                        @endif

                        <a href="{{ route('student.flashcards.manual', $subject->id) }}" class="btn btn-outline-secondary rounded-pill fw-bold btn-sm py-2">
                            <i class="fas fa-list me-2"></i> Browse Deck
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<style>
    .hover-card { transition: transform 0.2s, box-shadow 0.2s; }
    .hover-card:hover { transform: translateY(-5px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
</style>
@endsection