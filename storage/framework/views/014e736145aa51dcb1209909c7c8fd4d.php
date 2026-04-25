<?php $__env->startSection('content'); ?>
<div class="container my-5">
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-lg-4 col-md-6">
            <div class="card shadow-lg p-4 border-0">
                <div class="card-title text-center mb-4">
                    <h2 class="fw-bold text-primary"><?php echo e(__('Welcome')); ?></h2>
                    <img src="<?php echo e(asset('image/logoMRSM.png')); ?>" alt="My Picture" width="100">
                    <hr>
                    <p class="text-muted">Sign in to continue to your account.</p>
                </div>

                <div class="card-body">
                    <form method="POST" action="<?php echo e(route('login')); ?>">
                        <?php echo csrf_field(); ?>

                        <div class="form-floating mb-3">
                            <input id="email" type="text" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                name="email" value="<?php echo e(old('email')); ?>" required autocomplete="email" autofocus 
                                placeholder="Email or No. Maktab">
                            <label for="email"><?php echo e(__('Email Address or No. Maktab')); ?></label>
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback" role="alert">
                                    <strong><?php echo e($message); ?></strong>
                                </span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="form-floating mb-3">
                            <input id="password" type="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                name="password" required autocomplete="current-password"
                                placeholder="Password">
                            <label for="password"><?php echo e(__('Password')); ?></label>
                            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback" role="alert">
                                    <strong><?php echo e($message); ?></strong>
                                </span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" <?php echo e(old('remember') ? 'checked' : ''); ?>>
                                <label class="form-check-label text-muted" for="remember">
                                    <?php echo e(__('Remember Me')); ?>

                                </label>
                            </div>
                            <?php if(Route::has('password.request')): ?>
                                <a class="text-decoration-none small" href="<?php echo e(route('password.request')); ?>">
                                    <?php echo e(__('Forgot Your Password?')); ?>

                                </a>
                            <?php endif; ?>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <?php echo e(__('Login')); ?>

                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <p class="text-center mt-3 text-white">
    Don't have an account? <a href="<?php echo e(route('register') ?? '#'); ?>" class="ttext-decoration-none text-white font-weight-bold hover:text-light">Sign Up</a>
</p>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\islamicWebsiteEducation_FYP_2023657278\resources\views/auth/login.blade.php ENDPATH**/ ?>