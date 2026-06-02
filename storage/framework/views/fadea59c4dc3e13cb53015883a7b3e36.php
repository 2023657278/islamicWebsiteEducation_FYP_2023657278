<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Mission | <?php echo e($subject->subject_name); ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { 
            /* Deep Maroon Premium Palette */
            background-color: #4c0519;
            /* Subtle Geometric Cross-Grid Design Overlay */
            background-image: 
                radial-gradient(rgba(255, 255, 255, 0.15) 1px, transparent 0),
                radial-gradient(rgba(255, 255, 255, 0.1) 2px, transparent 0);
            background-size: 30px 30px;
            background-position: 0 0, 15px 15px;
            
            color: #f8fafc; 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            min-height: 100vh; 
            display: flex; 
            flex-direction: column;
            justify-content: center;
            position: relative;
        }
        
        /* 🔙 Navigation Element */
        .back-nav-container {
            position: absolute;
            top: 30px;
            left: 40px;
            z-index: 10;
        }
        .btn-back {
            color: #fecdd3;
            font-weight: 700;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            background: rgba(255, 255, 255, 0.1);
            padding: 10px 20px;
            border-radius: 50px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        .btn-back:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(-3px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            border-color: rgba(255, 255, 255, 0.4);
        }

        /* ⚔️ Mission Cards Layout */
        .mode-card { 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            cursor: pointer; 
            border: 2px solid transparent; 
            height: 100%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25) !important;
        }
        .mode-card:hover { 
            transform: translateY(-10px); 
            border-color: #fbbf24; 
            box-shadow: 0 25px 35px -5px rgba(0, 0, 0, 0.4) !important; 
        }
        .pvp-gradient { 
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); 
            color: white; 
        }
        .btn-warning { background: #fbbf24; border: none; color: #0f172a; font-weight: 800; }
        .fw-800 { font-weight: 800; }
        
        .badge-subject {
            background-color: #be123c;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        @media (max-width: 768px) {
            body { justify-content: start; padding-top: 90px; }
            .back-nav-container { top: 20px; left: 20px; position: fixed; width: 100%; text-align: left; }
        }
    </style>
</head>
<body>

<div class="back-nav-container">
    <a href="<?php echo e(route('student.quizzes.index')); ?>" class="btn btn-back text-decoration-none d-inline-flex align-items-center">
        <i class="fas fa-arrow-left me-2"></i> Back to Hub
    </a>
</div>

<div class="container py-5">
    <div class="text-center mb-5">
        <h2 class="fw-800 text-white text-uppercase tracking-wide">Select Your Mission Path</h2>
        <p class="text-white-50">Subject: <span class="badge badge-subject px-3 py-2 fs-6 rounded-3 shadow-sm"><?php echo e($subject->subject_name); ?></span></p>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-md-5">
            <a href="<?php echo e(route('student.quizzes.difficulties', $subject->id)); ?>?mode=solo" class="text-decoration-none">
                <div class="card border-0 rounded-4 p-5 mode-card bg-white shadow-sm text-center">
                    <div class="fs-1 mb-3 text-primary"><i class="fas fa-user-ninja"></i></div>
                    <h3 class="fw-bold text-dark">SOLO MISSION</h3>
                    <p class="text-muted">Battle the Guardian alone. Practice by Topic.</p>
                    <span class="btn btn-outline-primary rounded-pill px-4 mt-3">Start Training</span>
                </div>
            </a>
        </div>

        <div class="col-md-5">
            <div class="card border-0 rounded-4 p-5 mode-card pvp-gradient shadow-lg text-center" 
                 data-bs-toggle="modal" data-bs-target="#pvpActionModal">
                <div class="fs-1 mb-3 text-warning"><i class="fas fa-fire-alt"></i></div>
                <h3 class="fw-bold text-white">PVP ARENA</h3>
                <p class="text-white-50">Join up to 20 warriors. Random questions by Difficulty.</p>
                <span class="btn btn-warning fw-bold rounded-pill px-4 mt-3">Enter Arena</span>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="pvpActionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg" style="background: #0f172a;">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title text-white fw-bold">Battle Command Center</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <a href="<?php echo e(route('student.quizzes.browse', $subject->id)); ?>" class="btn btn-outline-info w-100 rounded-pill mb-3 fw-bold py-2">
                    <i class="fas fa-search me-2"></i>FIND PUBLIC LOBBY
                </a>

                <form action="<?php echo e(route('student.quizzes.join')); ?>" method="POST" class="mb-4">
                    <?php echo csrf_field(); ?>
                    <div class="input-group">
                        <input type="text" name="room_code" class="form-control bg-dark border-0 text-white fw-bold text-center font-monospace" placeholder="ROOM CODE" style="letter-spacing: 2px;">
                        <button class="btn btn-warning fw-bold px-4" type="submit">JOIN</button>
                    </div>
                </form>

                <div class="text-center mb-3"><span class="badge bg-secondary rounded-pill px-3">OR</span></div>

                <a href="<?php echo e(route('student.quizzes.difficulties', $subject->id)); ?>?mode=pvp" class="btn btn-primary w-100 rounded-pill fw-bold py-2 text-white border-0" style="background: #3b82f6;">
                    <i class="fas fa-plus-circle me-2"></i>CREATE NEW LOBBY
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html><?php /**PATH C:\laragon\www\islamicWebsiteEducation_FYP_2023657278\resources\views/users/quizzes/mode_selection.blade.php ENDPATH**/ ?>