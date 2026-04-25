

<?php $__env->startSection('content'); ?>
<div class="container text-center py-4" style="max-width: 800px;">
    
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="<?php echo e(route('student.flashcards.index')); ?>" class="btn btn-light rounded-pill px-4 fw-bold">
            <i class="fas fa-times me-2"></i> Exit
        </a>
        <div class="text-center">
            <h5 class="fw-bold mb-0"><?php echo e($subject->subject_name); ?></h5>
            <small class="text-muted">Preview Mode</small>
        </div>
        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
            <?php echo e($current); ?> / <?php echo e($total); ?>

        </span>
    </div>

    
    <div class="progress mb-5" style="height: 6px;">
        <div class="progress-bar bg-primary rounded-pill" role="progressbar" style="width: <?php echo e($progress); ?>%"></div>
    </div>

    
    <?php if($cards->count() > 0): ?>
        <?php $card = $cards->first(); ?>
        <div class="scene mx-auto" style="height: 400px; width: 100%;">
            <div class="flashcard" id="card" onclick="this.classList.toggle('is-flipped')">
                <div class="card-face card-front bg-white rounded-5 shadow-sm border d-flex flex-column align-items-center justify-content-center p-5">
                    <span class="text-uppercase text-muted fw-bold small mb-4">Question</span>
                    <h2 class="fw-bold text-dark lh-base"><?php echo e($card->question); ?></h2>
                    <div class="mt-auto text-muted small"><i class="fas fa-hand-pointer me-2"></i> Tap to flip</div>
                </div>
                <div class="card-face card-back bg-light rounded-5 shadow-sm border d-flex flex-column align-items-center justify-content-center p-5">
                    <span class="text-uppercase text-muted fw-bold small mb-4">Answer</span>
                    <h2 class="fw-bold text-primary lh-base"><?php echo e($card->answer); ?></h2>
                </div>
            </div>
        </div>

        
        <div class="d-flex justify-content-between align-items-center mt-5 px-5">
            <?php if($cards->onFirstPage()): ?>
                <button class="btn btn-light rounded-pill px-4 py-2" disabled>Prev</button>
            <?php else: ?>
                <a href="<?php echo e($cards->previousPageUrl()); ?>" class="btn btn-outline-primary rounded-pill px-4 py-2">Prev</a>
            <?php endif; ?>

            <span class="text-muted small">Tap card to see answer</span>

            <?php if($cards->hasMorePages()): ?>
                <a href="<?php echo e($cards->nextPageUrl()); ?>" class="btn btn-primary rounded-pill px-4 py-2">Next</a>
            <?php else: ?>
                <button class="btn btn-light rounded-pill px-4 py-2" disabled>End</button>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No cards found for this subject.</div>
    <?php endif; ?>
</div>

<style>
    .scene { perspective: 1000px; }
    .flashcard { width: 100%; height: 100%; transition: transform 0.6s; transform-style: preserve-3d; cursor: pointer; position: relative; }
    .flashcard.is-flipped { transform: rotateY(180deg); }
    .card-face { position: absolute; width: 100%; height: 100%; backface-visibility: hidden; -webkit-backface-visibility: hidden; }
    .card-back { transform: rotateY(180deg); }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('users.students', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\islamicWebsiteEducation_FYP_2023657278\resources\views/users/flashcards/manual.blade.php ENDPATH**/ ?>