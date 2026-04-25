

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-dark">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center w-100">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Group</h5>
                    <span class="badge bg-white text-primary fw-bold">GROUP ID: #<?php echo e($group->id); ?></span>
                </div>
                <div class="card-body bg-light">
                    <form action="<?php echo e(route('groups.update', $group->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <div class="mb-3">
                            <label class="small fw-bold text-dark">Group Name</label>
                            <input type="text" name="group_name" class="form-control <?php $__errorArgs = ['group_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   value="<?php echo e(old('group_name', $group->group_name)); ?>" required>
                            <?php $__errorArgs = ['group_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger small"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="mb-4">
                            <label class="small fw-bold text-dark">Academic Year</label>
                            <input type="text" name="year" list="yearSuggestions" class="form-control <?php $__errorArgs = ['year'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   value="<?php echo e(old('year', $group->year->year ?? '')); ?>" required autocomplete="off">
                            
                            <datalist id="yearSuggestions">
                                <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yearRecord): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($yearRecord->year); ?>">
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </datalist>
                            <?php $__errorArgs = ['year'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger small"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="<?php echo e(route('groups.index')); ?>" class="btn btn-secondary shadow-sm">Cancel</a>
                            <button type="submit" class="btn btn-maroon shadow-sm px-4">Update Group</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-maroon { background-color: #800000; color: white; border: none; }
    .btn-maroon:hover { background-color: #600000; color: white; }
    .card { border-radius: 12px; }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.adminhome', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\islamicWebsiteEducation_FYP_2023657278\resources\views/groups/edit.blade.php ENDPATH**/ ?>