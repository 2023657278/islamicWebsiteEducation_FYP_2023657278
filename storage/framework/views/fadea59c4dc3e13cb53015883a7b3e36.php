

<?php $__env->startSection('content'); ?>
<style>
    .mode-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer; border: 2px solid transparent; }
    .mode-card:hover { transform: translateY(-10px); border-color: #3b82f6; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }
    .pvp-gradient { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; }
</style>

<div class="container py-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-dark">Select Your Mission Path</h2>
        <p class="text-muted">Subject: <span class="badge bg-primary px-3"><?php echo e($subject->subject_name); ?></span></p>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-md-5">
            <a href="<?php echo e(route('student.quizzes.difficulties', $subject->id)); ?>?mode=solo" class="text-decoration-none">
                <div class="card h-100 border-0 rounded-4 p-5 mode-card bg-white shadow-sm text-center">
                    <div class="fs-1 mb-3 text-primary"><i class="fas fa-user-ninja"></i></div>
                    <h3 class="fw-bold text-dark">SOLO MISSION</h3>
                    <p class="text-muted">Battle the Guardian alone. Practice by Topic.</p>
                    <span class="btn btn-outline-primary rounded-pill px-4 mt-3">Start Training</span>
                </div>
            </a>
        </div>

        <div class="col-md-5">
            <div class="card h-100 border-0 rounded-4 p-5 mode-card pvp-gradient shadow-lg text-center" 
                 data-bs-toggle="modal" data-bs-target="#pvpActionModal">
                <div class="fs-1 mb-3 text-warning"><i class="fas fa-fire-alt"></i></div>
                <h3 class="fw-bold">PVP ARENA</h3>
                <p class="text-white-50">Join up to 20 warriors. Random questions by Difficulty.</p>
                <span class="btn btn-warning fw-bold rounded-pill px-4 mt-3">Enter Arena</span>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="pvpActionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg" style="background: #0f172a;">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title text-white fw-bold">Battle Command Center</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <a href="<?php echo e(route('student.quizzes.browse', $subject->id)); ?>" class="btn btn-outline-info w-100 rounded-pill mb-3 fw-bold">
                    <i class="fas fa-radar me-2"></i>FIND PUBLIC LOBBY
                </a>

                <form action="<?php echo e(route('student.quizzes.join')); ?>" method="POST" class="mb-4">
                    <?php echo csrf_field(); ?>
                    <div class="input-group">
                        <input type="text" name="room_code" class="form-control bg-dark border-0 text-white fw-bold" placeholder="ROOM CODE">
                        <button class="btn btn-warning fw-bold" type="submit">JOIN</button>
                    </div>
                </form>

                <div class="text-center mb-3"><span class="badge bg-secondary rounded-pill">OR</span></div>

                <a href="<?php echo e(route('student.quizzes.difficulties', $subject->id)); ?>?mode=pvp" class="btn btn-primary w-100 rounded-pill fw-bold">
                    <i class="fas fa-plus-circle me-2"></i>CREATE NEW LOBBY
                </a>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('users.students', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\islamicWebsiteEducation_FYP_2023657278\resources\views/users/quizzes/mode_selection.blade.php ENDPATH**/ ?>