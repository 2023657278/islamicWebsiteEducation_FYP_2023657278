

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-primary"><i class="fas fa-chart-line me-2"></i>Student Analytics</h2>
            <p class="text-muted mb-0">Detailed performance report for <?php echo e($student->name); ?></p>
        </div>
        <div class="btn-group">
            <a href="<?php echo e(route('students.index')); ?>" class="btn btn-secondary shadow-sm"><i class="fas fa-arrow-left me-1"></i> Back</a>
            <a href="<?php echo e(route('students.edit', $student->id)); ?>" class="btn btn-warning shadow-sm"><i class="fas fa-edit me-1"></i> Edit</a>
        </div>
    </div>

    <div class="row">
        
        <div class="col-lg-4">
            
            
            <div class="card shadow-sm mb-4 border-0 rounded-4">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                         <?php if($student->profile_photo_path): ?>
                            <img src="<?php echo e(asset('storage/' . $student->profile_photo_path)); ?>" class="rounded-circle shadow-sm" width="100" height="100" style="object-fit: cover;">
                        <?php else: ?>
                            <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center text-primary" style="width: 80px; height: 80px;">
                                <i class="fas fa-user-graduate fa-3x"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <h4 class="fw-bold text-dark mb-1"><?php echo e($student->name); ?></h4>
                    <span class="badge bg-primary px-3 py-2 mb-3 rounded-pill">
                        <?php echo e($student->group->group_name ?? 'No Group'); ?> (<?php echo e($student->group->year->year ?? '-'); ?>)
                    </span>
                    
                    
                    <div class="bg-light p-3 rounded-3 small text-start">
                        <div class="row g-2">
                            <div class="col-6">
                                <p class="mb-1 text-muted-dark"><strong>System ID:</strong></p>
                                <p class="mb-2 text-dark font-weight-bold">#<?php echo e($student->id); ?></p>
                                
                                <p class="mb-1 text-muted-dark"><strong>No. Maktab:</strong></p>
                                <p class="mb-2 text-primary font-weight-bold"><?php echo e($student->no_maktab ?? 'N/A'); ?></p>
                                
                                <p class="mb-1 text-muted-dark"><strong>Email Address:</strong></p>
                                <p class="mb-0 text-dark text-truncate" title="<?php echo e($student->email); ?>"><?php echo e($student->email); ?></p>
                            </div>
                            <div class="col-6 text-end">
                                <p class="mb-1 text-muted-dark"><strong>Phone Class:</strong></p>
                                <p class="mb-2 text-dark font-weight-bold"><?php echo e($student->phone_number ?? '-'); ?></p>
                                
                                <p class="mb-1 text-muted-dark"><strong>Enrolled Matrix:</strong></p>
                                <p class="mb-2 text-dark font-weight-bold"><?php echo e($student->created_at->format('M Y')); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="card shadow-sm border-0 rounded-4 text-white" style="background: linear-gradient(135deg, #0d9488, #115e59);">
                <div class="card-body p-4 text-center">
                    <h5 class="opacity-75 text-uppercase small ls-1">Overall Status</h5>
                    <h2 class="fw-bold mb-1"><?php echo e($cluster); ?></h2>
                    <p class="small opacity-75 mb-0">Avg Score: <?php echo e(round($currentAvg, 1)); ?>%</p>
                </div>
            </div>

             
             <div class="card shadow-sm border-0 rounded-4 mt-3 text-white" style="background: linear-gradient(135deg, #4f46e5, #4338ca);">
                <div class="card-body p-4 text-center">
                    <h5 class="opacity-75 text-uppercase small ls-1">Predicted Next Score</h5>
                    <h1 class="fw-bold mb-0 display-4"><?php echo e($predictedNextScore); ?>%</h1>
                    <p class="small opacity-75 mb-0">
                        Based on slope: <?php echo e(number_format($slope, 2)); ?>

                        <?php if($slope > 0): ?> <i class="fas fa-arrow-up"></i> <?php elseif($slope < 0): ?> <i class="fas fa-arrow-down"></i> <?php endif; ?>
                    </p>
                </div>
            </div>

        </div>

        
        <div class="col-lg-8">
            
            
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold m-0 text-dark"><i class="fas fa-chart-area me-2 text-primary"></i>Performance Trend & Prediction</h6>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>

            
            <h6 class="fw-bold text-dark mb-3"><i class="fas fa-tasks me-2 text-success"></i>Subject Completion</h6>
            <div class="row g-3">
                <?php $__empty_1 = true; $__currentLoopData = $subjectProgress; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $barColor = $sub->progress >= 100 ? 'success' : ($sub->progress >= 50 ? 'primary' : 'warning');
                        $scoreColor = $sub->avg_score >= 80 ? 'text-success' : ($sub->avg_score >= 50 ? 'text-primary' : 'text-danger');
                    ?>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-3 h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold mb-0"><?php echo e($sub->name); ?></h6>
                                    <span class="small fw-bold <?php echo e($scoreColor); ?>"><?php echo e($sub->avg_score); ?>% Avg</span>
                                </div>
                                
                                <div class="d-flex justify-content-between small text-muted mb-1">
                                    <span>Progress</span>
                                    <span><?php echo e($sub->completed); ?>/<?php echo e($sub->total); ?> Quizzes</span>
                                </div>

                                <div class="progress rounded-pill" style="height: 8px;">
                                    <div class="progress-bar bg-<?php echo e($barColor); ?>" role="progressbar" style="width: <?php echo e($sub->progress); ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="col-12 text-center text-muted py-3">No subject data available.</div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('trendChart').getContext('2d');
    
    const labels = <?php echo json_encode($dates, 15, 512) ?>;
    const actualScores = <?php echo json_encode($scores, 15, 512) ?>;
    const trendScores = <?php echo json_encode($trendPoints, 15, 512) ?>;

    labels.push('Next (Predicted)');
    trendScores.push(<?php echo e($predictedNextScore); ?>);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Actual Score',
                    data: actualScores,
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    borderWidth: 3,
                    pointRadius: 5,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#4f46e5',
                    pointBorderWidth: 2,
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Trend Prediction',
                    data: trendScores,
                    borderColor: '#f59e0b',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    pointRadius: 4,
                    pointBackgroundColor: '#f59e0b',
                    tension: 0,
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' },
                tooltip: { 
                    backgroundColor: '#1f2937',
                    padding: 10,
                    cornerRadius: 8
                }
            },
            scales: {
                y: { 
                    beginAtZero: true, 
                    max: 100,
                    grid: { borderDash: [2, 2] }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.adminhome', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\islamicWebsiteEducation_FYP_2023657278\resources\views/students/show.blade.php ENDPATH**/ ?>