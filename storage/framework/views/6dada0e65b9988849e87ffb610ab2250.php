
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="p-4 bg-white shadow-sm rounded-lg border-left border-danger mb-4">
        <h4 class="font-weight-bold"><i class="fab fa-youtube text-danger mr-2"></i> Select Videos with #MRSM</h4>
        
<strong><?php echo e(\App\Models\Group::find($group_id)?->group_name ?? 'General Resource'); ?></strong>
    </div>

    <form action="<?php echo e(route('resources.sync.store_selected')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="group_id" value="<?php echo e($group_id); ?>">
        <input type="hidden" name="subject_id" value="<?php echo e($subject_id); ?>">

        <div class="row">
            <?php $__empty_1 = true; $__currentLoopData = $youtubeVideos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $video): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="col-md-3 mb-4">
                <div class="card h-100 shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                    <img src="<?php echo e($video['thumbnail']); ?>" class="card-img-top">
                    <div class="card-body">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="video_ids[<?php echo e($video['id']); ?>]" value="<?php echo e($video['title']); ?>" class="custom-control-input" id="vid-<?php echo e($video['id']); ?>">
                            <label class="custom-control-label font-weight-bold small" for="vid-<?php echo e($video['id']); ?>"><?php echo e($video['title']); ?></label>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-12 text-center py-5 text-muted">No videos found on your channel with #MRSM.</div>
            <?php endif; ?>
        </div>

        <?php if(count($youtubeVideos) > 0): ?>
        <div class="text-right mt-4 p-3 bg-white sticky-bottom shadow-lg" style="border-radius: 12px 12px 0 0;">
            <a href="<?php echo e(route('resources.index')); ?>" class="btn btn-light mr-2 font-weight-bold">Cancel</a>
            <button type="submit" class="btn btn-danger px-5 font-weight-bold shadow">Import Selected Videos</button>
        </div>
        <?php endif; ?>
    </form>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.adminhome', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\islamicWebsiteEducation_FYP_2023657278\resources\views/resources/sync_selection.blade.php ENDPATH**/ ?>