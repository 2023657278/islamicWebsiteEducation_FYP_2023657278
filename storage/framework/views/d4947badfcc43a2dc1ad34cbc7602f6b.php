

<?php $__env->startSection('content'); ?>

<div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white position-relative py-3">
        <h5 class="mb-0 fw-bold"><i class="fas fa-chart-bar me-2"></i>Student Quiz Results</h5>
        </div>

    <div class="card-body bg-light border-bottom">
        <form action="<?php echo e(route('results.index')); ?>" method="GET" class="row g-2">
            
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search_name" class="form-control border-start-0" 
                           placeholder="Search Student Name..." 
                           value="<?php echo e(request('search_name')); ?>">
                </div>
            </div>

            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-users text-muted"></i></span>
                    <select name="search_group" class="form-select border-start-0">
                        <option value="">All Groups</option>
                        <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> 
                                            <option value="<?php echo e($g->id); ?>">
                                                <?php echo e($g->group_name); ?> (<?php echo e($g->year->year ?? '-'); ?>)
                                            </option> 
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-book text-muted"></i></span>
                    <select name="search_subject" class="form-select border-start-0">
                        <option value="">All Subjects</option>
                        <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($s->id); ?>" <?php echo e(request('search_subject') == $s->id ? 'selected' : ''); ?>>
                                <?php echo e($s->subject_name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>

            <div class="col-md-2 d-flex">
                <button type="submit" class="btn btn-primary btn-sm w-100 me-1 fw-bold shadow-sm">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                <?php if(request('search_name') || request('search_group') || request('search_subject')): ?>
                <a href="<?php echo e(route('results.index')); ?>" class="btn btn-secondary btn-sm shadow-sm" title="Clear Filters">
                    <i class="fas fa-undo"></i>
                </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover table-striped">
    <thead class="table-dark">
        <tr>
            <th class="ps-4">#</th>
            <th>Student Name</th>
            <th>Group</th>
            <th>Quiz / Subject</th>
            <th class="text-center">Score</th>
            <th class="text-center">Trend</th> <th class="text-center">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attempt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
            <td class="ps-4 text-muted small"><?php echo e($loop->iteration + ($results->currentPage() - 1) * $results->perPage()); ?></td>
            
            <td><div class="fw-bold text-dark"><?php echo e($attempt->user->name ?? 'Unknown'); ?></div></td>
            
            <td><span class="badge bg-secondary"><?php echo e($attempt->user->group->group_name ?? '-'); ?></span></td>
            
            <td>
                <div class="fw-bold small"><?php echo e($attempt->quiz->title ?? 'Deleted'); ?></div>
                <small class="text-muted"><?php echo e($attempt->quiz->subject->subject_name ?? 'General'); ?></small>
            </td>
            
            <td class="text-center">
                <span class="badge <?php echo e(($attempt->score ?? 0) >= 50 ? 'bg-success' : 'bg-danger'); ?> rounded-pill px-3">
                    <?php echo e($attempt->score ?? 0); ?>%
                </span>
            </td>

            <td class="text-center">
                <?php if($attempt->trend == 'Excellent'): ?>
                    <span class="badge bg-success" title="Slope: <?php echo e($attempt->slope); ?>"><i class="fas fa-angle-double-up me-1"></i> Excellent</span>
                <?php elseif($attempt->trend == 'Improving'): ?>
                    <span class="badge bg-info text-dark" title="Slope: <?php echo e($attempt->slope); ?>"><i class="fas fa-angle-up me-1"></i> Improving</span>
                <?php elseif($attempt->trend == 'Stable'): ?>
                    <span class="badge bg-secondary" title="Slope: <?php echo e($attempt->slope); ?>"><i class="fas fa-minus me-1"></i> Stable</span>
                <?php elseif($attempt->trend == 'Warning'): ?>
                    <span class="badge bg-warning text-dark" title="Slope: <?php echo e($attempt->slope); ?>"><i class="fas fa-angle-down me-1"></i> Warning</span>
                <?php else: ?>
                    <span class="badge bg-danger" title="Slope: <?php echo e($attempt->slope); ?>"><i class="fas fa-angle-double-down me-1"></i> Critical</span>
                <?php endif; ?>
            </td>

            <td class="text-center">
                <form action="<?php echo e(route('results.destroy', $attempt->id)); ?>" method="POST" class="d-inline">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button class="btn btn-sm btn-danger shadow-sm" onclick="return confirm('Reset this score?')"><i class="fas fa-trash-alt"></i></button>
                </form>
            </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="7" class="text-center py-5">No results found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
        </div>
    </div>
    
    <div class="card-footer bg-white py-2 d-flex justify-content-between align-items-center">
        <small class="text-muted">Showing <strong><?php echo e($results->count()); ?></strong> results on this page.</small>
        <div><?php echo e($results->links()); ?></div>
    </div>
</div>

<style>
    /* Exact styles from your snippet to maintain consistency */
    .card { border-radius: 8px; border: 1px solid #dee2e6; overflow: hidden; }
    .table th { font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .row.g-2 > div { margin-bottom: 10px; }
    }
</style>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.adminhome', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\islamicWebsiteEducation_FYP_2023657278\resources\views/results/index.blade.php ENDPATH**/ ?>