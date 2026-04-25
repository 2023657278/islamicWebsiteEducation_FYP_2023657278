

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 font-weight-bold text-warning"><i class="fas fa-edit mr-2"></i>Edit Assessment</h5>
                    <a href="<?php echo e(route('quizzes.index')); ?>" class="btn btn-sm btn-light border text-muted">Cancel</a>
                </div>
                <div class="card-body p-4">
                    <form action="<?php echo e(route('quizzes.update', $quiz->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-muted small text-uppercase">Quiz Title</label>
                            <input type="text" name="title" class="form-control form-control-lg bg-light border-0" value="<?php echo e($quiz->title); ?>" required>
                        </div>
                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-muted small text-uppercase">Instructions</label>
                            <textarea name="description" class="form-control bg-light border-0" rows="3"><?php echo e($quiz->description); ?></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label class="font-weight-bold text-muted small text-uppercase">Subject</label>
                                <select name="subject_id" class="form-control bg-light border-0" required>
                                    <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <option value="<?php echo e($subject->id); ?>" <?php echo e($quiz->subject_id == $subject->id ? 'selected' : ''); ?>><?php echo e($subject->subject_name); ?></option> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <label class="font-weight-bold text-muted small text-uppercase">Topic / Chapter</label>
                                <input type="text" name="topic" class="form-control bg-light border-0" value="<?php echo e($quiz->topic); ?>" required>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label class="font-weight-bold text-muted small text-uppercase">Difficulty Level</label>
                                <select name="difficulty" class="form-control bg-light border-0" required>
                                    <option value="Easy" <?php echo e($quiz->difficulty == 'Easy' ? 'selected' : ''); ?>>Easy</option>
                                    <option value="Medium" <?php echo e($quiz->difficulty == 'Medium' ? 'selected' : ''); ?>>Medium</option>
                                    <option value="Hard" <?php echo e($quiz->difficulty == 'Hard' ? 'selected' : ''); ?>>Hard</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label class="font-weight-bold text-muted small text-uppercase">Duration (Minutes)</label>
                                <input type="number" name="duration_minutes" class="form-control bg-light border-0" value="<?php echo e($quiz->duration_minutes); ?>" required>
                            </div>
                        </div>
                        <div class="pt-3 border-top mt-2 text-right"><button type="submit" class="btn btn-warning px-5 py-2 font-weight-bold shadow-sm" style="border-radius: 8px;"><i class="fas fa-save mr-2"></i> Update Quiz</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.adminhome', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\islamicWebsiteEducation_FYP_2023657278\resources\views/quizzes/edit.blade.php ENDPATH**/ ?>