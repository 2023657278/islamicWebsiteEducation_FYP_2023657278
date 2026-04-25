

<?php $__env->startSection('content'); ?>
<div class="container-fluid p-0 text-start">
    
    <div class="mb-5">
        <a href="<?php echo e(route('student.quizzes.index')); ?>" class="text-muted text-decoration-none">
            <i class="fas fa-arrow-left me-1"></i> Back to Subjects
        </a>
        <h2 class="fw-bold mt-2"><?php echo e($subject->subject_name); ?> <span class="text-muted fw-light">/ Skill Levels</span></h2>
        <p class="text-muted">Master each level to unlock the next challenge.</p>
    </div>

    <div class="row g-4">
        <?php $__currentLoopData = ['Easy', 'Medium', 'Hard']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php 
                $isAllowed = in_array($level, $allowed); 
                $color = $level == 'Easy' ? 'success' : ($level == 'Medium' ? 'warning' : 'danger');
                $levelStats = $stats[$level];
            ?>
            
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden <?php echo e(!$isAllowed ? 'bg-light' : ''); ?>" style="transition: transform 0.3s;">
                    
                    <div style="height: 6px; background-color: var(--bs-<?php echo e($color); ?>);"></div>
                    
                    <div class="card-body p-4 text-center d-flex flex-column">
                        <div class="mb-3">
                            <?php if($isAllowed): ?>
                                <div class="bg-<?php echo e($color); ?> bg-opacity-10 text-<?php echo e($color); ?> rounded-circle d-inline-flex p-3">
                                    <i class="fas fa-medal fa-2x"></i>
                                </div>
                            <?php else: ?>
                                <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle d-inline-flex p-3">
                                    <i class="fas fa-lock fa-2x"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <h3 class="fw-bold text-dark mb-1"><?php echo e($level); ?></h3>
                        
                        
                        <div class="mt-3 mb-4">
                            <div class="d-flex justify-content-between mb-1 small text-muted font-weight-bold">
                                <span>Progress</span>
                                <span><?php echo e($levelStats['done']); ?>/<?php echo e($levelStats['total']); ?> Quizzes</span>
                            </div>
                            <div class="progress" style="height: 10px; border-radius: 5px;">
                                <?php 
                                    $percent = ($levelStats['total'] > 0) ? ($levelStats['done'] / $levelStats['total']) * 100 : 0;
                                ?>
                                <div class="progress-bar bg-<?php echo e($color); ?>" role="progressbar" style="width: <?php echo e($percent); ?>%"></div>
                            </div>
                            <?php if($isAllowed && $levelStats['done'] > 0): ?>
                                <small class="text-<?php echo e($color); ?> fw-bold d-block mt-2">Avg Score: <?php echo e($levelStats['avg']); ?>%</small>
                            <?php endif; ?>
                        </div>

                        <div class="mt-auto">
                            <?php if($isAllowed): ?>
                                <a href="<?php echo e(route('student.quizzes.topics_diff', [$subject->id, $level])); ?>" 
                                   class="btn btn-<?php echo e($color); ?> w-100 rounded-pill fw-bold py-2 shadow-sm">
                                   Enter Level
                                </a>
                            <?php else: ?>
                                <div class="p-3 bg-white border rounded-4 small text-muted">
                                    <i class="fas fa-info-circle text-warning me-1"></i> 
                                    Clear <b><?php echo e($level == 'Medium' ? 'Easy' : 'Medium'); ?></b> with all quizzes done and avg > 50% to unlock.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>

<style>
    .card:hover { transform: translateY(-5px); }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('users.students', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\islamicWebsiteEducation_FYP_2023657278\resources\views/users/quizzes/level2_difficulties.blade.php ENDPATH**/ ?>