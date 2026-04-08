

<?php $__env->startSection('content'); ?>
<div class="container-fluid p-0">
    <div class="mb-4">
        <a href="<?php echo e(route('student.quizzes.topics', $subject->id)); ?>" class="text-muted text-decoration-none mb-2 d-inline-block">
            <i class="fas fa-arrow-left"></i> Back to Topics
        </a>
        <h2 class="fw-bold"><?php echo e($topic ?? 'General'); ?> <span class="text-muted fw-light">/ Quizzes</span></h2>
    </div>

    
    <ul class="nav nav-pills mb-4 gap-2" id="pills-tab" role="tablist">
        <?php $__currentLoopData = ['Easy', 'Medium', 'Hard']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 <?php echo e($loop->first ? 'active' : ''); ?> <?php echo e($level == 'Easy' ? 'btn-outline-success' : ($level == 'Medium' ? 'btn-outline-warning' : 'btn-outline-danger')); ?>" 
                    id="pills-<?php echo e($level); ?>-tab" 
                    data-bs-toggle="pill" 
                    data-bs-target="#pills-<?php echo e($level); ?>" 
                    type="button" 
                    role="tab">
                <?php echo e($level); ?>

            </button>
        </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>

    <div class="tab-content" id="pills-tabContent">
        <?php $__currentLoopData = ['Easy', 'Medium', 'Hard']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="tab-pane fade <?php echo e($loop->first ? 'show active' : ''); ?>" id="pills-<?php echo e($level); ?>" role="tabpanel">
            <div class="row g-4">
                <?php if(isset($groupedQuizzes[$level]) && $groupedQuizzes[$level]->count() > 0): ?>
                    <?php $__currentLoopData = $groupedQuizzes[$level]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quiz): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative">
                            
                            <div class="position-absolute top-0 start-0 w-100" style="height: 5px; background: <?php echo e($level == 'Easy' ? '#22c55e' : ($level == 'Medium' ? '#f59e0b' : '#ef4444')); ?>;"></div>
                            
                            <div class="card-body p-4 d-flex flex-column">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="badge <?php echo e($level == 'Easy' ? 'bg-success' : ($level == 'Medium' ? 'bg-warning' : 'bg-danger')); ?> bg-opacity-10 text-dark border">
                                        <?php echo e($level); ?>

                                    </span>
                                    <?php if($quiz->is_completed): ?>
                                        <i class="fas fa-check-circle text-success fa-lg"></i>
                                    <?php endif; ?>
                                </div>

                                <h5 class="fw-bold text-dark mb-2"><?php echo e($quiz->title); ?></h5>
                                <p class="text-muted small mb-4 line-clamp-2"><?php echo e($quiz->description); ?></p>

                                <div class="mt-auto">
                                    <?php if($quiz->is_completed): ?>
                                        <div class="d-flex justify-content-between align-items-center mb-3 bg-light p-2 rounded">
                                            <small class="fw-bold text-muted">Best Score</small>
                                            <span class="fw-bold text-success"><?php echo e($quiz->my_score); ?>%</span>
                                        </div>
                                        <a href="<?php echo e(route('student.quizzes.take', $quiz->id)); ?>" class="btn btn-outline-dark w-100 rounded-pill">Retake</a>
                                    <?php else: ?>
                                        <a href="<?php echo e(route('student.quizzes.take', $quiz->id)); ?>" class="btn btn-primary w-100 rounded-pill">Start Quiz</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <p class="text-muted">No <?php echo e($level); ?> quizzes available for this topic yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('users.students', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\islamicWebsiteEducation_FYP_2023657278\resources\views/users/quizzes/level3_list.blade.php ENDPATH**/ ?>