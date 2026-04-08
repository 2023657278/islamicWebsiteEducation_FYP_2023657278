

<?php $__env->startSection('content'); ?>

<div class="card">
    <div class="card-header bg-primary text-white position-relative">
        <h5 class="mb-0"><i class="fas chalkboard-teacher mr-2"></i>Teachers (MRSM Terendak)</h5>
        <a href="<?php echo e(route('teachers.create')); ?>" class="btn btn-maroon btn-sm position-absolute top-right-btn">
            <i class="fas fa-plus"></i> Add Teacher
        </a>
    </div>

    <div class="card-body bg-light border-bottom">
        <form action="<?php echo e(route('teachers.index')); ?>" method="GET" class="row g-2">
            <div class="col-md-10">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Search by Teacher Name, Email, or Phone..." 
                           value="<?php echo e(request('search')); ?>">
                </div>
            </div>
            <div class="col-md-2 d-flex">
                <button type="submit" class="btn btn-primary btn-sm w-100 me-1">Filter</button>
                <a href="<?php echo e(route('teachers.index')); ?>" class="btn btn-secondary btn-sm"><i class="fas fa-undo"></i></a>
            </div>
        </form>
    </div>

    <?php if($message = Session::get('success')): ?>
        <div class="alert alert-success m-3 py-2 small">
            <i class="fas fa-check-circle me-1"></i> <?php echo e($message); ?>

        </div>
    <?php endif; ?>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-3" style="width: 80px;">ID</th>
                        <th>Teacher Name</th>
                        <th>Email Address</th>
                        <th>Phone Number</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="ps-3 fw-bold text-dark"><?php echo e($t->id); ?></td>
                        <td>
                            <div class="fw-bold"><?php echo e($t->name); ?></div>
                            
                        </td>
                        <td><?php echo e($t->email); ?></td>
                        <td><?php echo e($t->phone_number ?? 'Not Provided'); ?></td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="<?php echo e(route('teachers.show', $t->id)); ?>" class="btn btn-sm btn-info text-white shadow-sm" title="Show Progress">
                                    <i class="fas fa-chart-line"></i>
                                </a>
                                <a href="<?php echo e(route('teachers.edit', $t->id)); ?>" class="btn btn-sm btn-warning text-white shadow-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="<?php echo e(route('teachers.destroy', $t->id)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-danger shadow-sm" onclick="return confirm('Delete teacher record?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No teachers found matching your search criteria.</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white py-2">
        <small class="text-muted">Currently showing <strong><?php echo e($teachers->count()); ?></strong> teaching staff members.</small>
    </div>
</div>

<style>
    /* Existing Styles */
.card { border-radius: 8px; border: 1px solid #000; overflow: hidden; }
.btn-maroon { background-color: #800000; border-color: #800000; color: white; }
.btn-maroon:hover { background-color: #660000; color: white; }
.top-right-btn { right: 15px; top: 50%; transform: translateY(-50%); }

/* Responsive adjustments for the filter bar */
@media (max-width: 768px) {
    .row.g-2 > div { margin-bottom: 10px; }
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.adminhome', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\islamicWebsiteEducation_FYP_2023657278\resources\views/teachers/table.blade.php ENDPATH**/ ?>