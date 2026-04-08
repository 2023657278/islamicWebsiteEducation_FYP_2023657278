

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4 px-5"> 
    
    
    <div class="mb-5 text-start">
        <h1 class="fw-bold text-dark mb-1" style="font-size: 2.5rem;">
            <i class="fas fa-book-reader me-2 text-danger"></i>Digital Library
        </h1>
        <p class="text-muted fs-5">Browse and read your official textbooks</p>
    </div>

    
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php $__empty_1 = true; $__currentLoopData = $groupedTextbooks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subjectName => $books): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php $__currentLoopData = $books; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white book-card transition-all">
                    <div class="row g-0 h-100">
                        
                        <div class="col-4 d-flex align-items-center justify-content-center bg-light border-end py-5">
                            <?php 
                                $colors = ['#ef4444', '#f59e0b', '#10b981', '#3b82f6', '#8b5cf6'];
                                $color = $colors[($book->subject_id ?? 1) % 5];
                            ?>
                            <div class="icon-wrapper">
                                <i class="fas fa-book fa-4x" style="color: <?php echo e($color); ?>;"></i> 
                            </div>
                        </div>
                        
                        
                        <div class="col-8 text-start">
                            <div class="card-body h-100 d-flex flex-column p-4"> 
                                <h5 class="fw-bold text-dark mb-1 text-truncate" title="<?php echo e($book->title); ?>">
                                    <?php echo e($book->title); ?>

                                </h5>
                                <p class="mb-3">
                                    <span class="badge bg-light text-danger border px-2 py-1"><?php echo e($subjectName); ?></span>
                                </p>

                                
                                <div class="mb-4 mt-auto">
                                    <div class="progress mb-2" style="height: 6px; border-radius: 10px; background-color: #eee;">
                                        <div class="progress-bar bg-danger" role="progressbar" 
                                             style="width: <?php echo e($book->progress_percent ?? 0); ?>%; border-radius: 10px;"></div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted fw-medium">Reading Progress</small>
                                        <small class="text-danger fw-bold"><?php echo e($book->progress_percent ?? 0); ?>%</small>
                                    </div>
                                </div>

                                <a href="<?php echo e(route('student.textbooks.read', $book->id)); ?>" 
                                   class="btn btn-outline-danger rounded-pill w-100 fw-bold py-2 shadow-sm transition">
                                    <?php echo e(($book->progress_percent ?? 0) > 0 ? 'Continue Reading' : 'Read Now'); ?>

                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-12 text-center py-5">
                <i class="fas fa-book-open fa-4x text-muted opacity-25 mb-3"></i>
                <h5 class="text-muted">No textbooks currently available.</h5>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    /* ✅ Card Sizing to cover more area */
    .book-card {
        border: 1px solid rgba(0,0,0,0.05) !important;
        min-height: 200px; /* ✅ Increased height for a "bigger" feel */
        background-color: #fff;
    }
    
    .book-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 25px rgba(0,0,0,0.1) !important;
    }
    
    .icon-wrapper {
        transition: transform 0.3s ease;
    }
    
    .book-card:hover .icon-wrapper {
        transform: scale(1.15) rotate(-5deg);
    }
    
    .transition-all {
        transition: all 0.3s ease-in-out;
    }

    .text-start {
        text-align: left !important;
    }

    /* ✅ Grid spacing adjustment */
    .g-4 {
        --bs-gutter-x: 2rem;
        --bs-gutter-y: 2rem;
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('users.students', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\islamicWebsiteEducation_FYP_2023657278\resources\views/users/resources/textbooks.blade.php ENDPATH**/ ?>