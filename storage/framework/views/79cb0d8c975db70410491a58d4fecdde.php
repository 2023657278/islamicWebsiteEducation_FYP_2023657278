

<?php $__env->startSection('content'); ?>
<div class="container-fluid p-4">
    <h2 class="mb-4 fw-bold">🗂️ Flashcard Manager</h2>

    <?php if(session('success')): ?>
        <div class="alert alert-success mb-3"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert alert-danger mb-3"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <div class="row mb-4">
        
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white fw-bold">➕ Manual Upload</div>
                <div class="card-body">
                    <form action="<?php echo e(route('flashcards.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="mb-2">
                            <label class="form-label">Subject</label>
                            <select name="subject_id" class="form-control" required>
                                <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($sub->id); ?>"><?php echo e($sub->subject_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Topic</label>
                            <input type="text" name="topic" class="form-control" placeholder="e.g. Tajweed" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Question</label>
                            <textarea name="question" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Answer</label>
                            <textarea name="answer" class="form-control" rows="2" required></textarea>
                        </div>
                        <button class="btn btn-primary w-100 mt-2">Save Card</button>
                    </form>
                </div>
            </div>
        </div>

        
        <div class="col-md-6">
            <div class="card shadow-sm h-100 border-info">
                <div class="card-header bg-info text-white fw-bold">⚡ Auto-Generate from Quiz</div>
                <div class="card-body">
                    <p class="text-muted small">First select a subject, then choose a quiz to import.</p>
                    
                    <form action="<?php echo e(route('flashcards.import')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label class="form-label fw-bold">1. Filter by Subject</label>
                            <select id="importSubjectFilter" name="subject_id" class="form-control" required>
                                <option value="">-- Select Subject --</option>
                                <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($sub->id); ?>"><?php echo e($sub->subject_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">2. Select Quiz</label>
                            <select id="quizSelect" name="quiz_id" class="form-control" disabled required>
                                <option value="">-- First Select a Subject --</option>
                                <?php $__currentLoopData = $quizzes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quiz): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($quiz->id); ?>" data-subject="<?php echo e($quiz->subject_id); ?>">
                                        <?php echo e($quiz->title); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-info text-white w-100">
                            Generate Now
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Flashcard List</h4>
        <div class="d-flex gap-2">
            
            <div class="input-group shadow-sm" style="width: 250px;">
                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" id="topicSearch" class="form-control border-start-0" placeholder="Search topic...">
            </div>

            
            <div class="d-flex gap-2 align-items-center bg-white px-2 rounded shadow-sm border">
                <i class="fas fa-filter text-muted ms-1"></i>
                <select id="tableSubjectFilter" class="form-select form-select-sm border-0" style="width: 180px; cursor: pointer;">
                    <option value="all">All Subjects</option>
                    <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($sub->subject_name); ?>"><?php echo e($sub->subject_name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>
    </div>

    
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0" id="flashcardsTable">
                <thead class="bg-light">
                    <tr>
                        <th>Topic</th>
                        <th>Subject</th>
                        <th>Question</th>
                        <th>Answer</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $flashcards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="flashcard-row" 
                        data-subject="<?php echo e($card->subject->subject_name ?? 'General'); ?>" 
                        data-topic="<?php echo e(strtolower($card->topic)); ?>">
                        <td><span class="badge bg-secondary"><?php echo e($card->topic); ?></span></td>
                        <td><small class="fw-bold text-primary"><?php echo e($card->subject->subject_name ?? 'General'); ?></small></td>
                        <td><?php echo e(Str::limit($card->question, 40)); ?></td>
                        <td class="text-success fw-bold"><?php echo e(Str::limit($card->answer, 40)); ?></td>
                        <td>
                            <form action="<?php echo e(route('flashcards.destroy', $card->id)); ?>" method="POST">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">🗑️</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="5" class="text-center p-4 text-muted">No flashcards created yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Logic for Auto-Generate Filtering
        const importSubjectSelect = document.getElementById('importSubjectFilter');
        const quizSelect = document.getElementById('quizSelect');
        const allQuizzes = Array.from(quizSelect.options);

        importSubjectSelect.addEventListener('change', function() {
            const selectedId = this.value;
            quizSelect.innerHTML = '<option value="">-- Select Quiz --</option>';
            quizSelect.disabled = true;

            if (selectedId) {
                const filtered = allQuizzes.filter(opt => opt.getAttribute('data-subject') == selectedId);
                if (filtered.length > 0) {
                    filtered.forEach(opt => quizSelect.appendChild(opt));
                    quizSelect.disabled = false;
                } else {
                    quizSelect.innerHTML = '<option value="">No quizzes found</option>';
                }
            }
        });

        // ✅ COMBINED FILTERING LOGIC (Topic Search + Subject Filter)
        const tableFilter = document.getElementById('tableSubjectFilter');
        const topicSearch = document.getElementById('topicSearch');
        const rows = document.querySelectorAll('.flashcard-row');

        function applyFilters() {
            const selectedSubject = tableFilter.value;
            const searchKeyword = topicSearch.value.toLowerCase();

            rows.forEach(row => {
                const rowSubject = row.getAttribute('data-subject');
                const rowTopic = row.getAttribute('data-topic');

                const matchesSubject = (selectedSubject === 'all' || rowSubject === selectedSubject);
                const matchesTopic = rowTopic.includes(searchKeyword);

                if (matchesSubject && matchesTopic) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        tableFilter.addEventListener('change', applyFilters);
        topicSearch.addEventListener('input', applyFilters);
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.adminhome', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\islamicWebsiteEducation_FYP_2023657278\resources\views/flashcards/index.blade.php ENDPATH**/ ?>