

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4 text-start">
    
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-white p-3 rounded-4 shadow-sm">
            <li class="breadcrumb-item"><a href="<?php echo e(route('student.quizzes.index')); ?>" class="text-decoration-none">Subjects</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('student.quizzes.difficulties', $subject->id)); ?>" class="text-decoration-none"><?php echo e($subject->subject_name); ?></a></li>
            <li class="breadcrumb-item active text-<?php echo e($difficulty == 'Easy' ? 'success' : ($difficulty == 'Medium' ? 'warning' : 'danger')); ?> fw-bold" aria-current="page"><?php echo e($difficulty); ?> Level</li>
        </ol>
    </nav>

    <div class="mb-5">
        <h2 class="fw-black text-dark mb-1">Select a Topic</h2>
        <p class="text-muted">Choose a specific area of study to view available challenges.</p>
    </div>

    
    <div class="row g-4">
        <?php $__empty_1 = true; $__currentLoopData = $topics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $topic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="col-md-6 col-xl-4">
            <a href="<?php echo e(route('student.quizzes.list', ['subject_id' => $subject->id, 'difficulty' => $difficulty, 'topic' => urlencode($topic ?: 'General')])); ?>" 
               class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 h-100 topic-card transition-all">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 bg-primary bg-opacity-10 text-primary p-3 rounded-4 me-3">
                                <i class="fas fa-layer-group fa-2x"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h4 class="fw-bold text-dark mb-1"><?php echo e($topic ?: 'General Knowledge'); ?></h4>
                                <div class="d-flex align-items-center text-muted small">
                                    <span class="me-3"><i class="fas fa-star text-warning me-1"></i> <?php echo e($difficulty); ?></span>
                                    <span><i class="fas fa-chevron-circle-right me-1"></i> View Quizzes</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-12 text-center py-5">
            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486754.png" width="120" class="opacity-25 mb-3">
            <h5 class="text-muted">No topics assigned to this level yet.</h5>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .fw-black { font-weight: 900; }
    .topic-card { border: 1px solid rgba(0,0,0,0.05) !important; background: #fff; }
    .topic-card:hover { 
        transform: translateY(-8px); 
        box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important; 
        background: #fdfdfd;
        border-color: var(--bs-primary) !important;
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('users.students', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\islamicWebsiteEducation_FYP_2023657278\resources\views/users/quizzes/level3_topics.blade.php ENDPATH**/ ?>