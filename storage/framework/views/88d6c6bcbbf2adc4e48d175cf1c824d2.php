

<?php $__env->startSection('content'); ?>
<div class="container d-flex justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="card border-0 shadow rounded-4 text-center p-5" style="max-width: 450px; width: 100%;">

        <div class="mb-4">
            <?php if($percentage >= 80): ?>
                <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                    <i class="fas fa-trophy text-white fa-3x"></i>
                </div>
            <?php elseif($percentage >= 50): ?>
                <div class="bg-warning rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                    <i class="fas fa-thumbs-up text-white fa-3x"></i>
                </div>
            <?php else: ?>
                <div class="bg-danger rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                    <i class="fas fa-times text-white fa-3x"></i>
                </div>
            <?php endif; ?>
        </div>

        <h2 class="fw-bold text-dark mb-1">
            <?php if($percentage >= 50): ?> Alhamdulillah! <?php else: ?> Keep Trying! <?php endif; ?>
        </h2>
        <p class="text-muted mb-4">You've completed the quiz.</p>

        <div class="bg-light rounded-4 p-4 mb-4 border <?php echo e($percentage >= 50 ? 'border-success' : 'border-danger'); ?> border-opacity-25">
            <h1 class="display-3 fw-bold <?php echo e($percentage >= 50 ? 'text-success' : 'text-danger'); ?> mb-0"><?php echo e($percentage); ?>%</h1>
            <p class="text-muted mb-0"><?php echo e($score); ?> out of <?php echo e($totalQuestions); ?> correct answers</p>
        </div>

        <div class="d-grid gap-2">
            <a href="<?php echo e(route('student.quizzes.take', $quiz->id)); ?>" class="btn btn-outline-dark rounded-pill fw-bold">Retake Quiz</a>
            <a href="<?php echo e(route('student.quizzes.index')); ?>" class="btn btn-primary rounded-pill fw-bold">Back to Quiz Center</a>
        </div>

    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('users.students', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\islamicWebsiteEducation_FYP_2023657278\resources\views/users/quizzes/result.blade.php ENDPATH**/ ?>