

<?php $__env->startSection('content'); ?>
<style>
    /* --- THEME VARIABLES --- */
    :root { 
        --deep-maroon: #4a0000; 
        --accent-gold: #c5a059; 
        --glass-white: rgba(255, 255, 255, 0.95); 
    }
    
    .main-wrapper { 
        background-color: var(--deep-maroon); 
        min-height: 100vh; 
        padding: 2rem; 
        background-image: radial-gradient(circle at top right, rgba(128,0,0,0.4), transparent); 
    }

    .glass-card { 
        background: var(--glass-white); 
        border-radius: 30px; 
        box-shadow: 0 40px 80px rgba(0,0,0,0.4); 
        padding: 30px; 
    }

    /* --- GRID LAYOUT --- */
    /* UPDATED: 1 Column for Day Label + 11 Columns for Time Slots (08:00 to 18:00) */
    .timetable-grid {
        display: grid;
        grid-template-columns: 80px repeat(11, 1fr); 
        gap: 8px;
        overflow-x: auto; 
        padding-bottom: 20px; 
    }

    /* Top Row: Time Headers */
    .grid-header-time {
        background: var(--deep-maroon);
        color: white;
        text-align: center;
        padding: 8px;
        border-radius: 8px;
        margin-bottom: 5px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .period-num { font-size: 1.2rem; font-weight: 800; color: var(--accent-gold); line-height: 1; }
    .period-time { font-size: 0.65rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin-top: 4px; }

    /* Left Column: Day Labels (Mo, Tu, We...) */
    .day-label-cell {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        color: var(--deep-maroon);
        font-weight: 900;
        font-size: 1.8rem;
        border-radius: 15px;
        border: 2px solid #e2e8f0;
        box-shadow: inset 0 0 10px rgba(0,0,0,0.05);
        height: 100%; /* Fill height */
    }

    /* The Lesson Cell container */
    .cell {
        background: #f8fafc;
        border-radius: 12px;
        min-height: 110px;
        border: 1px solid #eef2f6;
        padding: 4px;
        position: relative;
    }

    /* The content inside the cell */
    .lesson-card {
        background: white;
        border-radius: 8px;
        padding: 8px;
        height: 100%;
        border-left: 4px solid var(--deep-maroon);
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        justify-content: center;
        transition: transform 0.2s;
    }
    .lesson-card:hover { transform: translateY(-2px); }

    /* Highlight for the specific timetable being viewed */
    .highlight {
        border: 2px solid var(--accent-gold);
        background: #fffdf5;
        box-shadow: 0 0 15px rgba(197, 160, 89, 0.3);
        z-index: 10;
        transform: scale(1.02);
    }

    .subject-name { font-size: 0.8rem; font-weight: 800; color: #1e293b; line-height: 1.2; }
    .teacher-name { font-size: 0.65rem; color: #64748b; font-weight: 600; margin-top: 4px; }
    
    /* Back Button Style */
    .btn-back {
        background: var(--deep-maroon);
        color: white;
        border-radius: 50px;
        padding: 8px 20px;
        font-weight: 600;
        transition: 0.3s;
    }
    .btn-back:hover { background: #000; color: white; transform: translateX(-5px); }
</style>

<div class="main-wrapper">
    <div class="container-fluid">
        <div class="glass-card">
            
            
            <div class="d-flex justify-content-between align-items-center mb-5 pb-3 border-bottom">
                <div>
                    <h2 class="fw-bold mb-0" style="color: var(--deep-maroon);"><?php echo e($timetable->group->group_name); ?></h2>
                    <p class="text-muted fw-bold small m-0 uppercase">
                        <span style="color: var(--accent-gold)">MASTER SCHEDULE</span> • Session <?php echo e($timetable->group->year->year); ?>

                    </p>
                </div>
                <a href="<?php echo e(route('timetables.index')); ?>" class="btn btn-back shadow-sm text-decoration-none">
                    <i class="fas fa-chevron-left me-2"></i> Return
                </a>
            </div>

            
            <?php 
                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
                $shortDays = ['Mo', 'Tu', 'We', 'Th', 'Fr'];
                // UPDATED: Added '18:00' to the array below
                $timeslots = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00'];
                
                // Fetch all schedules for this group
                $schedules = \App\Models\Timetable::where('group_id', $timetable->group_id)
                    ->with(['subject','teacher','day'])
                    ->get();
            ?>

            
            <div class="timetable-grid">
                
                
                <div class="text-center fw-bold text-muted d-flex align-items-end justify-content-center pb-2">
                    <small>Day/Time</small>
                </div>
                
                <?php $__currentLoopData = $timeslots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $time): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="grid-header-time shadow-sm">
                        <span class="period-num"><?php echo e($index + 1); ?></span>
                        <span class="period-time"><?php echo e($time); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                
                <?php $__currentLoopData = $days; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dayIndex => $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    
                    
                    <div class="day-label-cell">
                        <?php echo e($shortDays[$dayIndex]); ?>

                    </div>
                    
                    <?php 
                        $slotsToSkip = 0; // Reset counter for the new row
                    ?>

                    
                    <?php $__currentLoopData = $timeslots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $time): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    
                        
                        <?php if($slotsToSkip > 0): ?>
                            <?php $slotsToSkip--; ?>
                            <?php continue; ?>
                        <?php endif; ?>

                        <?php
                            // Check for lesson starting at this exact Day & Time
                            $currentLesson = $schedules->filter(function($item) use ($day, $time) {
                                return $item->day->day_name == $day && 
                                       date('H', strtotime($item->time_from)) == date('H', strtotime($time));
                            })->first();

                            $colSpan = 1; // Default span
                            
                            // If lesson exists, calculate duration
                            if($currentLesson) {
                                $start = \Carbon\Carbon::parse($currentLesson->time_from);
                                $end   = \Carbon\Carbon::parse($currentLesson->time_to); 
                                $hours = $end->diffInHours($start); 
                                
                                // Set span (minimum 1)
                                $colSpan = $hours > 0 ? $hours : 1; 
                                
                                // Set how many FUTURE slots to skip
                                $slotsToSkip = $colSpan - 1; 
                            }
                        ?>

                        
                        <div class="cell" style="grid-column: span <?php echo e($colSpan); ?>;">
                            <?php if($currentLesson): ?>
                                <div class="lesson-card <?php echo e($currentLesson->id == $timetable->id ? 'highlight' : ''); ?>">
                                    
                                    
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="subject-name"><?php echo e($currentLesson->subject->subject_name); ?></div>
                                        <?php if($colSpan > 1): ?>
                                            <span class="badge bg-warning text-dark" style="font-size: 0.6rem;"><?php echo e($colSpan); ?> Hrs</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    
                                    <div class="teacher-name text-truncate">
                                        <i class="fas fa-user-tie me-1"></i> <?php echo e($currentLesson->teacher->name); ?>

                                    </div>
                                    
                                    
                                    <div class="text-muted small mt-1" style="font-size: 0.65rem;">
                                        <?php echo e(date('h:i', strtotime($currentLesson->time_from))); ?> - <?php echo e(date('h:i', strtotime($currentLesson->time_to))); ?>

                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> 
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> 

            </div>
            
            <div class="mt-4 text-center text-muted small fw-bold">
                <i class="fas fa-school me-1"></i> Official School Timetable Layout
            </div>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.adminhome', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\islamicWebsiteEducation_FYP_2023657278\resources\views/timetables/show.blade.php ENDPATH**/ ?>