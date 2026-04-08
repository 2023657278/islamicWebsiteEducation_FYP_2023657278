

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
    <table style="width: 100%;">
        <tr>
            <td style="text-align: left;">
                <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i> REGISTER NEW TEACHER</h5>
            </td>
            <td style="text-align: right;">
                <span class="badge bg-white text-primary fw-bold">New Account</span>
            </td>
        </tr>
    </table>
</div>

                <div class="card-body bg-light">
                    <form action="<?php echo e(route('teachers.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="small font-weight-bold text-dark">Full Name</label>
                                <input type="text" name="name" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       placeholder="Enter teacher's full name" value="<?php echo e(old('name')); ?>" required>
                                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger small"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="small font-weight-bold text-dark">Email Address</label>
                                <input type="email" name="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       placeholder="teacher@gmail.com" value="<?php echo e(old('email')); ?>" required>
                                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger small"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="small font-weight-bold text-dark">Phone Number</label>
                                <input type="text" name="phone_number" class="form-control" 
                                       placeholder="e.g. 0123456789" value="<?php echo e(old('phone_number')); ?>">
                            </div>
                        </div>

                        <div class="card border-primary mb-4 mt-2">
                            <div class="card-header bg-primary text-white py-2">
                                <span class="small fw-bold"><i class="fas fa-lock me-1"></i> Account Security</span>
                            </div>
                            <div class="card-body row py-3">
                                <div class="col-md-6">
                                    <label class="small font-weight-bold">Initial Password</label>
                                    <input type="password" name="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           placeholder="Minimum 6 characters" required>
                                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger small"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="col-md-6">
                                    <label class="small font-weight-bold">Confirm Password</label>
                                    <input type="password" name="password_confirmation" class="form-control" 
                                           placeholder="Repeat password" required>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mt-2">
                            <a href="<?php echo e(route('teachers.index')); ?>" class="btn btn-secondary shadow-sm">
                                <i class="fas fa-arrow-left me-1"></i> Back to List
                            </a>
                            <button type="submit" class="btn btn-maroon shadow-sm px-4">
                                <i class="fas fa-check-circle me-1"></i> Register Teacher
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* MRSM Terendak Theme Consistency */
.card { border-radius: 12px; border: 1px solid #000; overflow: hidden; }
.btn-maroon { background-color: #800000; border-color: #800000; color: white; }
.btn-maroon:hover { background-color: #660000; color: white; }
.border-primary { border: 1px solid #007bff !important; }
.form-control:focus { border-color: #800000; box-shadow: 0 0 0 0.2rem rgba(128, 0, 0, 0.1); }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.adminhome', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\islamicWebsiteEducation_FYP_2023657278\resources\views/teachers/create.blade.php ENDPATH**/ ?>