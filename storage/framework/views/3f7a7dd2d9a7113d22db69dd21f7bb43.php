<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal | MRSM Terendak</title>
    
    <!-- CSS Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-red: #8B1E24; 
            --accent-orange: #C05621;
            --gold-accent: #D4AF37;
            --bg-cream: #FDFBF7;
            --sidebar-cream: #FFFDF5; /* ✅ NEW: Creamy White Color */
            --sidebar-width: 280px;
        }
        
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #f8fafc;
            background-image: 
                radial-gradient(at 0% 0%, rgba(139, 30, 36, 0.03) 0, transparent 50%), 
                radial-gradient(at 100% 100%, rgba(212, 175, 55, 0.05) 0, transparent 50%),
                url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%239C92AC' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            color: #1A202C; 
            overflow-x: hidden;
            min-height: 100vh;
        }
        
        /* --- DYNAMIC SIDEBAR DESIGN --- */
        .sidebar {
            width: var(--sidebar-width); 
            height: 100vh; 
            position: fixed; 
            top: 0; 
            left: 0;
            padding: 30px; 
            z-index: 1000; 
            display: flex; 
            flex-direction: column;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            /* ✅ Apply Creamy White with slight transparency */
            background: rgba(255, 253, 245, 0.95); 
            backdrop-filter: blur(12px);
            border-right: 1px solid rgba(212, 175, 55, 0.15);
            box-shadow: 4px 0 15px rgba(0,0,0,0.02);
        }

        /* Homepage Specific Sidebar (Royal Slate) */
        body.is-homepage .sidebar {
            background: radial-gradient(circle at top left, #1e293b, #0f172a);
            border-right: 2px solid var(--gold-accent);
            box-shadow: 10px 0 30px rgba(0,0,0,0.3);
        }

        .logo-section { display: flex; align-items: center; gap: 15px; margin-bottom: 50px; }
        .logo-icon { 
            background: linear-gradient(135deg, var(--primary-red), #b02a37); 
            color: white; 
            width: 45px; height: 45px; 
            display: flex; align-items: center; justify-content: center;
            border-radius: 12px; font-size: 1.4rem; 
            box-shadow: 0 8px 16px rgba(139, 30, 36, 0.2);
        }

        body.is-homepage .logo-section .platform-name { color: #f8fafc !important; }
        body.is-homepage .logo-section .platform-sub { color: var(--gold-accent) !important; opacity: 1; }
        
        /* Navigation Links */
        .nav-link {
            font-weight: 600; padding: 14px 18px; border-radius: 12px;
            margin-bottom: 8px; display: flex; align-items: center; gap: 15px; 
            transition: all 0.3s ease; color: #64748B;
        }
        
        .nav-link:hover { background-color: rgba(139, 30, 36, 0.05); color: var(--primary-red); transform: translateX(5px); }
        .nav-link.active { border-left: 4px solid var(--primary-red); background-color: #FFF5F5; color: var(--primary-red); }

        /* Homepage Link Styling */
        body.is-homepage .nav-link { color: #94a3b8; }
        body.is-homepage .nav-link:hover { color: white; background: rgba(255, 255, 255, 0.05); }
        body.is-homepage .nav-link.active {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.15) 0%, rgba(212, 175, 55, 0) 100%);
            color: var(--gold-accent);
            border-left: 5px solid var(--gold-accent);
            font-weight: 700;
        }

        /* --- TOPBAR TECH HUB --- */
        .topbar { 
            margin-left: var(--sidebar-width); 
            padding: 20px 40px;
            display: flex; 
            justify-content: space-between; 
            align-items: center;
        }

        .system-hub {
            display: flex; align-items: center; gap: 20px;
            background: rgba(255, 255, 255, 0.8); 
            backdrop-filter: blur(5px);
            padding: 8px 25px; border-radius: 50px;
            border: 1px solid rgba(226, 232, 240, 0.8); 
            box-shadow: 0 4px 12px rgba(0,0,0,0.02);
        }

        .hub-item { display: flex; align-items: center; gap: 8px; font-size: 0.7rem; font-weight: 800; color: #64748B; }
        .hub-status-dot { width: 8px; height: 8px; border-radius: 50%; background: #10B981; position: relative; }
        .hub-status-dot::after {
            content: ''; position: absolute; width: 100%; height: 100%;
            background: inherit; border-radius: 50%; animation: pulse 2s infinite;
        }

        @keyframes pulse { 0% { transform: scale(1); opacity: 0.8; } 100% { transform: scale(2.5); opacity: 0; } }

        /* --- AVATAR CAPSULE --- */
        .avatar-capsule {
            display: flex; align-items: center; gap: 12px; padding: 5px 15px 5px 5px;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(5px);
            border-radius: 50px; border: 1px solid #E2E8F0;
            transition: 0.3s;
        }
        .avatar-capsule:hover { background: #fff; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
        .avatar { 
            width: 35px; height: 35px; border-radius: 50%; 
            background: var(--primary-red); color: white; 
            display: flex; align-items: center; justify-content: center; 
            font-weight: 800; overflow: hidden; 
        }

        .main-content { margin-left: var(--sidebar-width); padding: 0 40px 40px 40px; }
        
        .sidebar-footer { margin-top: auto; padding-top: 20px; }
        body.is-homepage .logout-btn-container {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 15px; padding: 10px; border: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
</head>
<body class="<?php echo e(Route::is('student.homepage') ? 'is-homepage' : ''); ?>">

    <div class="sidebar">
        <!-- Logo Section -->
        <div class="logo-section">
            <div class="logo-icon"><i class="fas fa-kaaba"></i></div>
            <div>
                <div class="platform-name" style="font-weight: 800; font-size: 1.1rem; color: #1A202C;">PAI Platform</div>
                <div class="platform-sub" style="font-size: 0.7rem; color: #94A3B8; font-weight: 700; text-transform: uppercase;">MRSM Terendak</div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="nav flex-column mb-auto">
            <!-- Home Portal -->
            <a class="nav-link <?php echo e(Route::is('student.homepage') ? 'active' : ''); ?>" 
               href="<?php echo e(route('student.homepage')); ?>"
               style="<?php echo e(Route::is('student.homepage') ? 'margin-bottom: 20px; border: 1px solid rgba(212, 175, 55, 0.3); background: rgba(212, 175, 55, 0.1);' : 'margin-bottom: 20px;'); ?>">
                <i class="fas fa-house-user" style="font-size: 1.2rem; filter: drop-shadow(0 0 8px rgba(212, 175, 55, 0.5));"></i> 
                <span style="letter-spacing: 1px; text-transform: uppercase; font-size: 0.75rem;">Home Portal</span>
            </a>

            <!-- Divider -->
            <div style="height: 1px; background: rgba(139, 30, 36, 0.05); margin: 0 15px 20px 15px;"></div>

            <!-- Dashboard Links -->
            <a class="nav-link <?php echo e(Route::is('student.dashboard') ? 'active' : ''); ?>" href="<?php echo e(route('student.dashboard')); ?>">
                <i class="fas fa-chart-line"></i> <span>Dashboard</span>
            </a>
            <a class="nav-link <?php echo e(Route::is('student.quizzes.index') ? 'active' : ''); ?>" href="<?php echo e(route('student.quizzes.index')); ?>">
                <i class="fas fa-tasks"></i> <span>Quizzes</span>
            </a>
            <a class="nav-link <?php echo e(Route::is('student.textbooks.index') ? 'active' : ''); ?>" href="<?php echo e(route('student.textbooks.index')); ?>">
                <i class="fas fa-book-reader"></i> <span>Textbooks</span>
            </a>
            <a class="nav-link <?php echo e(Route::is('student.ranking') ? 'active' : ''); ?>" href="<?php echo e(route('student.ranking')); ?>">
                <i class="fas fa-award"></i> <span>Leaderboard</span>
            </a>
        </nav>
        
        <!-- Footer / Logout -->
        <div class="sidebar-footer">
            <div class="logout-btn-container">
                <form action="<?php echo e(route('logout')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn w-100 text-start border-0 text-danger fw-bold">
                        <i class="fas fa-power-off me-2"></i> Sign Out
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Topbar -->
    <div class="topbar">
        <div class="system-hub">
            <div class="hub-item">
                <div class="hub-status-dot"></div>
                <span>SYSTEM: <span class="text-dark">ONLINE</span></span>
            </div>
            <div class="hub-item border-start ps-3">
                <i class="fas fa-bolt text-warning"></i>
                <span id="ping-value">24ms</span>
            </div>
            <div class="hub-item border-start ps-3">
                <i class="fas fa-shield-alt text-primary"></i>
                <span>SECURE</span>
            </div>
        </div>
        
        <div class="profile-section d-flex align-items-center gap-3">
            <a href="<?php echo e(route('student.profile.show')); ?>" class="text-decoration-none">
                <div class="avatar-capsule shadow-sm">
                    <div class="avatar">
                        <?php if(Auth::user()->profile_image): ?>
                            <img src="<?php echo e(asset('storage/' . Auth::user()->profile_image)); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <?php echo e(substr(Auth::user()->name, 0, 1)); ?>

                        <?php endif; ?>
                    </div>
                    <div class="text-start">
                        <div style="font-weight: 800; font-size: 0.8rem; color: #1A202C; line-height: 1;"><?php echo e(explode(' ', Auth::user()->name)[0]); ?></div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Content Area -->
    <div class="main-content">
        <?php echo $__env->yieldContent('content'); ?>
    </div>

    <script>
        setInterval(() => {
            const ping = Math.floor(Math.random() * (32 - 18 + 1) + 18);
            const pingElement = document.getElementById('ping-value');
            if(pingElement) pingElement.innerText = ping + 'ms';
        }, 4000);
    </script>
</body>
</html><?php /**PATH C:\laragon\www\islamicWebsiteEducation_FYP_2023657278\resources\views/users/students.blade.php ENDPATH**/ ?>