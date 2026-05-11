

<?php $__env->startSection('content'); ?>
<?php 
    $mode = request('mode', 'solo'); 
    $isPvp = ($mode === 'pvp');
    $userPts = Auth::user()->pvp_points;
?>

<div class="container-fluid p-0 text-start">
    
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-end">
            <div>
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
            
            
            <div class="text-end">
                <div class="p-3 bg-white shadow-sm border rounded-4">
                    <small class="text-muted d-block fw-bold text-uppercase">Your Current Ranking</small>
                    <div class="d-flex align-items-center gap-2">
                        <span class="fs-4 fw-black text-primary"><?php echo e(number_format($userPts)); ?> <small class="fs-6">PTS</small></span>
                        <?php if($userPts >= 300): ?>
                            <span class="badge bg-warning text-dark rounded-pill px-3">GOLD RANK</span>
                        <?php elseif($userPts >= 100): ?>
                            <span class="badge bg-secondary text-white rounded-pill px-3">SILVER RANK</span>
                        <?php else: ?>
                            <span class="badge bg-bronze rounded-pill px-3" style="background: #CD7F32; color: white;">BRONZE RANK</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <?php if($isPvp): ?>
    <div class="d-flex justify-content-center mb-4">
        <div class="form-check form-switch p-3 bg-white rounded-pill shadow-sm border">
            <input class="form-check-input ms-0 me-2" type="checkbox" id="isPublic" checked style="cursor: pointer;">
            <label class="form-check-label fw-bold text-muted mb-0" for="isPublic" style="cursor: pointer;">
                <i class="fas fa-globe-asia me-1 text-primary"></i> Make Mission Public
            </label>
        </div>
    </div>
    <?php endif; ?>

    <div class="row g-4">
        <?php $__currentLoopData = ['Easy', 'Medium', 'Hard']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php 
                // 🔒 PROGRESSION GATING LOGIC
                $ptsNeeded = ($level == 'Medium') ? 100 : (($level == 'Hard') ? 300 : 0);
                
                // If PvP: Check Points. If Solo: Check progress array from controller.
                if($isPvp) {
                    $isAllowed = $userPts >= $ptsNeeded;
                } else {
                    $isAllowed = in_array($level, $allowed);
                }

                $color = $level == 'Easy' ? 'success' : ($level == 'Medium' ? 'warning' : 'danger');
                $levelStats = $stats[$level] ?? ['done' => 0, 'total' => 0, 'avg' => 0];
                $maxWinPts = ['Easy' => 15, 'Medium' => 30, 'Hard' => 70];
            ?>
            
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden <?php echo e(!$isAllowed ? 'bg-light opacity-75' : ''); ?>" style="transition: 0.3s;">
                    <div style="height: 6px; background-color: var(--bs-<?php echo e($color); ?>);"></div>
                    
                    <div class="card-body p-4 text-center d-flex flex-column">
                        <div class="mb-3">
                            <div class="bg-<?php echo e($color); ?> bg-opacity-10 text-<?php echo e($color); ?> rounded-circle d-inline-flex p-3">
                                <?php if(!$isAllowed): ?>
                                    <i class="fas fa-lock fa-2x"></i>
                                <?php else: ?>
                                    <i class="fas <?php echo e($isPvp ? 'fa-fire-alt' : 'fa-medal'); ?> fa-2x"></i>
                                <?php endif; ?>
                            </div>
                        </div>

                        <h3 class="fw-bold text-dark mb-1"><?php echo e($level); ?></h3>
                        
                        <?php if($isPvp): ?>
                            <div class="mt-3 mb-4">
                                <?php if(!$isAllowed): ?>
                                    <span class="text-danger fw-bold small">
                                        <i class="fas fa-exclamation-circle me-1"></i> Requires <?php echo e($ptsNeeded); ?> PTS to unlock
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-dark text-warning rounded-pill px-3 py-2">
                                        <i class="fas fa-trophy me-1"></i> Up to +<?php echo e($maxWinPts[$level]); ?> Ranking Points
                                    </span>
                                <?php endif; ?>
                                <p class="text-muted small mt-2">10 Randomized Arena Questions</p>
                            </div>
                        <?php else: ?>
                            <div class="mt-3 mb-4">
                                <div class="progress" style="height: 10px; border-radius: 5px; background: rgba(0,0,0,0.05);">
                                    <?php $percent = ($levelStats['total'] > 0) ? ($levelStats['done'] / $levelStats['total']) * 100 : 0; ?>
                                    <div class="progress-bar bg-<?php echo e($color); ?>" style="width: <?php echo e($percent); ?>%"></div>
                                </div>
                                <small class="text-muted d-block mt-2"><?php echo e($levelStats['done']); ?>/<?php echo e($levelStats['total']); ?> Missions Completed</small>
                            </div>
                        <?php endif; ?>

                        <div class="mt-auto">
                            <?php if($isAllowed): ?>
                                <?php if($isPvp): ?>
                                    <button type="button" onclick="startPvp('<?php echo e($subject->id); ?>', '<?php echo e($level); ?>')" 
                                            class="btn btn-<?php echo e($color); ?> w-100 rounded-pill fw-black py-2 shadow-sm border-0">
                                        DEPLOY <?php echo e(strtoupper($level)); ?> MISSION
                                    </button>
                                <?php else: ?>
                                    <a href="<?php echo e(route('student.quizzes.topics_diff', [$subject->id, $level])); ?>" 
                                       class="btn btn-<?php echo e($color); ?> w-100 rounded-pill fw-black py-2 shadow-sm border-0">
                                        ENTER LEVEL
                                    </a>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="p-2 bg-white border border-dashed rounded-pill small text-muted fw-bold">
                                    <i class="fas fa-lock me-1"></i> CONTENT LOCKED
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
        const isPublic = document.getElementById('isPublic').checked ? 1 : 0;
        // Show a confirmation before creating a mission
        if(confirm(`Are you ready to deploy a ${level} mission?`)) {
            window.location.href = `/student/quizzes/create_pvp/${subjectId}/${level}?is_public=${isPublic}`;
        }
    }
</script>

<style>
    .card:hover { transform: translateY(-8px); box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important; }
    .fw-black { font-weight: 900; }
    .border-dashed { border-style: dashed !important; border-width: 2px !important; }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('users.students', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\islamicWebsiteEducation_FYP_2023657278\resources\views/users/quizzes/level2_difficulties.blade.php ENDPATH**/ ?>