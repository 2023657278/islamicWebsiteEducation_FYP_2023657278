

<?php $__env->startSection('content'); ?>
<style>
    :root { --deep-maroon: #4a0000; --accent-gold: #c5a059; --glass-white: rgba(255, 255, 255, 0.96); }
    
    .main-wrapper { 
        background-color: var(--deep-maroon); min-height: 100vh; padding: 4rem 1rem;
        background-image: radial-gradient(circle at 10% 20%, rgba(128, 0, 0, 0.5) 0%, rgba(0, 0, 0, 0.2) 90%);
    }

    .glass-card { 
        background: var(--glass-white); border-radius: 30px; 
        box-shadow: 0 40px 80px rgba(55, 0, 0, 0.5); padding: 50px;
    }

    .form-label-elegant { font-size: 1.00rem; color: #64748b; text-transform: uppercase; letter-spacing: 2px; font-weight: 900; margin-bottom: 10px; display: block; }
    
    .form-control-elegant { 
        border: 2px solid #f1f5f9; 
        border-radius: 15px; 
        padding: 14px; 
        background: #fdfdfd; 
        transition: 0.4s; 
        font-weight: 500; 
    }
    
    .form-control-elegant:focus { border-color: var(--deep-maroon); box-shadow: 0 0 0 5px rgba(107, 0, 0, 0.05); }

    /* --- WHITE CLOCK ICON FIX --- */
    .form-control-elegant::-webkit-calendar-picker-indicator { 
        filter: invert(1); 
        cursor: pointer;
    }

    .btn-submit { background: linear-gradient(135deg, #800000 0%, #300000 100%); color: white; border-radius: 15px; padding: 15px 40px; font-weight: 700; border: none; }
</style>

<div class="main-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="glass-card">
                    <h2 class="text-center fw-bold mb-5" style="color: var(--deep-maroon);">
                        <i class="fas fa-calendar-plus me-2 opacity-50"></i>New <span style="border-bottom: 4px solid var(--accent-gold);">Assignment</span>
                    </h2>

                    <form action="<?php echo e(route('timetables.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="row g-4">
                            <div class="col-md-12">
                                <label class="form-label-elegant">Faculty Lead / Teacher</label>
                                <select class="form-select form-control-elegant" name="teacher_id" required>
                                    <option value="">-- Assign Instructor --</option>
                                    <?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teacher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($teacher->id); ?>"><?php echo e($teacher->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label-elegant">Class Placement & Academic Session</label>
                                <select class="form-select form-control-elegant" name="group_id" required>
                                    <option value="">-- Select Class (Year) --</option>
                                    <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($group->id); ?>">
                                            <?php echo e($group->group_name); ?> — Session <?php echo e($group->year->year ?? 'N/A'); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-elegant">Academic Subject</label>
                                <select class="form-select form-control-elegant" name="subject_id" required>
                                    <option value="">-- Choose Subject --</option>
                                    <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($subject->id); ?>"><?php echo e($subject->subject_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-elegant">Scheduled Day</label>
                                <select class="form-select form-control-elegant" name="day_id" required>
                                    <option value="">-- Choose Day --</option>
                                    <?php $__currentLoopData = $days; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($day->id); ?>"><?php echo e($day->day_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-elegant">Time Start (8am - 6pm)</label>
                                <input type="time" name="time_from" class="form-control form-control-elegant" min="08:00" max="18:00" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-elegant">Time End (8am - 6pm)</label>
                                <input type="time" name="time_to" class="form-control form-control-elegant" min="08:00" max="18:00" required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-5">
                            <a href="<?php echo e(route('timetables.index')); ?>" class="text-dark fw-bold text-decoration-none">Back to List</a>
                            <button type="submit" class="btn btn-submit shadow">CONFIRM ASSIGNMENT</button>
                        </div>

                        <?php if($errors->any()): ?>
                            <div class="alert alert-danger border-0 shadow-sm rounded-4 mt-4" style="background-color: #fff5f5; color: #c53030;">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-exclamation-triangle me-3 fa-2x"></i>
                                    <div>
                                        <strong class="d-block">Schedule Conflict Detected!</strong>
                                        <ul class="mb-0 small fw-bold list-unstyled">
                                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li><?php echo e($error); ?></li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.adminhome', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\islamicWebsiteEducation_FYP_2023657278\resources\views/timetables/create.blade.php ENDPATH**/ ?>