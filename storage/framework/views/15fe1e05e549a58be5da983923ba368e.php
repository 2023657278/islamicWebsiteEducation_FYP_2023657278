<div class="card border-0 h-100" style="border-radius: 12px 12px 0 0; background: #fff; position: relative;">
    <?php if($res->type == 'video'): ?>
        <div class="ratio ratio-16x9 bg-dark" style="border-radius: 12px 12px 0 0; overflow: hidden;">
            <iframe src="https://www.youtube.com/embed/<?php echo e($res->file_url); ?>" allowfullscreen style="width: 100%; border: none; aspect-ratio: 16/9;"></iframe>
        </div>
        <div class="card-body p-3 d-flex flex-column">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <h6 class="font-weight-bold text-dark mb-0 text-truncate" style="max-width: 85%;"><?php echo e($res->title); ?></h6>
                <div class="dropdown">
                    <button class="btn btn-sm text-muted p-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
                    <div class="dropdown-menu dropdown-menu-right shadow border-0" style="z-index: 9999;">
                        <form action="<?php echo e(route('resources.destroy', $res->id)); ?>" method="POST"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button type="submit" class="dropdown-item text-danger" onclick="return confirm('Delete video?')"><i class="fas fa-trash mr-2"></i> Delete</button></form>
                    </div>
                </div>
            </div>
            <div class="mb-2"><span class="badge bg-light text-dark border"><?php echo e($res->subject->subject_name); ?></span> <span class="badge text-white" style="background:#be185d;">Video</span></div>
            <div class="mt-auto pt-2 border-top d-flex justify-content-between align-items-center"><small class="text-muted"><i class="fab fa-youtube text-danger mr-1"></i> YouTube</small><small class="text-muted"><?php echo e($res->created_at->format('d M')); ?></small></div>
        </div>
    <?php else: ?>
        <div class="card-body p-4 d-flex flex-column h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <?php $ext = pathinfo($res->file_url, PATHINFO_EXTENSION); $bg = ($ext == 'pdf') ? 'bg-danger text-white' : 'bg-primary text-white'; ?>
                <div class="rounded d-flex align-items-center justify-content-center <?php echo e($bg); ?>" style="width: 48px; height: 48px;"><i class="fas <?php echo e($ext == 'pdf' ? 'fa-file-pdf' : 'fa-file-alt'); ?> fa-lg"></i></div>
                <div class="dropdown">
                    <button class="btn btn-sm text-muted p-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
                    <div class="dropdown-menu dropdown-menu-right shadow border-0" style="z-index: 9999;">
                        <a class="dropdown-item" href="<?php echo e(route('resources.preview', $res->id)); ?>" target="_blank"><i class="fas fa-eye mr-2"></i> Preview</a>
                        <a class="dropdown-item" href="<?php echo e(route('resources.download', $res->id)); ?>"><i class="fas fa-download mr-2"></i> Download</a>
                        <div class="dropdown-divider"></div>
                        <form action="<?php echo e(route('resources.destroy', $res->id)); ?>" method="POST"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button type="submit" class="dropdown-item text-danger" onclick="return confirm('Delete resource?')"><i class="fas fa-trash mr-2"></i> Delete</button></form>
                    </div>
                </div>
            </div>
            <h6 class="font-weight-bold text-dark mb-1 text-truncate"><?php echo e($res->title); ?></h6>
            <div class="mb-3"><span class="badge bg-light text-dark border"><?php echo e($res->subject->subject_name ?? 'General'); ?></span> <span class="badge <?php echo e($res->type == 'note' ? 'bg-info text-white' : 'bg-warning text-dark'); ?>"><?php echo e(ucfirst($res->type)); ?></span></div>
            <div class="mt-auto d-flex justify-content-between align-items-center pt-3 border-top"><small class="text-muted font-weight-bold text-uppercase"><?php echo e($ext); ?></small><small class="text-muted"><?php echo e($res->created_at->format('d M')); ?></small></div>
        </div>
    <?php endif; ?>
</div><?php /**PATH C:\laragon\www\islamicWebsiteEducation_FYP_2023657278\resources\views/resources/partials/resource-card.blade.php ENDPATH**/ ?>