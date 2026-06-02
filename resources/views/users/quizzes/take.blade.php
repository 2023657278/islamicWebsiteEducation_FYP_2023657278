<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $quiz->title }} | Solo Mission</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;900&display=swap" rel="stylesheet">

    <style>
        body { 
            /* 🌌 BIG VISIBLE GEOMETRIC BACKGROUND */
            background-color: #0f172a;
            background-image: 
                linear-gradient(rgba(30, 41, 59, 0.5) 2px, transparent 2px),
                linear-gradient(90deg, rgba(30, 41, 59, 0.5) 2px, transparent 2px),
                linear-gradient(rgba(30, 41, 59, 0.2) 1px, transparent 1px),
                linear-gradient(90deg, rgba(30, 41, 59, 0.2) 1px, transparent 1px);
            background-size: 100px 100px, 100px 100px, 20px 20px, 20px 20px;
            background-attachment: fixed;
            
            color: #f8fafc; 
            font-family: 'Plus Jakarta Sans', sans-serif;
            padding-top: 20px;
            min-height: 100vh;
        }

        /* Glassmorphism Effect for Cards to let background show through slightly */
        .card { 
            background: rgba(255, 255, 255, 0.95); 
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px !important;
        }

        /* Question Navigator Styles */
        .q-nav-btn { width: 40px; height: 40px; border-radius: 12px; border: 1px solid #e2e8f0; background: white; font-weight: bold; color: #64748b; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; }
        .q-nav-btn:hover { background: #f1f5f9; transform: translateY(-2px); }
        .q-nav-btn.active { background: #be123c; color: white; border-color: #be123c; box-shadow: 0 4px 12px rgba(190, 18, 60, 0.3); }
        .q-nav-btn.correct { background: #10b981 !important; color: white !important; border-color: #10b981 !important; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); }
        .q-nav-btn.wrong { background: #ef4444 !important; color: white !important; border-color: #ef4444 !important; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3); }

        /* 3D CARD MECHANICS */
        .answer-grid { perspective: 1000px; display: flex; flex-direction: column; gap: 12px; }
        .card-container { height: 75px; cursor: pointer; perspective: 1000px; position: relative; }
        .card-inner { position: relative; width: 100%; height: 100%; transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); transform-style: preserve-3d; }
        .is-selected .card-inner { transform: rotateX(180deg); }
        
        .card-front, .card-back { position: absolute; width: 100%; height: 100%; backface-visibility: hidden; border: 2px solid #f1f5f9; border-radius: 16px; display: flex; align-items: center; padding: 0 20px; font-weight: 600; transition: all 0.3s; }
        .card-front { background: white; z-index: 2; color: #1e293b; }
        .card-back { background: #1e293b; color: white; transform: rotateX(180deg); display: flex; justify-content: space-between; z-index: 1; border-color: #334155; }

        /* Evaluation Color States */
        .reveal-hit .card-front { border-color: #10b981 !important; border-width: 3px; background-color: #ecfdf5; color: #059669; }
        .reveal-missed .card-front { border-color: #ef4444 !important; border-width: 3px; background-color: #fef2f2; color: #dc2626; }
        
        .locked { pointer-events: none !important; opacity: 0.9; }
        .fw-black { font-weight: 900; }
        
        .btn-check-action {
            background: #fbbf24;
            color: #0f172a;
            border: none;
            transition: all 0.3s;
        }
        .btn-check-action:hover {
            background: #f59e0b;
            transform: scale(1.05);
        }
    </style>
</head>
<body>

<div class="container py-4" style="max-width: 950px;">
    <form action="{{ route('student.quizzes.submit', $quiz->id) }}" method="POST" id="quizForm">
        @csrf

        {{-- 1. TOP HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-black mb-0 text-white">{{ $quiz->title }}</h2>
                <span class="badge bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25 px-3">Solo Mission Arena</span>
            </div>
            <div class="text-end d-flex align-items-center gap-3">
                <button type="button" class="btn btn-danger rounded-pill px-4 shadow-lg fw-bold" onclick="triggerSubmitValidation()">
                    <i class="fas fa-flag-checkered me-2"></i> Finish Mission
                </button>
                <span class="badge bg-dark fs-5 px-3 py-2 shadow-lg border border-secondary">
                    <i class="fas fa-clock me-2 text-warning"></i> <span id="timer" class="font-monospace">00:00</span>
                </span>
            </div>
        </div>

        {{-- 2. QUESTION NAVIGATOR --}}
        <div class="card border-0 shadow-lg p-3 mb-4">
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
        <div class="questions-container" style="min-height: 400px;">
            @foreach($quiz->questions as $index => $q)
            <div class="question-step" id="step-{{ $index + 1 }}" style="{{ $index > 0 ? 'display:none;' : '' }}">
                
                <div class="card border-0 shadow-lg overflow-hidden">
                    <div class="card-header bg-white border-bottom p-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-light text-dark border">Task {{ $index + 1 }} / {{ $quiz->questions->count() }}</span>
                            <small class="text-muted fw-bold text-uppercase tracking-wider">{{ str_replace('_', ' ', $q->question_type) }}</small>
                        </div>
                        <h4 class="fw-bold text-dark mb-0">{{ $q->question_text }}</h4>
                    </div>

                    <div class="card-body p-4 bg-light bg-opacity-50">
                        @if(in_array($q->question_type, ['single', 'single_choice', 'multiple', 'multiple_choice']))
                            <div class="answer-grid" id="grid-{{ $index + 1 }}">
                                @foreach($q->options as $optIndex => $opt)
                                <div class="card-container" data-is-correct="{{ $opt->is_correct }}" onclick="handleCardClick(this, '{{ $q->question_type }}', {{ $index + 1 }})">
                                    <div class="card-inner">
                                        <div class="card-front">
                                            <span class="fw-bold text-muted border rounded-circle d-flex align-items-center justify-content-center me-3 small" style="width:28px; height:28px;">{{ chr(65 + $optIndex) }}</span>
                                            <span class="option-text">{{ $opt->option_text }}</span>
                                        </div>
                                        <div class="card-back">
                                            <span class="fw-black italic text-uppercase">Locked In</span>
                                            <i class="fas fa-crosshairs align-self-center fs-5"></i>
                                            <input type="{{ (strpos($q->question_type, 'single') !== false) ? 'radio' : 'checkbox' }}" 
                                                   name="q_{{ $q->id }}{{ (strpos($q->question_type, 'multiple') !== false) ? '[]' : '' }}" 
                                                   value="{{ $opt->id }}" class="d-none card-input">
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @elseif(in_array($q->question_type, ['text', 'fill_in_the_blank']))
                            <div class="card-container w-100" style="height: 120px;" id="text-card-{{ $index + 1 }}">
                                <div class="card-inner">
                                    <div class="card-front p-0 overflow-hidden">
                                        <textarea id="text-input-{{ $q->id }}" class="form-control border-0 text-center fs-4 fw-bold h-100 bg-white" placeholder="Type answer here..." data-correct="{{ $q->correct_answer_text }}"></textarea>
                                        <input type="hidden" name="q_{{ $q->id }}" id="hidden-q-{{ $q->id }}">
                                    </div>
                                    <div class="card-back flex-column justify-content-center text-center">
                                        <h5 class="fw-black text-uppercase mb-0" id="fib-status-{{ $index + 1 }}">Verified</h5>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- 4. ACTION FOOTER --}}
        <div class="d-flex justify-content-between align-items-center mt-4">
            <button type="button" class="btn btn-outline-light rounded-pill px-4 fw-bold" id="btnPrev" onclick="prevQuestion()" style="display:none;">
                <i class="fas fa-arrow-left me-2"></i> Previous
            </button>
            
            <button type="button" class="btn btn-check-action btn-lg rounded-pill px-5 mx-auto fw-black shadow-lg" id="btnCheck" onclick="checkCurrentQuestion()">
                <i class="fas fa-bolt me-2"></i> CHECK STRIKE
            </button>
            
            <button type="button" class="btn btn-dark rounded-pill px-5 shadow-lg fw-bold" id="btnNext" onclick="nextQuestion()">
                Next <i class="fas fa-arrow-right ms-2"></i>
            </button>
        </div>
    </form>
</div>

{{-- MODAL --}}
<div class="modal fade" id="submissionModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg text-dark">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-shield-alt text-warning me-2"></i> Finalize Assessment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="p-3 bg-light rounded-4 mb-0 border text-center">
                    <h1 id="unansweredCount" class="fw-black text-danger mb-0">0</h1>
                    <small class="text-muted text-uppercase fw-bold">Unchecked Question Blocks</small>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0 d-flex gap-2">
                <button type="button" class="btn btn-light rounded-pill px-4 border flex-grow-1" data-bs-dismiss="modal">Go Back</button>
                <button type="button" class="btn btn-danger rounded-pill px-4 flex-grow-1 fw-bold shadow-sm" onclick="forceFormSubmit()">Submit Now</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let currentStep = 1;
    let totalSteps = {{ $quiz->questions->count() }};
    let checkedSet = new Set();
    let isSubmitting = false;

    window.addEventListener('beforeunload', function (e) {
        if (isSubmitting) return;
        e.preventDefault();
        e.returnValue = 'Unsaved changes!';
    });

    let duration = {{ $quiz->duration_minutes * 60 }};
    let timerInterval = setInterval(() => {
        if(duration > 0) {
            duration--;
            let min = Math.floor(duration / 60);
            let sec = duration % 60;
            document.getElementById('timer').innerText = (min < 10 ? '0' : '') + min + ":" + (sec < 10 ? '0' : '') + sec;
        } else {
            clearInterval(timerInterval);
            isSubmitting = true;
            document.getElementById('quizForm').submit();
        }
    }, 1000);

    function handleCardClick(card, type, step) {
        if (card.classList.contains('locked')) return;
        const input = card.querySelector('.card-input');
        if (type.includes('single')) {
            document.querySelectorAll(`#grid-${step} .card-container`).forEach(c => {
                c.classList.remove('is-selected');
                if(c.querySelector('.card-input')) c.querySelector('.card-input').checked = false;
            });
            card.classList.add('is-selected');
            input.checked = true;
        } else {
            card.classList.toggle('is-selected');
            input.checked = !input.checked;
        }
    }

    function checkCurrentQuestion() {
        let step = currentStep;
        let stepContainer = document.getElementById('step-' + step);
        let textarea = stepContainer.querySelector('textarea');
        let isCorrect = false;

        if (textarea) {
            let hiddenInput = document.getElementById(`hidden-q-${textarea.id.split('-')[2]}`);
            let correctText = textarea.getAttribute('data-correct').trim().toLowerCase();
            let userText = textarea.value.trim();
            hiddenInput.value = userText; 
            let card = document.getElementById(`text-card-${step}`);
            card.classList.add('locked');
            if (userText.toLowerCase() === correctText) {
                card.classList.add('reveal-hit');
                document.getElementById(`fib-status-${step}`).innerText = "CRITICAL HIT!";
                isCorrect = true;
            } else {
                card.classList.add('reveal-missed');
                document.getElementById(`fib-status-${step}`).innerHTML = `MISSED!<br><small>Ans: ${textarea.getAttribute('data-correct')}</small>`;
                card.classList.add('is-selected'); 
            }
        } else {
            let containers = stepContainer.querySelectorAll('.card-container');
            let selectedCards = [];
            let correctCards = [];
            containers.forEach(card => {
                card.classList.add('locked');
                let isCorrectAttr = card.getAttribute('data-is-correct') == "1";
                let isSelected = card.classList.contains('is-selected');
                if (isCorrectAttr) correctCards.push(card);
                if (isSelected) selectedCards.push(card);
                card.classList.remove('is-selected');
                if (isCorrectAttr) card.classList.add('reveal-hit');
                else if (isSelected) card.classList.add('reveal-missed');
            });
            if (selectedCards.length > 0) {
                let wrongSelection = selectedCards.some(c => c.getAttribute('data-is-correct') != "1");
                let correctSelectionCount = selectedCards.filter(c => c.getAttribute('data-is-correct') == "1").length;
                if (!wrongSelection && correctSelectionCount === correctCards.length) isCorrect = true;
            }
        }
        let bubble = document.getElementById('nav-btn-' + step);
        bubble.classList.add(isCorrect ? 'correct' : 'wrong');
        checkedSet.add(step);
        document.getElementById('btnCheck').style.display = 'none';
    }

    function showStep(step) {
        document.querySelectorAll('.question-step').forEach(el => el.style.display = 'none');
        document.getElementById('step-' + step).style.display = 'block';
        document.getElementById('btnPrev').style.display = step === 1 ? 'none' : 'block';
        document.getElementById('btnNext').style.display = step === totalSteps ? 'none' : 'block';
        document.getElementById('btnCheck').style.display = checkedSet.has(step) ? 'none' : 'block';
        document.querySelectorAll('.q-nav-btn').forEach(btn => btn.classList.remove('active'));
        document.getElementById('nav-btn-' + step).classList.add('active');
        currentStep = step;
    }

    function nextQuestion() { if(currentStep < totalSteps) showStep(currentStep + 1); }
    function prevQuestion() { if(currentStep > 1) showStep(currentStep - 1); }
    function goToQuestion(step) { showStep(step); }

    function triggerSubmitValidation() {
        document.getElementById('unansweredCount').innerText = totalSteps - checkedSet.size;
        (new bootstrap.Modal(document.getElementById('submissionModal'))).show();
    }
    function forceFormSubmit() { isSubmitting = true; document.getElementById('quizForm').submit(); }
</script>
</body>
</html>