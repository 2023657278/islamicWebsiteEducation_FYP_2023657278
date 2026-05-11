

<?php $__env->startSection('content'); ?>
<?php $userPts = Auth::user()->pvp_points; ?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="fas fa-radar me-2 text-primary"></i> Active Missions</h2>
        <a href="<?php echo e(route('student.quizzes.select_mode', $subject->id)); ?>" class="btn btn-light rounded-pill">Back</a>
    </div>

    <div class="row g-3">
        <?php $__empty_1 = true; $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
            $diff = strtolower($room->quiz->difficulty);
            // 🔒 Define lock logic
            $isLocked = ($diff === 'medium' && $userPts < 100) || ($diff === 'hard' && $userPts < 300);
            $color = $diff == 'easy' ? 'success' : ($diff == 'medium' ? 'warning' : 'danger');
        ?>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 mb-2 <?php echo e($isLocked ? 'opacity-75' : ''); ?>">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h5 class="fw-bold mb-0"><?php echo e($room->quiz->title); ?></h5>
                            <span class="badge bg-<?php echo e($color); ?> text-uppercase small" style="font-size: 0.6rem;"><?php echo e($diff); ?></span>
                        </div>
                        <small class="text-muted">Host: <?php echo e($room->host->name ?? 'Warrior Leader'); ?></small>
                    </div>
                    
                    <div class="text-end">
                        <span class="badge bg-light text-dark mb-2 d-block border">
                            <?php echo e($room->participants_count); ?>/20 Players
                        </span>

                        <?php if($isLocked): ?>
                            
                            <button disabled class="btn btn-secondary btn-sm px-4 rounded-pill">
                                <i class="fas fa-lock me-1"></i> <?php echo e($diff === 'medium' ? '100' : '300'); ?> PTS
                            </button>
                        <?php else: ?>
                            
                            <form action="<?php echo e(route('student.quizzes.join')); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="room_code" value="<?php echo e($room->room_code); ?>">
                                <button type="submit" class="btn btn-primary btn-sm px-4 rounded-pill">JOIN</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-12 text-center py-5">
            <div class="text-muted mb-3"><i class="fas fa-ghost fa-3x"></i></div>
            <h5 class="text-muted">No public missions found. Start one yourself!</h5>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('users.students', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\islamicWebsiteEducation_FYP_2023657278\resources\views/users/quizzes/lobby_browser.blade.php ENDPATH**/ ?>