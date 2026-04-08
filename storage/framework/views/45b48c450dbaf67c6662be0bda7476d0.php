<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PAI Learning | MRSM Terendak</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    
    
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            --primary-red: #8B1E24; 
            --accent-brown: #C05621; 
            --bg-cream: #FFF9F2; 
            --text-dark: #1A202C;
            --text-gray: #4A5568;
        }

        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--bg-cream); color: var(--text-dark); overflow-x: hidden; }

        /* --- Navbar --- */
        .navbar { background-color: #fff; padding: 1rem 0; border-bottom: 1px solid rgba(0,0,0,0.05); }
        .navbar-brand { display: flex; align-items: center; gap: 12px; }
        .brand-icon { width: 40px; height: 40px; background-color: var(--primary-red); color: white; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
        .brand-title { font-weight: 700; font-size: 1.1rem; color: var(--text-dark); line-height: 1.2; }
        
        .btn-primary-custom { background-color: var(--primary-red); color: white; font-weight: 600; padding: 12px 32px; border-radius: 12px; border: none; transition: 0.3s; text-decoration: none; display: inline-block; }
        .btn-primary-custom:hover { background-color: #6b151a; color: white; transform: translateY(-2px); }

        /* --- Hero Section: Framed Look --- */
        section { padding: 80px 0; }
        .hero-title { font-weight: 800; font-size: 3.5rem; line-height: 1.1; margin-bottom: 1.5rem; }
        .hero-img-container {
            background: white; padding: 10px; border-radius: 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.05);
        }
        .hero-image { width: 100%; border-radius: 20px; }

        /* --- Tools with Descriptions --- */
        .feature-card { background: white; padding: 25px; border-radius: 20px; border: 1px solid #eee; transition: 0.3s; height: 100%; display: flex; flex-direction: column; text-align: left; }
        .feature-card:hover { transform: translateY(-5px); border-color: var(--primary-red); box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .feature-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: white; margin-bottom: 15px; }

        /* --- Quiz Section Glassmorphism (Upgraded) --- */
        .quiz-section { background-color: var(--primary-red); color: white; }
        .quiz-card { background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.1); border-radius: 16px; padding: 20px; backdrop-filter: blur(10px); height: 100%; transition: 0.3s; }
        .quiz-card:hover { background: rgba(255,255,255,0.15); transform: translateY(-5px); }
        .progress-line { height: 4px; background: rgba(255,255,255,0.2); border-radius: 2px; width: 100%; margin-top: 8px; }
        .progress-fill { height: 100%; background: #FBBF24; border-radius: 2px; }

        /* --- Textbook Section --- */
        .textbook-card { background: white; border-radius: 16px; overflow: hidden; border: 1px solid #eee; transition: 0.3s; height: 100%; }
        .textbook-img { height: 160px; width: 100%; object-fit: cover; }
        .textbook-body { padding: 15px; text-align: left; }

        /* --- About Section (Upgraded with Map) --- */
        .about-section { background: white; }
        .stat-icon { width: 80px; height: 80px; background: var(--primary-red); color: white; border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 20px; }
        .map-container { border-radius: 24px; overflow: hidden; border: 8px solid white; box-shadow: 0 15px 35px rgba(0,0,0,0.08); cursor: pointer; transition: 0.3s; }
        .map-container:hover { transform: translateY(-5px); box-shadow: 0 20px 45px rgba(0,0,0,0.12); }
        
        footer { background-color: var(--text-dark); color: rgba(255,255,255,0.7); padding: 40px 0; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">
                <div class="brand-icon"><i class="fas fa-book-open"></i></div>
                <div class="text-start">
                    <span class="brand-title d-block">PAI Learning</span>
                    <span class="brand-subtitle">MRSM Terendak</span>
                </div>
            </a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link" href="#quiz">Quiz</a></li>
                    <li class="nav-item"><a class="nav-link" href="#textbooks">Textbooks</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                </ul>
                <div class="d-flex align-items-center gap-3">
                    <?php if(auth()->guard()->check()): ?>
                        <a href="<?php echo e(route('student.dashboard')); ?>" class="btn-primary-custom px-4">Dashboard</a>
                    <?php else: ?>
                        <a href="<?php echo e(route('login')); ?>" class="btn-primary-custom px-4">Log In</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    
    <section class="hero-section mt-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 text-start" data-aos="fade-right">
                    <h1 class="hero-title">Master Your <br> <span style="color: var(--accent-brown)">Islamic Studies</span></h1>
                    <p class="lead text-muted mb-4">The ultimate digital platform designed for PAI students at MRSM Terendak.</p>
                    <a href="<?php echo e(route('student.dashboard')); ?>" class="btn-primary-custom">Get Started <i class="fas fa-arrow-right ms-2"></i></a>
                </div>
                <div class="col-lg-6 mt-5 mt-lg-0" data-aos="fade-left">
                    <div class="hero-img-container">
                        <img src="<?php echo e(asset('image/BoardMRSM.png')); ?>" class="hero-image" alt="MRSM Terendak Entrance">
                    </div>
                </div>
            </div>
        </div>
    </section>

    
    <section id="features">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="fw-bold display-6">Integrated Learning Tools</h2>
                <p class="text-muted">A comprehensive set of tools to aid your academic journey.</p>
            </div>
            <div class="row g-4 text-start">
                <?php
                    $features = [
                        ['icon' => 'far fa-calendar-alt', 'bg' => '#8B1E24', 'title' => 'Timetable', 'desc' => 'Organize your study hours and stay on top of your class schedules.', 'route' => 'student.timetable.view'],
                        ['icon' => 'far fa-user', 'bg' => '#C05621', 'title' => 'Profile', 'desc' => 'Track your personal information, achievements, and account settings.', 'route' => 'student.profile.show'],
                        ['icon' => 'far fa-comment-dots', 'bg' => '#D93025', 'title' => 'Messages', 'desc' => 'Directly communicate with your teachers for instant learning support.', 'route' => 'student.messages.index'],
                        ['icon' => 'far fa-folder-open', 'bg' => '#00897B', 'title' => 'Resources', 'desc' => 'Access a curated collection of PAI notes and video materials.', 'route' => 'student.resources.index'],
                        ['icon' => 'fas fa-graduation-cap', 'bg' => '#F59E0B', 'title' => 'Flashcards', 'desc' => 'Boost your memorization with subject-specific digital flashcards.', 'route' => 'student.flashcards.index'],
                        ['icon' => 'fas fa-chart-line', 'bg' => '#8B1E24', 'title' => 'Progress', 'desc' => 'Visualize your academic growth and quiz performance over time.', 'route' => 'student.progress.index'],
                    ];
                ?>
                <?php $__currentLoopData = $features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-4" data-aos="zoom-in" data-aos-delay="<?php echo e($loop->index * 50); ?>">
                    <div class="feature-card shadow-sm">
                        <div class="feature-icon" style="background: <?php echo e($f['bg']); ?>;"><i class="<?php echo e($f['icon']); ?>"></i></div>
                        <h5 class="fw-bold mb-2"><?php echo e($f['title']); ?></h5>
                        <p class="text-muted small mb-4"><?php echo e($f['desc']); ?></p>
                        <a href="<?php echo e(route($f['route'])); ?>" class="text-danger fw-bold text-decoration-none mt-auto">Go to Tool <i class="fas fa-arrow-right ms-2"></i></a>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </section>

    
    <section id="quiz" class="quiz-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-5 text-start" data-aos="fade-right">
                    <h2 class="fw-bold display-5 text-white mb-4">Quiz Center</h2>
                    <p class="lead text-white opacity-75 mb-4">Validate your understanding through interactive tests. Our quizzes are designed to help you prepare for Form 4 examinations with real-time feedback.</p>
                    <a href="<?php echo e(route('student.quizzes.index')); ?>" class="btn btn-light text-danger fw-bold px-5 py-3 rounded-pill shadow-lg">Start Quiz Now</a>
                </div>
                <div class="col-lg-6 offset-lg-1">
                    <div class="row g-3">
                        <?php
                            $quizInfo = [
                                'Al-Quran' => 'Tajwid rules and memorization.',
                                'Hadis' => 'Understanding Prophetic teachings.',
                                'Akidah' => 'Foundations of Islamic belief.',
                                'Fiqh' => 'Practical application of Shariah.',
                                'Sirah' => 'Lessons from Islamic history.',
                                'Akhlak' => 'Cultivating noble character.'
                            ];
                        ?>
                        <?php $__currentLoopData = $quizInfo; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-6" data-aos="fade-up" data-aos-delay="<?php echo e($loop->index * 50); ?>">
                            <div class="quiz-card text-start">
                                <span class="fw-bold text-white d-block mb-1"><?php echo e($name); ?></span>
                                <p class="small text-white-50 mb-0" style="font-size: 0.75rem;"><?php echo e($info); ?></p>
                                <div class="progress-line"><div class="progress-fill" style="width: 15%;"></div></div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    
    <section id="textbooks">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="fw-bold display-5">Digital Library</h2>
                <p class="text-muted">High-resolution textbooks available on all your devices.</p>
            </div>
            <div class="row g-4 text-start">
                <?php
                    $images = [
                        asset('image/quran_front.jpg'),
                        asset('image/hadith_front.jpg'),
                        asset('image/akidah_front.jpg'),
                        asset('image/fiqh_front.png'),
                        asset('image/sirah_front.jpg'),
                        asset('image/akhlak_front.jpg')
                    ];
                ?>
                <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-4 col-lg-2" data-aos="flip-left" data-aos-delay="<?php echo e($loop->index * 100); ?>">
                    <div class="textbook-card shadow-sm">
                        <img src="<?php echo e($images[$index % 6]); ?>" class="textbook-img" alt="<?php echo e($subject->subject_name); ?>">
                        <div class="textbook-body">
                            <h6 class="fw-bold text-dark text-truncate mb-0"><?php echo e($subject->subject_name); ?></h6>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="text-center mt-5" data-aos="zoom-in">
                <a href="<?php echo e(route('student.textbooks.index')); ?>" class="btn-primary-custom px-5 py-3 shadow-lg">
                    Start Reading <i class="fas fa-book-open ms-2"></i>
                </a>
            </div>
        </div>
    </section>

    
    <section id="about" class="about-section border-top">
        <div class="container">
            <div class="row align-items-center g-5" data-aos="fade-up">
                <div class="col-lg-5 text-start">
                    <h2 class="fw-bold display-5 mb-4">About Platform</h2>
                    <p class="text-muted mb-4">Developed specifically for students at MRSM Terendak, Melaka. This platform combines traditional Islamic education with modern learning technology to help you excel in your studies.</p>
                    
                    <div class="row g-4 mt-2">
                        <div class="col-6">
                            <div class="stat-icon shadow-sm mb-3"><i class="fas fa-user-graduate"></i></div>
                            <h5 class="fw-bold">Student-Centric</h5>
                        </div>
                        <div class="col-6">
                            <div class="stat-icon shadow-sm mb-3"><i class="fas fa-check-double"></i></div>
                            <h5 class="fw-bold">Interactive</h5>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    
                    <div class="map-container" onclick="window.open('https://www.google.com/maps/place/MRSM+Terendak/@2.2887568,102.1142923,17z', '_blank')">
                        <img src="<?php echo e(asset('image/MRSM-map.png')); ?>" class="img-fluid w-100" style="min-height: 350px; object-fit: cover;" alt="Map of MRSM Terendak Location">
                        <div class="bg-white p-3 text-center">

http://googleusercontent.com/map_location_reference/1
                            <small class="text-muted fw-bold"><i class="fas fa-hand-pointer me-2"></i>Click to open [MRSM Terendak](http://googleusercontent.com/map_location_reference/0) in Google Maps</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="text-center">
        <div class="container border-top pt-4">
            <p class="small text-white">&copy; <?php echo e(date('Y')); ?> PAI Learning Platform. All Rights Reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            AOS.init({ duration: 800, once: true, offset: 100, easing: 'ease-in-out' });
        });
    </script>
</body>
</html><?php /**PATH C:\laragon\www\islamicWebsiteEducation_FYP_2023657278\resources\views/homepage/welcome.blade.php ENDPATH**/ ?>