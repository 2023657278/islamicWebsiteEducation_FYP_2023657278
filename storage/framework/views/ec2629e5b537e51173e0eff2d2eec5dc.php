

<?php $__env->startSection('content'); ?>
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
    <form action="<?php echo e(route('student.quizzes.submit', $quiz->id)); ?>" method="POST" id="quizForm">
        <?php echo csrf_field(); ?>

        
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="fw-bold mb-0"><?php echo e($quiz->title); ?></h4>
                <small class="text-muted">Answer all questions before time runs out.</small>
            </div>
            <div class="text-end">
                <span class="badge bg-danger fs-6 px-3 py-2">
                    <i class="fas fa-clock me-2"></i> <span id="timer">00:00</span>
                </span>
            </div>
        </div>

        
        <div class="card border-0 shadow-sm rounded-4 p-3 mb-4">
            <div class="d-flex flex-wrap gap-2 justify-content-center">
                <?php $__currentLoopData = $quiz->questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="q-nav-btn <?php echo e($index === 0 ? 'active' : ''); ?>" 
                     id="nav-btn-<?php echo e($index + 1); ?>" 
                     onclick="goToQuestion(<?php echo e($index + 1); ?>)">
                    <?php echo e($index + 1); ?>

                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        
        <div class="questions-container">
            <?php $__currentLoopData = $quiz->questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="question-step" id="step-<?php echo e($index + 1); ?>" style="<?php echo e($index > 0 ? 'display:none;' : ''); ?>">
                
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom p-4">
                        <span class="badge bg-light text-dark border mb-2">Question <?php echo e($index + 1); ?></span>
                        <h5 class="fw-bold text-dark mb-0"><?php echo e($q->question_text); ?></h5>
                        <small class="text-muted">Type: <?php echo e(ucfirst($q->question_type)); ?></small>
                    </div>

                    <div class="card-body p-4">
                        
                        <?php if($q->question_type === 'single'): ?>
                            <div class="d-grid gap-2">
                                <?php $__currentLoopData = $q->options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="option-card">
                                    <input type="radio" name="q_<?php echo e($q->id); ?>" value="<?php echo e($opt->id); ?>" class="option-input d-none" onchange="markAnswered(<?php echo e($index + 1); ?>)">
                                    <span class="option-content d-flex align-items-center">
                                        <span class="fw-bold text-muted border rounded-circle px-2 py-1 me-3 small">
                                            <?php echo e(chr(65 + $loop->index)); ?>

                                        </span>
                                        <?php echo e($opt->option_text); ?>

                                    </span>
                                </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>

                        
                        <?php elseif($q->question_type === 'multiple'): ?>
                            <div class="d-grid gap-2">
                                <?php $__currentLoopData = $q->options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="option-card">
                                    <input type="checkbox" name="q_<?php echo e($q->id); ?>[]" value="<?php echo e($opt->id); ?>" class="option-input d-none" onchange="markAnswered(<?php echo e($index + 1); ?>)">
                                    <span class="option-content d-flex align-items-center">
                                        <span class="fw-bold text-muted border rounded px-2 py-1 me-3 small">
                                            <i class="fas fa-check"></i>
                                        </span>
                                        <?php echo e($opt->option_text); ?>

                                    </span>
                                </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <small class="text-muted mt-2 d-block"><i class="fas fa-info-circle"></i> Select all correct answers.</small>

                        
                        <?php elseif($q->question_type === 'text'): ?>
                            <div class="form-group">
                                <label class="fw-bold text-muted small mb-2">YOUR ANSWER:</label>
                                <textarea name="q_<?php echo e($q->id); ?>" class="form-control bg-light border-0 p-3 rounded-3" rows="3" placeholder="Type your answer here..." oninput="markAnswered(<?php echo e($index + 1); ?>)"></textarea>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        
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
    let totalSteps = <?php echo e($quiz->questions->count()); ?>;
    
    // 1. TIMER LOGIC
    let duration = <?php echo e($quiz->duration_minutes * 60); ?>;
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('users.students', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\islamicWebsiteEducation_FYP_2023657278\resources\views/users/quizzes/take.blade.php ENDPATH**/ ?>