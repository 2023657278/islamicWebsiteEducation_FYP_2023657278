

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 font-weight-bold text-primary"><i class="fas fa-plus-circle mr-2"></i>New Assessment</h5>
                </div>
                <div class="card-body p-4">
                    <form action="<?php echo e(route('quizzes.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-muted small text-uppercase">Quiz Title</label>
                            <input type="text" name="title" class="form-control form-control-lg bg-light border-0" required placeholder="e.g. Mid-Term Exam">
                        </div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-muted small text-uppercase">Instructions (Optional)</label>
                            <textarea name="description" class="form-control bg-light border-0" rows="3" placeholder="Answer all questions correctly..."></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label class="font-weight-bold text-muted small text-uppercase">Subject</label>
                                <select name="subject_id" class="form-control bg-light border-0" required>
                                    <option value="" disabled selected>Select Subject</option>
                                    <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <option value="<?php echo e($subject->id); ?>"><?php echo e($subject->subject_name); ?></option> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <label class="font-weight-bold text-muted small text-uppercase">Topic / Chapter</label>
                                <input type="text" name="topic" class="form-control bg-light border-0" placeholder="e.g. Akhlak" required>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label class="font-weight-bold text-muted small text-uppercase">Difficulty</label>
                                <select name="difficulty" class="form-control bg-light border-0" required>
                                    <option value="Easy">Easy</option>
                                    <option value="Medium" selected>Medium</option>
                                    <option value="Hard">Hard</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label class="font-weight-bold text-muted small text-uppercase">Duration (Minutes)</label>
                                <input type="number" name="duration_minutes" class="form-control bg-light border-0" value="30" min="1" required>
                            </div>
                        </div>

                        <div class="pt-3 border-top mt-2 text-right">
                            <button type="submit" class="btn btn-primary px-5 py-2 font-weight-bold shadow-sm" style="border-radius: 8px;">Next: Add Questions <i class="fas fa-arrow-right ml-2"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.adminhome', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\islamicWebsiteEducation_FYP_2023657278\resources\views/quizzes/create.blade.php ENDPATH**/ ?>