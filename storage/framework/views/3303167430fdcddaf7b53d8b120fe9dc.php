

<?php $__env->startSection('content'); ?>

<div class="container-fluid py-4 py-md-5">
    
    <div class="row justify-content-center">
        
        <div class="col-12 col-xxl-8 col-xl-9 col-lg-10">
            
            
            <div class="text-center mb-5">
                <a href="<?php echo e(route('student.quizzes.topics_diff', [$subject->id, $difficulty])); ?>" 
                   class="btn btn-link text-decoration-none text-muted fw-bold mb-3 transition-all hover-translate-x">
                    <i class="fas fa-chevron-left me-2"></i> BACK TO TOPICS
                </a>

                <div class="header-content animate-fade-in">
                    <p class="text-primary text-uppercase fw-black mb-1 small tracking-widest">
                        <?php echo e($subject->subject_name); ?>

                    </p>
                    <h1 class="display-3 fw-black text-dark mb-3"><?php echo e($topic); ?></h1>
                    
                    
                    <div class="d-flex justify-content-center align-items-center gap-3">
                        <?php $color = $difficulty == 'Easy' ? 'success' : ($difficulty == 'Medium' ? 'warning' : 'danger'); ?>
                        <div class="flex-grow-1 border-top border-2 opacity-25 d-none d-sm-block"></div>
                        <span class="badge bg-<?php echo e($color); ?> bg-opacity-10 text-<?php echo e($color); ?> border border-<?php echo e($color); ?> rounded-pill px-4 py-2 fs-6 fw-black">
                            <i class="fas fa-signal me-2"></i><?php echo e(strtoupper($difficulty)); ?> LEVEL
                        </span>
                        <div class="flex-grow-1 border-top border-2 opacity-25 d-none d-sm-block"></div>
                    </div>
                </div>
            </div>

            
            <?php $__empty_1 = true; $__currentLoopData = $quizzes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quiz): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="card border-0 shadow-lg rounded-5 mb-4 overflow-hidden position-relative hover-lift transition-all">
                
                
                <div class="position-absolute top-0 start-0 h-100" style="width: 10px; background-color: var(--bs-<?php echo e($color); ?>);"></div>

                <div class="card-body p-4 p-md-5">
                    <div class="row align-items-center g-4">
                        
                        
                        <div class="col-lg-8 text-center text-lg-start">
                            <div class="d-flex flex-column flex-lg-row align-items-center gap-3 mb-3">
                                <?php if($quiz->is_completed): ?>
                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px;">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 fw-bold">MASTERED</span>
                                <?php else: ?>
                                    <div class="bg-light text-primary border rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="fas fa-play"></i>
                                    </div>
                                    <span class="badge bg-light text-muted border rounded-pill px-3 py-1 fw-bold">UNFINISHED</span>
                                <?php endif; ?>
                            </div>

                            <h2 class="fw-black text-dark mb-2"><?php echo e($quiz->title); ?></h2>
                            <p class="text-muted fs-5 mb-0"><?php echo e($quiz->description ?: 'Begin this challenge to test your proficiency.'); ?></p>
                        </div>

                        
                        <div class="col-lg-4">
                            <?php if($quiz->is_completed): ?>
                                <div class="bg-light rounded-4 p-4 text-center mb-3 border">
                                    <small class="text-uppercase text-muted fw-black d-block mb-1">Score</small>
                                    <h2 class="display-5 fw-black text-success mb-0"><?php echo e($quiz->my_score); ?>%</h2>
                                </div>
                                <a href="<?php echo e(route('student.quizzes.take', $quiz->id)); ?>" class="btn btn-outline-dark btn-lg w-100 rounded-pill fw-bold transition-all">
                                    RETAKE
                                </a>
                            <?php else: ?>
                                <a href="<?php echo e(route('student.quizzes.take', $quiz->id)); ?>" class="btn btn-primary btn-lg w-100 rounded-pill fw-black py-3 shadow transition-all scale-hover">
                                    START QUIZ <i class="fas fa-chevron-right ms-2"></i>
                                </a>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="text-center py-5 bg-white rounded-5 shadow-sm border">
                <i class="fas fa-folder-open fa-4x text-muted opacity-25 mb-3"></i>
                <h3 class="fw-bold text-muted">No Quizzes Available</h3>
                <p class="text-muted">Check back later or try a different topic.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    /* Typography & Weight */
    .fw-black { font-weight: 900; }
    .tracking-widest { letter-spacing: 0.15em; }

    /* Custom Transitions */
    .transition-all { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    
    /* Perfect Fit Hover Effects */
    .hover-lift:hover { 
        transform: translateY(-5px); 
        box-shadow: 0 1.5rem 4rem rgba(0,0,0,0.12) !important; 
    }

    .scale-hover:hover {
        transform: scale(1.02);
    }

    .hover-translate-x:hover {
        transform: translateX(-5px);
    }

    /* Animation */
    .animate-fade-in {
        animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('users.students', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\islamicWebsiteEducation_FYP_2023657278\resources\views/users/quizzes/level4_list.blade.php ENDPATH**/ ?>