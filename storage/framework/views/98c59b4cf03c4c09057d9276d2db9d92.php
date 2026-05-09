

<?php $__env->startSection('content'); ?>
<style>
    /* 1. ARENA HUD & LAYOUT */
    .battle-hud { background: white; border-radius: 15px; padding: 20px; margin-bottom: 25px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); }
    .boss-hp-bar { height: 12px; background: #f1f5f9; border-radius: 20px; overflow: hidden; border: 1px solid #e2e8f0; }
    .boss-hp-fill { height: 100%; background: linear-gradient(90deg, #ef4444, #b91c1c); transition: width 0.8s ease; }
    
    /* 2. 3D CARD MECHANICS */
    .answer-grid { perspective: 1000px; display: flex; flex-direction: column; gap: 12px; }
    .card-container { height: 75px; cursor: pointer; perspective: 1000px; position: relative; }
    .card-inner { position: relative; width: 100%; height: 100%; transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1); transform-style: preserve-3d; }
    
    /* Selection Flip */
    .is-selected .card-inner { transform: rotateX(180deg); }
    
    .card-front, .card-back { position: absolute; width: 100%; height: 100%; backface-visibility: hidden; border: 2px solid #eee; border-radius: 12px; display: flex; align-items: center; padding: 0 20px; font-weight: 600; transition: all 0.4s; }
    .card-front { background: white; z-index: 2; color: #334155; }
    .card-back { background: #3b82f6; color: white; transform: rotateX(180deg); display: flex; justify-content: space-between; z-index: 1; }

    /* 🟢 THE REVEAL COLORS (Applied only to the ACTUAL CORRECT CARD) */
    .reveal-hit .card-front { border-color: #10b981 !important; border-width: 4px; background-color: #ecfdf5; color: #059669; box-shadow: 0 0 15px rgba(16, 185, 129, 0.4); }
    .reveal-missed .card-front { border-color: #ef4444 !important; border-width: 4px; background-color: #fef2f2; color: #dc2626; box-shadow: 0 0 15px rgba(239, 68, 68, 0.4); }

    .q-nav-dot { width: 35px; height: 35px; border-radius: 50%; border: 1px solid #ddd; background: white; display: flex; align-items: center; justify-content: center; font-weight: bold; transition: 0.3s; }
    .q-nav-dot.active { background: #1e293b; color: white; }
    .q-nav-dot.correct { background: #10b981 !important; color: white; }
    .q-nav-dot.wrong { background: #ef4444 !important; color: white; }

    .locked { pointer-events: none !important; }
</style>

<div class="container py-4" style="max-width: 900px;">
    <form action="<?php echo e(route('student.quizzes.submit', $quiz->id)); ?>" method="POST" id="quizForm">
        <?php echo csrf_field(); ?>

        
        <div class="battle-hud">
            <div class="row align-items-center">
                <div class="col-4">
                    <small class="text-muted text-uppercase fw-bold" style="font-size: 10px;">Warrior</small>
                    <h5 class="fw-bold mb-0"><?php echo e(Auth::user()->name); ?></h5>
                </div>
                <div class="col-4 text-center">
                    <div class="bg-dark text-white rounded-pill px-4 py-2 d-inline-flex shadow-sm">
                        <i class="fas fa-clock me-2 text-warning"></i> <span id="timer" class="font-monospace fw-bold">30:00</span>
                    </div>
                </div>
                <div class="col-4 text-end">
                    <small class="text-danger text-uppercase fw-bold" style="font-size: 10px;">Guardian HP</small>
                    <div class="boss-hp-bar mt-1">
                        <div id="boss-hp-fill" class="boss-hp-fill" style="width: 100%"></div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="card border-0 shadow-sm rounded-4 p-3 mb-4">
            <div class="d-flex flex-wrap gap-2 justify-content-center">
                <?php $__currentLoopData = $quiz->questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="q-nav-dot <?php echo e($index === 0 ? 'active' : ''); ?>" id="nav-btn-<?php echo e($index + 1); ?>"><?php echo e($index + 1); ?></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        
        <div class="questions-container">
            <?php $__currentLoopData = $quiz->questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="question-step" id="step-<?php echo e($index + 1); ?>" style="<?php echo e($index > 0 ? 'display:none;' : ''); ?>">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom p-4 text-center">
                        <span class="badge bg-light text-dark border mb-2">Question <?php echo e($index + 1); ?> of <?php echo e($quiz->questions->count()); ?></span>
                        <h4 class="fw-bold text-dark mb-0"><?php echo e($q->question_text); ?></h4>
                    </div>

                    <div class="card-body p-4 bg-light/30">
                        <?php if(in_array($q->question_type, ['single', 'single_choice', 'multiple', 'multiple_choice'])): ?>
                            <div class="answer-grid" id="grid-<?php echo e($index + 1); ?>">
                                <?php $__currentLoopData = $q->options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $optIndex => $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="card-container" data-is-correct="<?php echo e($opt->is_correct); ?>" onclick="handleCardClick(this, '<?php echo e($q->question_type); ?>', <?php echo e($index + 1); ?>)">
                                    <div class="card-inner">
                                        <div class="card-front">
                                            <div class="fw-bold text-muted border rounded-circle px-2 py-1 me-3 small"><?php echo e(chr(65 + $optIndex)); ?></div>
                                            <div class="option-text"><?php echo e($opt->option_text); ?></div>
                                        </div>
                                        <div class="card-back">
                                            <span class="fw-black italic uppercase">Targeted</span>
                                            <i class="fas fa-crosshairs"></i>
                                            <input type="<?php echo e(strpos($q->question_type, 'single') !== false ? 'radio' : 'checkbox'); ?>" 
                                                   name="q_<?php echo e($q->id); ?><?php echo e(strpos($q->question_type, 'multiple') !== false ? '[]' : ''); ?>" 
                                                   value="<?php echo e($opt->id); ?>" class="d-none card-input">
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>

                        <?php elseif(in_array($q->question_type, ['text', 'fill_in_the_blank'])): ?>
                            <div class="card-container w-100" style="height: 150px;" id="text-card-<?php echo e($index + 1); ?>">
                                <div class="card-inner">
                                    <div class="card-front p-0">
                                        <textarea id="text-input-<?php echo e($q->id); ?>" 
          class="form-control border-0 text-center fs-3 fw-bold h-100" 
          placeholder="Type Divine Code..." 
          data-correct="<?php echo e($q->correct_answer_text); ?>"></textarea>
                                        
                                        <input type="hidden" name="q_<?php echo e($q->id); ?>" id="hidden-q-<?php echo e($q->id); ?>">
                                    </div>
                                    <div class="card-back flex-column justify-content-center text-center">
                                        <h4 class="fw-black uppercase mb-1" id="fib-status-<?php echo e($index + 1); ?>">Strike Result</h4>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="mt-5 text-center">
                            <button type="button" class="btn btn-warning btn-lg px-5 py-3 fw-black rounded-pill shadow-lg" 
                                    id="exec-<?php echo e($index + 1); ?>" onclick="executeStrike(<?php echo e($index + 1); ?>, '<?php echo e($q->question_type); ?>')">
                                <i class="fas fa-bolt me-2"></i> EXECUTE STRIKE
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="mt-4 text-end">
            <button type="button" class="btn btn-dark rounded-pill px-5" id="btnNext" onclick="nextQuestion()" style="display:none;">NEXT SECTOR <i class="fas fa-chevron-right ms-2"></i></button>
            <button type="submit" class="btn btn-danger rounded-pill px-5" id="btnSubmit" style="display:none;">SUBMIT MISSION <i class="fas fa-paper-plane ms-2"></i></button>
        </div>
    </form>
</div>

<script>
    let currentStep = 1;
    let totalSteps = <?php echo e($quiz->questions->count()); ?>;
    let bossHp = 100;
    const hpPerHit = 100 / totalSteps;

    function handleCardClick(card, type, step) {
        if (card.classList.contains('locked')) return;
        const input = card.querySelector('.card-input');
        
        if (type.includes('single')) {
            document.querySelectorAll(`#grid-${step} .card-container`).forEach(c => {
                c.classList.remove('is-selected');
                if(c.querySelector('.card-input')) c.querySelector('.card-input').checked = false;
            });
            card.classList.add('is-selected');
            input.checked = true; // LOCK IN DATA
        } else {
            card.classList.toggle('is-selected');
            input.checked = !input.checked;
        }
    }

    function executeStrike(step, type) {
        const execBtn = document.getElementById(`exec-${step}`);
        let isCorrect = false;
        execBtn.style.display = 'none';

        if (type.includes('text')) {
            const area = document.querySelector(`#step-${step} textarea`);
            const hidden = document.getElementById(`hidden-q-${area.id.split('-')[2]}`);
            const correctText = area.getAttribute('data-correct').trim();
            const userText = area.value.trim();
            
            // FIX: Ensure data reaches Controller
            hidden.value = userText; 
            const card = document.getElementById(`text-card-${step}`);
            card.classList.add('locked');

            if (userText.toLowerCase() === correctText.toLowerCase()) {
                card.classList.add('reveal-hit');
                document.getElementById(`fib-status-${step}`).innerText = "CRITICAL HIT!";
                isCorrect = true;
            } else {
                card.classList.add('reveal-missed');
                // FIX: Reveal actual answer
                document.getElementById(`fib-status-${step}`).innerHTML = `STRIKE MISSED!<br><small>Answer: ${correctText}</small>`;
                card.classList.add('is-selected'); // Flip back to show text result
            }
        } 
        else {
            const containers = document.querySelectorAll(`#step-${step} .card-container`);
            containers.forEach(card => {
                card.classList.add('locked');
                const isCorrectAttr = card.getAttribute('data-is-correct') == "1";
                const isSelected = card.classList.contains('is-selected');

                // FIX: Flip back all cards
                card.classList.remove('is-selected');

                if (isCorrectAttr) {
                    if (isSelected) {
                        card.classList.add('reveal-hit'); // Picked correctly -> Green
                        isCorrect = true;
                    } else {
                        card.classList.add('reveal-missed'); // Missed the correct card -> Red
                    }
                }
            });
        }

        // HUD Damage Logic
        if (isCorrect) {
            bossHp -= hpPerHit;
            document.getElementById('boss-hp-fill').style.width = Math.max(0, bossHp) + "%";
            document.getElementById(`nav-btn-${step}`).classList.add('correct');
        } else {
            document.getElementById(`nav-btn-${step}`).classList.add('wrong');
        }

        if (step === totalSteps) document.getElementById('btnSubmit').style.display = 'inline-block';
        else document.getElementById('btnNext').style.display = 'inline-block';
    }

    function nextQuestion() {
        document.getElementById('btnNext').style.display = 'none';
        currentStep++;
        document.querySelectorAll('.question-step').forEach(el => el.style.display = 'none');
        document.getElementById('step-' + currentStep).style.display = 'block';
        document.querySelectorAll('.q-nav-dot').forEach(d => d.classList.remove('active'));
        document.getElementById(`nav-btn-${currentStep}`).classList.add('active');
    }

    let duration = <?php echo e(($quiz->duration_minutes ?? 30) * 60); ?>;
    setInterval(() => {
        if(duration > 0) {
            duration--;
            let min = Math.floor(duration / 60);
            let sec = duration % 60;
            document.getElementById('timer').innerText = `${min}:${sec < 10 ? '0' : ''}${sec}`;
        } else {
            document.getElementById('quizForm').submit();
        }
    }, 1000);
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('users.students', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\islamicWebsiteEducation_FYP_2023657278\resources\views/users/quizzes/arena.blade.php ENDPATH**/ ?>