

<?php $__env->startSection('content'); ?>
<div class="container-fluid p-0">
    
    
    <a href="<?php echo e(route('student.resources.index')); ?>" class="text-muted text-decoration-none mb-4 d-inline-block">
        <i class="fas fa-arrow-left me-2"></i> Back to Teachers
    </a>

    
    <div class="d-flex align-items-center mb-5">
        <div class="me-4" style="width: 80px; height: 80px; border-radius: 50%; background: #fff; border: 4px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.1); overflow: hidden; display: flex; align-items: center; justify-content: center;">
            <?php if($teacher->profile_image): ?>
                <img src="<?php echo e(asset('storage/' . $teacher->profile_image)); ?>" style="width: 100%; height: 100%; object-fit: cover;">
            <?php else: ?>
                <h2 class="fw-bold text-secondary m-0"><?php echo e(substr($teacher->name, 0, 2)); ?></h2>
            <?php endif; ?>
        </div>
        <div>
            <h2 class="fw-bold text-dark mb-1"><?php echo e($teacher->name); ?></h2>
            <p class="text-muted mb-0"><?php echo e($subjectName); ?></p>
        </div>
    </div>

    
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-bottom pt-3">
            <ul class="nav nav-tabs card-header-tabs border-0" id="myTab" role="tablist">
                <li class="nav-item me-4" role="presentation">
                    <button class="nav-link active border-0 bg-transparent fw-bold text-danger border-bottom border-3 border-danger pb-3" id="videos-tab" data-bs-toggle="tab" data-bs-target="#videos" type="button" role="tab">
                        <i class="fas fa-video me-2"></i> Videos (<?php echo e($videos->count()); ?>)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link border-0 bg-transparent fw-bold text-muted pb-3" id="notes-tab" data-bs-toggle="tab" data-bs-target="#notes" type="button" role="tab">
                        <i class="fas fa-file-alt me-2"></i> Notes (<?php echo e($notes->count()); ?>)
                    </button>
                </li>
            </ul>
        </div>
        
        <div class="card-body p-4">
            <div class="tab-content" id="myTabContent">
                
                
                <div class="tab-pane fade show active" id="videos" role="tabpanel">
                    <div class="row g-4">
                        <?php $__empty_1 = true; $__currentLoopData = $videos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $video): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="ratio ratio-16x9">
                                    <iframe src="https://www.youtube.com/embed/<?php echo e($video->file_url); ?>" allowfullscreen></iframe>
                                </div>
                                <div class="card-body">
                                    <h6 class="fw-bold text-dark mb-2"><?php echo e($video->title); ?></h6>
                                    <small class="text-muted"><?php echo e($video->created_at->format('d M Y')); ?></small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="col-12 text-center py-5 text-muted">No videos uploaded yet.</div>
                        <?php endif; ?>
                    </div>
                </div>

                
                <div class="tab-pane fade" id="notes" role="tabpanel">
                    <div class="list-group list-group-flush">
                        <?php $__empty_1 = true; $__currentLoopData = $notes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $note): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="list-group-item border-0 border-bottom py-3 px-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="me-3 p-3 rounded bg-light text-danger">
                                        <i class="fas fa-file-pdf fa-lg"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1"><?php echo e($note->title); ?></h6>
                                        <small class="text-muted">Uploaded <?php echo e($note->created_at->format('d M Y')); ?></small>
                                    </div>
                                </div>
                                <div>
                                    <a href="<?php echo e(route('resources.preview', $note->id)); ?>" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3 me-2">View</a>
                                    <a href="<?php echo e(route('resources.download', $note->id)); ?>" class="btn btn-outline-success btn-sm rounded-pill px-3">Download</a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="col-12 text-center py-5 text-muted">No notes uploaded yet.</div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('users.students', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\islamicWebsiteEducation_FYP_2023657278\resources\views/users/resources/show.blade.php ENDPATH**/ ?>