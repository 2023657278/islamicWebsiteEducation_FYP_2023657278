@extends('users.students')

@section('content')
<style>
    /* Question Navigator Styles */
    .q-nav-btn { width: 35px; height: 35px; border-radius: 50%; border: 1px solid #ddd; background: white; font-weight: bold; color: #555; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; }
    .q-nav-btn:hover { background: #f0f0f0; }
    .q-nav-btn.active { background: #be123c; color: white; border-color: #be123c; }
    .q-nav-btn.answered { border-color: #0f766e; color: #0f766e; background: #f0fdfa; }

    /* Option Styles */
    .option-card { border: 2px solid #eee; border-radius: 12px; padding: 15px 20px; cursor: pointer; transition: all 0.2s; background: white; margin-bottom: 12px; display: block; }
    .option-card:hover { background: #f9f9f9; border-color: #ccc; }
    
    /* Selection Styling */
    .option-input:checked + .option-content { font-weight: bold; color: #be123c; }
    .option-card.selected { border-color: #be123c; background: #fff5f7; }
</style>

<div class="container" style="max-width: 900px;">
    <form action="{{ route('student.quizzes.submit', $quiz->id) }}" method="POST" id="quizForm">
        @csrf

        {{-- 1. TOP HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="fw-bold mb-0">{{ $quiz->title }}</h4>
                <small class="text-muted">Answer all questions before time runs out.</small>
            </div>
            <div class="text-end">
                <span class="badge bg-danger fs-6 px-3 py-2">
                    <i class="fas fa-clock me-2"></i> <span id="timer">00:00</span>
                </span>
            </div>
        </div>

        {{-- 2. QUESTION NAVIGATOR (Jump to Question) --}}
        <div class="card border-0 shadow-sm rounded-4 p-3 mb-4">
            <div class="d-flex flex-wrap gap-2 justify-content-center">
                @foreach($quiz->questions as $index => $q)
                <div class="q-nav-btn {{ $index === 0 ? 'active' : '' }}" 
                     id="nav-btn-{{ $index + 1 }}" 
                     onclick="goToQuestion({{ $index + 1 }})">
                    {{ $index + 1 }}
                </div>
                @endforeach
            </div>
        </div>

        {{-- 3. QUESTIONS CONTAINER --}}
        <div class="questions-container">
            @foreach($quiz->questions as $index => $q)
            <div class="question-step" id="step-{{ $index + 1 }}" style="{{ $index > 0 ? 'display:none;' : '' }}">
                
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom p-4">
                        <span class="badge bg-light text-dark border mb-2">Question {{ $index + 1 }}</span>
                        <h5 class="fw-bold text-dark mb-0">{{ $q->question_text }}</h5>
                        <small class="text-muted">Type: {{ ucfirst($q->question_type) }}</small>
                    </div>

                    <div class="card-body p-4">
                        {{-- A. SINGLE CHOICE (Radio) --}}
                        @if($q->question_type === 'single')
                            <div class="d-grid gap-2">
                                @foreach($q->options as $opt)
                                <label class="option-card">
                                    <input type="radio" name="q_{{ $q->id }}" value="{{ $opt->id }}" class="option-input d-none" onchange="markAnswered({{ $index + 1 }})">
                                    <span class="option-content d-flex align-items-center">
                                        <span class="fw-bold text-muted border rounded-circle px-2 py-1 me-3 small">
                                            {{ chr(65 + $loop->index) }}
                                        </span>
                                        {{ $opt->option_text }}
                                    </span>
                                </label>
                                @endforeach
                            </div>

                        {{-- B. MULTIPLE CHOICE (Checkbox) --}}
                        @elseif($q->question_type === 'multiple')
                            <div class="d-grid gap-2">
                                @foreach($q->options as $opt)
                                <label class="option-card">
                                    <input type="checkbox" name="q_{{ $q->id }}[]" value="{{ $opt->id }}" class="option-input d-none" onchange="markAnswered({{ $index + 1 }})">
                                    <span class="option-content d-flex align-items-center">
                                        <span class="fw-bold text-muted border rounded px-2 py-1 me-3 small">
                                            <i class="fas fa-check"></i>
                                        </span>
                                        {{ $opt->option_text }}
                                    </span>
                                </label>
                                @endforeach
                            </div>
                            <small class="text-muted mt-2 d-block"><i class="fas fa-info-circle"></i> Select all correct answers.</small>

                        {{-- C. FILL IN THE BLANK (Text) --}}
                        @elseif($q->question_type === 'text')
                            <div class="form-group">
                                <label class="fw-bold text-muted small mb-2">YOUR ANSWER:</label>
                                <textarea name="q_{{ $q->id }}" class="form-control bg-light border-0 p-3 rounded-3" rows="3" placeholder="Type your answer here..." oninput="markAnswered({{ $index + 1 }})"></textarea>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- 4. NAVIGATION BUTTONS --}}
        <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-outline-secondary rounded-pill px-4" id="btnPrev" onclick="prevQuestion()" style="display:none;">
                <i class="fas fa-arrow-left me-2"></i> Previous
            </button>
            
            <button type="button" class="btn btn-dark rounded-pill px-5 ms-auto" id="btnNext" onclick="nextQuestion()">
                Next <i class="fas fa-arrow-right ms-2"></i>
            </button>
            
            <button type="submit" class="btn btn-danger rounded-pill px-5 ms-auto" id="btnSubmit" style="display:none;" onclick="return confirm('Are you sure you want to submit?')">
                Submit Quiz <i class="fas fa-check ms-2"></i>
            </button>
        </div>

    </form>
</div>

<script>
    let currentStep = 1;
    let totalSteps = {{ $quiz->questions->count() }};
    
    // 1. TIMER LOGIC
    let duration = {{ $quiz->duration_minutes * 60 }};
    let timerInterval = setInterval(() => {
        if(duration > 0) {
            duration--;
            let min = Math.floor(duration / 60);
            let sec = duration % 60;
            document.getElementById('timer').innerText = min + ":" + (sec < 10 ? '0' : '') + sec;
        } else {
            clearInterval(timerInterval);
            alert("Time is up! Submitting quiz...");
            document.getElementById('quizForm').submit();
        }
    }, 1000);

    // 2. NAVIGATION LOGIC
    function showStep(step) {
        // Hide all steps
        document.querySelectorAll('.question-step').forEach(el => el.style.display = 'none');
        // Show target step
        document.getElementById('step-' + step).style.display = 'block';
        
        // Update Buttons
        document.getElementById('btnPrev').style.display = step === 1 ? 'none' : 'block';
        
        if (step === totalSteps) {
            document.getElementById('btnNext').style.display = 'none';
            document.getElementById('btnSubmit').style.display = 'block';
        } else {
            document.getElementById('btnNext').style.display = 'block';
            document.getElementById('btnSubmit').style.display = 'none';
        }

        // Update Nav Bubbles
        document.querySelectorAll('.q-nav-btn').forEach(btn => btn.classList.remove('active'));
        document.getElementById('nav-btn-' + step).classList.add('active');

        currentStep = step;
    }

    function nextQuestion() {
        if(currentStep < totalSteps) showStep(currentStep + 1);
    }

    function prevQuestion() {
        if(currentStep > 1) showStep(currentStep - 1);
    }

    function goToQuestion(step) {
        showStep(step);
    }

    // 3. STYLING LOGIC (Highlight Selected)
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('option-input')) {
            let parent = e.target.closest('.d-grid'); // Container of options
            let type = e.target.type;

            if (type === 'radio') {
                // Remove selected from all labels in this group
                parent.querySelectorAll('.option-card').forEach(lbl => lbl.classList.remove('selected'));
                // Add to checked
                e.target.closest('.option-card').classList.add('selected');
            } else if (type === 'checkbox') {
                // Toggle selected class
                e.target.closest('.option-card').classList.toggle('selected', e.target.checked);
            }
        }
    });

    // 4. MARK AS ANSWERED (Green Bubble)
    function markAnswered(step) {
        document.getElementById('nav-btn-' + step).classList.add('answered');
    }
</script>
@endsection