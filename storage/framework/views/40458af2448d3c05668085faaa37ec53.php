

<?php $__env->startSection('content'); ?>
<?php 
    $mode = request('mode', 'solo'); 
    $isPvp = ($mode === 'pvp');
?>

<div class="container-fluid p-0 text-start">
    
    <div class="mb-5">
        <a href="<?php echo e(route('student.quizzes.select_mode', $subject->id)); ?>" class="text-muted text-decoration-none">
            <i class="fas fa-arrow-left me-1"></i> Back to Mode Selection
        </a>
        <h2 class="fw-bold mt-2">
            <?php echo e($subject->subject_name); ?> 
            <span class="text-<?php echo e($isPvp ? 'warning' : 'primary'); ?> fw-light">
                / <?php echo e($isPvp ? 'PVP Battle Royale' : 'Solo Skill Training'); ?>

            </span>
        </h2>
        <p class="text-muted">
            <?php echo e($isPvp ? 'Challenge warriors in a randomized arena battle.' : 'Master each level to unlock the next challenge.'); ?>

        </p>
    </div>

    
    <?php if($isPvp): ?>
    <div class="d-flex justify-content-center mb-4">
        <div class="form-check form-switch p-3 bg-light rounded-pill shadow-sm border">
            <input class="form-check-input ms-0 me-2" type="checkbox" id="isPublic" checked>
            <label class="form-check-label fw-bold text-muted mb-0" for="isPublic">
                <i class="fas fa-globe-asia me-1"></i> Make Mission Public
            </label>
        </div>
    </div>
    <?php endif; ?>

    <div class="row g-4">
        <?php $__currentLoopData = ['Easy', 'Medium', 'Hard']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php 
                // Unlock all for PvP, keep level-locks for Solo
                $isAllowed = $isPvp ? true : in_array($level, $allowed); 
                $color = $level == 'Easy' ? 'success' : ($level == 'Medium' ? 'warning' : 'danger');
                $levelStats = $stats[$level] ?? ['done' => 0, 'total' => 0, 'avg' => 0];
                $pvpPoints = ['Easy' => 10, 'Medium' => 30, 'Hard' => 100];
            ?>
            
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden <?php echo e(!$isAllowed ? 'bg-light opacity-75' : ''); ?>">
                    <div style="height: 6px; background-color: var(--bs-<?php echo e($color); ?>);"></div>
                    
                    <div class="card-body p-4 text-center d-flex flex-column">
                        <div class="mb-3">
                            <div class="bg-<?php echo e($color); ?> bg-opacity-10 text-<?php echo e($color); ?> rounded-circle d-inline-flex p-3">
                                <i class="fas <?php echo e($isPvp ? 'fa-fire-alt' : ($isAllowed ? 'fa-medal' : 'fa-lock')); ?> fa-2x"></i>
                            </div>
                        </div>

                        <h3 class="fw-bold text-dark mb-1"><?php echo e($level); ?></h3>
                        
                        <?php if($isPvp): ?>
                            <div class="mt-3 mb-4">
                                <span class="badge bg-dark text-warning rounded-pill px-3 py-2">
                                    <i class="fas fa-trophy me-1"></i> +<?php echo e($pvpPoints[$level]); ?> Ranking Points
                                </span>
                                <p class="text-muted small mt-2">10 Random Questions</p>
                            </div>
                        <?php else: ?>
                            <div class="mt-3 mb-4">
                                <div class="progress" style="height: 10px; border-radius: 5px;">
                                    <?php $percent = ($levelStats['total'] > 0) ? ($levelStats['done'] / $levelStats['total']) * 100 : 0; ?>
                                    <div class="progress-bar bg-<?php echo e($color); ?>" style="width: <?php echo e($percent); ?>%"></div>
                                </div>
                                <small class="text-muted d-block mt-2"><?php echo e($levelStats['done']); ?>/<?php echo e($levelStats['total']); ?> Done</small>
                            </div>
                        <?php endif; ?>

                        <div class="mt-auto">
                            <?php if($isAllowed): ?>
                                
                                <?php if($isPvp): ?>
                                    
                                    <button type="button" onclick="startPvp('<?php echo e($subject->id); ?>', '<?php echo e($level); ?>')" 
                                            class="btn btn-<?php echo e($color); ?> w-100 rounded-pill fw-bold py-2 shadow-sm">
                                        START <?php echo e(strtoupper($level)); ?> BATTLE
                                    </button>
                                <?php else: ?>
                                    
                                    <a href="<?php echo e(route('student.quizzes.topics_diff', [$subject->id, $level])); ?>" 
                                       class="btn btn-<?php echo e($color); ?> w-100 rounded-pill fw-bold py-2 shadow-sm">
                                        Enter Level
                                    </a>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="p-3 bg-white border rounded-4 small text-muted">
                                    <i class="fas fa-lock me-1"></i> Unlock in Solo first.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>


<script>
    function startPvp(subjectId, level) {
        // Get the current state of the toggle
        const isPublic = document.getElementById('isPublic').checked ? 1 : 0;
        
        // Use backticks (`) for the URL template string
        // This builds the URL and adds the public/private flag as a query parameter
        window.location.href = `/student/quizzes/create_pvp/${subjectId}/${level}?is_public=${isPublic}`;
    }
</script>

<style>
    .card { transition: transform 0.3s; }
    .card:hover { transform: translateY(-5px); }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('users.students', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\islamicWebsiteEducation_FYP_2023657278\resources\views/users/quizzes/level2_difficulties.blade.php ENDPATH**/ ?>