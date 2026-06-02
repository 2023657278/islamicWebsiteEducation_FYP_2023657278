


<?php $__env->startSection('content'); ?>
    <div class="container-fluid my-3">
        
        
        <?php if(auth()->user()->role === 'admin'): ?>
            <div class="mb-4 p-3 rounded d-flex align-items-center justify-content-between text-xs font-weight-bold shadow-sm" style="background: #121214; border: 1px solid #27272a; color: #94a3b8;">
                <span><i class="fas fa-shield-alt text-primary mr-2"></i> SECURE MASTER SUBSYSTEM LAYER ACTIVE</span>
                <span class="badge bg-dark border text-primary border-primary px-3 py-1 rounded-pill">MODE: CRUD AUTHORIZED</span>
            </div>
        <?php else: ?>
            
            <div class="mb-4 alert alert-info border-0 shadow-sm text-sm p-3 d-flex align-items-center">
                <i class="fas fa-info-circle fa-lg mr-2 text-info"></i>
                <div>
                    <strong>Staff Directory View Mode:</strong> You can view profile rosters and historical analytics, but structural modification commands remain exclusive to System Administrators.
                </div>
            </div>
        <?php endif; ?>

        
        <?php echo $__env->make('teachers.table', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make(auth()->user()->role === 'admin' ? 'adminreal.master' : 'admin.adminhome', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\islamicWebsiteEducation_FYP_2023657278\resources\views/teachers/index.blade.php ENDPATH**/ ?>