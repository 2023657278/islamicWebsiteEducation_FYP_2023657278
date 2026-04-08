

<?php $__env->startSection('content'); ?>
<div class="container-fluid p-0">
    
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-bold text-dark">Quiz Center</h2>
            <p class="text-muted">Select a subject to begin.</p>
        </div>
        <div class="d-flex gap-3">
            <div class="bg-white p-3 rounded-4 shadow-sm text-center">
                <h4 class="fw-bold mb-0 text-success"><?php echo e($completedCount); ?></h4>
                <small class="text-muted">Completed</small>
            </div>
            <div class="bg-white p-3 rounded-4 shadow-sm text-center">
                <h4 class="fw-bold mb-0 text-primary"><?php echo e(round($avgScore)); ?>%</h4>
                <small class="text-muted">Avg Score</small>
            </div>
        </div>
    </div>

    
    <div class="row g-4">
        <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-md-4 col-lg-3">
            <a href="<?php echo e(route('student.quizzes.topics', $sub->id)); ?>" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm rounded-4 hover-scale transition-all">
                    <div class="card-body text-center p-5">
                        <div class="bg-light rounded-circle d-inline-flex p-3 mb-3 text-danger">
                            <i class="fas fa-book fa-2x"></i> 
                        </div>
                        <h4 class="fw-bold text-dark mb-1"><?php echo e($sub->subject_name); ?></h4>
                        <small class="text-muted"><?php echo e($sub->quizzes->count()); ?> Quizzes Available</small>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>

<style>
    .hover-scale { transition: transform 0.2s; }
    .hover-scale:hover { transform: translateY(-5px); }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('users.students', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\islamicWebsiteEducation_FYP_2023657278\resources\views/users/quizzes/level1_subjects.blade.php ENDPATH**/ ?>