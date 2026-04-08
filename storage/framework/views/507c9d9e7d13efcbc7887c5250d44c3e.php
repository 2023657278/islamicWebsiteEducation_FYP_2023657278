

<?php $__env->startSection('content'); ?>
<div class="container-fluid p-0">
    <div class="mb-4">
        <a href="<?php echo e(route('student.quizzes.index')); ?>" class="text-muted text-decoration-none mb-2 d-inline-block">
            <i class="fas fa-arrow-left"></i> Back to Subjects
        </a>
        <h2 class="fw-bold"><?php echo e($subject->subject_name); ?> <span class="text-muted fw-light">/ Topics</span></h2>
    </div>

    <div class="row g-3">
        <?php $__empty_1 = true; $__currentLoopData = $topics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $topic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="col-12">
            
            <a href="<?php echo e(route('student.quizzes.list', ['subject_id' => $subject->id, 'topic' => urlencode($topic ?: 'General')])); ?>" 
               class="card border-0 shadow-sm rounded-4 text-decoration-none p-4 hover-bg">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-3">
                            <i class="fas fa-layer-group fa-lg"></i>
                        </div>
                        <div>
                            
                            <h5 class="fw-bold text-dark mb-0"><?php echo e($topic ?: 'General Knowledge'); ?></h5>
                            <small class="text-muted">Click to view difficulty levels</small>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-muted"></i>
                </div>
            </a>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-12 text-center py-5">
            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486754.png" width="100" class="mb-3 opacity-50">
            <p class="text-muted">No topics found for this subject.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .hover-bg:hover { background-color: #f8f9fa; transform: translateX(5px); transition: all 0.2s; }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('users.students', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\islamicWebsiteEducation_FYP_2023657278\resources\views/users/quizzes/level2_topics.blade.php ENDPATH**/ ?>