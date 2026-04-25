

<?php $__env->startSection('content'); ?>
<div class="container">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-dark mb-0"><?php echo e($quiz->title); ?></h2>
            <p class="text-muted mb-0">
                <span class="badge badge-info"><?php echo e($quiz->subject->subject_name); ?></span>
                <span class="badge badge-secondary"><?php echo e($quiz->topic ?? 'General'); ?></span>
                <span class="badge badge-warning text-dark"><?php echo e($quiz->difficulty); ?></span>
            </p>
        </div>
        <a href="<?php echo e(url(route('quizzes.index'))); ?>" class="btn btn-secondary">Done</a>
    </div>

    <div class="row">
        
        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm border-left-primary">
                <div class="card-header bg-white">
                    <h5 class="m-0 font-weight-bold text-primary">Add Question</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('questions.store', $quiz->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold text-uppercase">Question Text</label>
                            <textarea name="question_text" class="form-control" rows="2" required placeholder="Type question here..."></textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="small font-weight-bold text-uppercase">Points</label>
                                <input type="number" name="points" class="form-control" value="1" min="1">
                            </div>
                            <div class="col-md-8">
                                <label class="small font-weight-bold text-uppercase">Question Type</label>
                                <select name="question_type" id="typeSelector" class="form-control">
                                    <option value="single">Single Choice (Radio)</option>
                                    <option value="multiple">Multiple Correct (Checkbox)</option>
                                    <option value="text">Fill in the Blanks</option>
                                </select>
                            </div>
                        </div>

                        <hr>

                        <div id="choicesSection">
                            <label class="small font-weight-bold text-uppercase mb-2">Options</label>
                            <small class="d-block text-muted mb-2">Check the box/radio for the correct answer(s).</small>
                            
                            <div id="optionsContainer">
                                <div class="input-group mb-2 option-row">
                                    <div class="input-group-text bg-white">
                                        <input type="radio" name="correct_single" value="0" class="type-radio" checked>
                                        <input type="checkbox" name="correct_multiple[]" value="0" class="type-check" style="display:none;">
                                    </div>
                                    <input type="text" name="options[]" class="form-control" placeholder="Option 1" required>
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-btn"><i class="fas fa-times"></i></button>
                                </div>
                                <div class="input-group mb-2 option-row">
                                    <div class="input-group-text bg-white">
                                        <input type="radio" name="correct_single" value="1" class="type-radio">
                                        <input type="checkbox" name="correct_multiple[]" value="1" class="type-check" style="display:none;">
                                    </div>
                                    <input type="text" name="options[]" class="form-control" placeholder="Option 2" required>
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-btn"><i class="fas fa-times"></i></button>
                                </div>
                            </div>

                            <button type="button" class="btn btn-sm btn-info mt-2" id="addOptionBtn">
                                <i class="fas fa-plus"></i> Add Another Option
                            </button>
                        </div>

                        <div id="textSection" style="display: none;">
                            <label class="small font-weight-bold text-uppercase mb-2">Correct Answer</label>
                            <input type="text" name="text_answer" class="form-control" placeholder="Enter the exact correct answer here...">
                            <small class="text-muted">Exact match required for auto-grading.</small>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block mt-4 shadow-sm">
                            <i class="fas fa-save mr-1"></i> Save Question
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="m-0 font-weight-bold text-dark">Questions (<?php echo e($quiz->questions->count()); ?>)</h5>
                </div>
                <div class="card-body p-0">
                    <?php $__empty_1 = true; $__currentLoopData = $quiz->questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="border-bottom p-3">
                        <div class="d-flex justify-content-between">
                            <strong>Q<?php echo e($index + 1); ?>. <?php echo e($q->question_text); ?></strong>
                            <div>
                                <span class="badge badge-secondary"><?php echo e(ucfirst($q->question_type)); ?></span>
                                <span class="badge badge-warning"><?php echo e($q->points); ?> pts</span>
                                <form action="<?php echo e(route('questions.destroy', $q->id)); ?>" method="POST" class="d-inline ml-2">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="btn btn-sm text-danger border-0 bg-transparent p-0" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="mt-2 ml-3">
                            <?php if($q->question_type == 'text'): ?>
                                <span class="text-success font-weight-bold">Answer: <?php echo e($q->options->first()->option_text ?? '-'); ?></span>
                            <?php else: ?>
                                <ul class="list-unstyled mb-0">
                                    <?php $__currentLoopData = $q->options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="<?php echo e($opt->is_correct ? 'text-success font-weight-bold' : 'text-muted'); ?>">
                                        <i class="fas <?php echo e($opt->is_correct ? 'fa-check-circle' : 'fa-circle'); ?>"></i> 
                                        <?php echo e($opt->option_text); ?>

                                    </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="p-4 text-center text-muted">No questions yet.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const typeSelector = document.getElementById('typeSelector');
        const choicesSection = document.getElementById('choicesSection');
        const textSection = document.getElementById('textSection');
        const optionsContainer = document.getElementById('optionsContainer');
        const addOptionBtn = document.getElementById('addOptionBtn');

        typeSelector.addEventListener('change', function() {
            let type = this.value;
            if (type === 'text') {
                choicesSection.style.display = 'none';
                textSection.style.display = 'block';
                toggleInputs(choicesSection, true); 
                toggleInputs(textSection, false);
            } else {
                choicesSection.style.display = 'block';
                textSection.style.display = 'none';
                toggleInputs(choicesSection, false);
                toggleInputs(textSection, true);

                let radios = document.querySelectorAll('.type-radio');
                let checks = document.querySelectorAll('.type-check');
                
                if (type === 'multiple') {
                    radios.forEach(el => el.style.display = 'none');
                    checks.forEach(el => el.style.display = 'inline-block');
                } else {
                    radios.forEach(el => el.style.display = 'inline-block');
                    checks.forEach(el => el.style.display = 'none');
                }
            }
        });

        function toggleInputs(section, disable) {
            section.querySelectorAll('input, textarea, select').forEach(el => el.disabled = disable);
        }

        addOptionBtn.addEventListener('click', function() {
            let index = optionsContainer.children.length;
            let type = typeSelector.value;
            let displayRadio = (type === 'single') ? 'inline-block' : 'none';
            let displayCheck = (type === 'multiple') ? 'inline-block' : 'none';

            let html = `
                <div class="input-group mb-2 option-row">
                    <div class="input-group-text bg-white">
                        <input type="radio" name="correct_single" value="${index}" class="type-radio" style="display:${displayRadio}">
                        <input type="checkbox" name="correct_multiple[]" value="${index}" class="type-check" style="display:${displayCheck}">
                    </div>
                    <input type="text" name="options[]" class="form-control" placeholder="Option ${index + 1}" required>
                    <button type="button" class="btn btn-outline-danger btn-sm remove-btn"><i class="fas fa-times"></i></button>
                </div>
            `;
            optionsContainer.insertAdjacentHTML('beforeend', html);
        });

        optionsContainer.addEventListener('click', function(e) {
            if (e.target.closest('.remove-btn')) {
                if (optionsContainer.children.length > 1) {
                    e.target.closest('.option-row').remove();
                    reindexOptions();
                } else {
                    alert("You need at least one option.");
                }
            }
        });

        function reindexOptions() {
            let rows = optionsContainer.querySelectorAll('.option-row');
            rows.forEach((row, index) => {
                row.querySelector('.type-radio').value = index;
                row.querySelector('.type-check').value = index;
                row.querySelector('input[type="text"]').placeholder = `Option ${index + 1}`;
            });
        }
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.adminhome', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\islamicWebsiteEducation_FYP_2023657278\resources\views/quizzes/manage.blade.php ENDPATH**/ ?>