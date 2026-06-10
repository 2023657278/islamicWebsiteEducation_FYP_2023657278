<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ilmora | PAI Learning Platform</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    
    {{-- ✅ AOS Library --}}
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            --primary-red: #8B1E24; 
            --accent-brown: #C05621; 
            --gold-accent: #D4AF37;
            --bg-cream: #FFFDF9; 
            --text-dark: #1A202C;
            --text-gray: #4A5568;
            --royal-slate: #0f172a;
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: var(--bg-cream); 
            /* Enhanced layered background for maximum visibility of the geometric design */
            background-image: 
                radial-gradient(at 0% 0%, rgba(139, 30, 36, 0.05) 0, transparent 50%), 
                radial-gradient(at 100% 100%, rgba(212, 175, 55, 0.08) 0, transparent 50%),
                radial-gradient(rgba(139, 30, 36, 0.08) 1.5px, transparent 0),
                url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23d4af37' fill-opacity='0.08'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            background-size: auto, auto, 40px 40px, auto;
            background-position: 0 0, 0 0, 0 0, 0 0;
            color: var(--text-dark); 
            overflow-x: hidden; 
        }

        /* --- Navbar with Animated Hover Lines --- */
        .navbar { 
            background-color: rgba(255, 255, 255, 0.95); 
            backdrop-filter: blur(12px);
            padding: 1rem 0; 
            border-bottom: 1px solid rgba(212, 175, 55, 0.2); 
        }
        .navbar-brand { display: flex; align-items: center; gap: 12px; }
        .brand-icon { 
            width: 42px; height: 42px; 
            background: linear-gradient(135deg, var(--primary-red), #a3242a); 
            color: white; border-radius: 12px; 
            display: flex; align-items: center; justify-content: center; 
            font-size: 1.2rem;
            box-shadow: 0 6px 12px rgba(139, 30, 36, 0.15);
        }
        .brand-title { font-weight: 800; font-size: 1.15rem; color: var(--text-dark); line-height: 1.1; letter-spacing: -0.5px; }
        .brand-subtitle { font-size: 0.72rem; color: #718096; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }

        .navbar-nav .nav-link {
            font-weight: 600;
            color: var(--text-gray) !important;
            margin: 0 15px;
            padding: 5px 0;
            position: relative;
            transition: color 0.3s ease;
        }

        .navbar-nav .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -2px;
            left: 0;
            background-color: var(--primary-red);
            visibility: hidden;
            transition: all 0.3s ease-in-out;
        }

        .navbar-nav .nav-link:hover {
            color: var(--primary-red) !important;
        }

        .navbar-nav .nav-link:hover::after {
            visibility: visible;
            width: 100%;
        }
        
        .btn-primary-custom { 
            background: linear-gradient(135deg, var(--primary-red) 0%, #70161b 100%); 
            color: white; font-weight: 700; padding: 12px 32px; border-radius: 12px; 
            border: 1px solid rgba(139, 30, 36, 0.2); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            text-decoration: none; display: inline-block;
            box-shadow: 0 4px 14px rgba(139, 30, 36, 0.25);
        }
        .btn-primary-custom:hover { color: white; transform: translateY(-3px); box-shadow: 0 8px 20px rgba(139, 30, 36, 0.4); }

        /* --- Hero Section --- */
        section { padding: 90px 0; }
        .hero-section { padding-top: 140px; }
        .hero-title { font-weight: 800; font-size: 4rem; line-height: 1.05; margin-bottom: 1.5rem; letter-spacing: -1.5px; }
        .hero-img-container {
            background: white; padding: 12px; border-radius: 28px; 
            box-shadow: 0 24px 50px rgba(0,0,0,0.06);
            border: 1px solid rgba(212, 175, 55, 0.2);
            position: relative;
        }
        .hero-img-container::before {
            content: ''; position: absolute; top: -15px; right: -15px; width: 100%; height: 100%;
            border: 2px dashed var(--gold-accent); border-radius: 28px; z-index: -1; opacity: 0.5;
        }
        .hero-image { width: 100%; border-radius: 20px; object-fit: cover; }

        /* --- Feature Cards --- */
        .feature-section { background-color: rgba(248, 250, 252, 0.6); border-top: 1px solid #E2E8F0; border-bottom: 1px solid #E2E8F0; backdrop-filter: blur(5px); }
        .feature-card { 
            background: rgba(255, 255, 255, 0.9); padding: 32px 25px; border-radius: 22px; 
            border: 1px solid rgba(226, 232, 240, 0.8); 
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); 
            height: 100%; display: flex; flex-direction: column; text-align: left; 
        }
        .feature-card:hover { transform: translateY(-8px); border-color: var(--gold-accent); box-shadow: 0 16px 36px rgba(0,0,0,0.08); }
        .feature-icon { 
            width: 54px; height: 54px; border-radius: 14px; 
            display: flex; align-items: center; justify-content: center; 
            font-size: 1.4rem; color: white; margin-bottom: 20px; 
            box-shadow: 0 6px 12px rgba(0,0,0,0.05);
        }

        /* --- Quiz Section (Royal Slate Background) --- */
        .quiz-section { 
            background: radial-gradient(circle at top left, #1e293b, #0f172a);
            color: white; 
            border-radius: 40px;
            margin: 0 15px;
            padding: 90px 0;
            border: 1px solid var(--gold-accent);
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.2);
        }
        .quiz-card { 
            background: rgba(255,255,255,0.05); 
            border: 1px solid rgba(255,255,255,0.1); 
            border-radius: 20px; padding: 25px; 
            backdrop-filter: blur(10px); height: 100%; 
            transition: all 0.3s ease; 
        }
        .quiz-card:hover { background: rgba(255,255,255,0.08); border-color: var(--gold-accent); transform: translateY(-4px); }
        .progress-line { height: 5px; background: rgba(255,255,255,0.1); border-radius: 10px; width: 100%; margin-top: 12px; overflow: hidden; }
        .progress-fill { height: 100%; background: var(--gold-accent); border-radius: 10px; box-shadow: 0 0 8px var(--gold-accent); }

        /* --- Textbooks Section --- */
        .textbook-card { 
            background: rgba(255, 255, 255, 0.95); border-radius: 18px; overflow: hidden; 
            border: 1px solid rgba(226, 232, 240, 0.8); 
            transition: all 0.3s ease; height: 100%; 
        }
        .textbook-card:hover { transform: scale(1.03); box-shadow: 0 12px 24px rgba(0,0,0,0.08); border-color: rgba(212, 175, 55, 0.5); }
        .textbook-img { height: 190px; width: 100%; object-fit: cover; border-bottom: 1px solid #f1f5f9; }
        .textbook-body { padding: 16px; text-align: left; }

        /* --- About Section (Warm Alabaster) --- */
        .about-section { 
            background: #F9F7F2; 
            padding: 100px 0; 
            border-top: 1px solid rgba(212, 175, 55, 0.15);
        }
        .stat-icon { 
            width: 70px; height: 70px; background: var(--primary-red); color: white; 
            border-radius: 16px; display: flex; align-items: center; justify-content: center; 
            font-size: 1.6rem; margin: 0 auto 15px; 
            box-shadow: 0 6px 15px rgba(139, 30, 36, 0.2);
        }
        .map-container { 
            border-radius: 28px; overflow: hidden; border: 10px solid white; 
            box-shadow: 0 20px 45px rgba(0,0,0,0.08); cursor: pointer; 
            transition: all 0.4s ease; border: 1px solid #E2E8F0;
        }
        .map-container:hover { transform: translateY(-5px); box-shadow: 0 25px 55px rgba(0,0,0,0.12); }
        
        footer { background-color: var(--text-dark); color: rgba(255,255,255,0.6); padding: 50px 0 30px; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">
                <div class="brand-icon"><i class="fas fa-book-open"></i></div>
                <div class="text-start">
                    <span class="brand-title d-block">PAI Learning</span>
                    <span class="brand-subtitle">Ilmora PAI</span>
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
                    @auth
                        <a href="{{ route('student.dashboard') }}" class="btn-primary-custom px-4">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-primary-custom px-4">Log In</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- 1. Hero Section --}}
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 text-start" data-aos="fade-right">
                    <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill mb-3 fw-bold"><i class="fas fa-star me-1 text-warning"></i> Solat & Study Ecosystem</span>
                    <h1 class="hero-title">Master Your <br> <span style="color: var(--accent-brown)">Islamic Studies</span></h1>
                    <p class="lead text-muted mb-4 pe-lg-4">The ultimate digital platform designed for premium performance in PAI syllabus modules at MRSM Terendak.</p>
                    <a href="{{ route('student.dashboard') }}" class="btn-primary-custom">Get Started <i class="fas fa-arrow-right ms-2"></i></a>
                </div>
                <div class="col-lg-6 mt-5 mt-lg-0" data-aos="fade-left">
                    <div class="hero-img-container">
                        <img src="{{ asset('image/BoardMRSM.png') }}" class="hero-image" alt="MRSM Terendak Entrance">
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 2. Tools Section --}}
    <section id="features" class="feature-section">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="fw-bold display-6">Integrated Learning Tools</h2>
                <p class="text-muted">A comprehensive set of tools to aid your academic journey.</p>
            </div>
            <div class="row g-4 text-start">
                @php
                    $features = [
                        ['icon' => 'far fa-calendar-alt', 'bg' => '#8B1E24', 'title' => 'Timetable', 'desc' => 'Organize your study hours and stay on top of your class schedules.', 'route' => 'student.timetable.view'],
                        ['icon' => 'far fa-user', 'bg' => '#C05621', 'title' => 'Profile', 'desc' => 'Track your personal information, achievements, and account settings.', 'route' => 'student.profile.show'],
                        ['icon' => 'far fa-comment-dots', 'bg' => '#D93025', 'title' => 'Messages', 'desc' => 'Directly communicate with your teachers for instant learning support.', 'route' => 'student.messages.index'],
                        ['icon' => 'far fa-folder-open', 'bg' => '#00897B', 'title' => 'Resources', 'desc' => 'Access a curated collection of PAI notes and video materials.', 'route' => 'student.resources.index'],
                        ['icon' => 'fas fa-graduation-cap', 'bg' => '#F59E0B', 'title' => 'Flashcards', 'desc' => 'Boost your memorization with subject-specific digital flashcards.', 'route' => 'student.flashcards.index'],
                        ['icon' => 'fas fa-chart-line', 'bg' => '#8B1E24', 'title' => 'Progress', 'desc' => 'Visualize your academic growth and quiz performance over time.', 'route' => 'student.progress.index'],
                    ];
                @endphp
                @foreach($features as $f)
                <div class="col-md-4" data-aos="zoom-in" data-aos-delay="{{ $loop->index * 50 }}">
                    <div class="feature-card shadow-sm">
                        <div class="feature-icon" style="background: {{ $f['bg'] }};"><i class="{{ $f['icon'] }}"></i></div>
                        <h5 class="fw-bold mb-2">{{ $f['title'] }}</h5>
                        <p class="text-muted small mb-4">{{ $f['desc'] }}</p>
                        <a href="{{ route($f['route']) }}" class="text-danger fw-bold text-decoration-none mt-auto">Go to Tool <i class="fas fa-arrow-right ms-2"></i></a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- 3. Quiz Section --}}
    <div class="px-3">
        <section id="quiz" class="quiz-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-5 text-start ps-lg-5" data-aos="fade-right">
                        <h2 class="fw-bold display-5 text-white mb-4">Quiz Center <span style="color: var(--gold-accent)">Pro</span></h2>
                        <p class="lead text-white opacity-75 mb-4">Validate your understanding through interactive tests designed to help you prepare for Form 4 examinations.</p>
                        <a href="{{ route('student.quizzes.index') }}" class="btn btn-light text-danger fw-bold px-5 py-3 rounded-pill shadow-lg">Start Quiz Now</a>
                    </div>
                    <div class="col-lg-6 offset-lg-1 pe-lg-5">
                        <div class="row g-3">
                            @php
                                $quizInfo = ['Al-Quran' => 'Tajwid rules.', 'Hadis' => 'Prophetic teachings.', 'Akidah' => 'Islamic belief.', 'Fiqh' => 'Shariah.', 'Sirah' => 'History.', 'Akhlak' => 'Character.'];
                            @endphp
                            @foreach($quizInfo as $name => $info)
                            <div class="col-md-6" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                                <div class="quiz-card text-start">
                                    <span class="fw-bold text-white d-block mb-1">{{ $name }}</span>
                                    <p class="small text-white-50 mb-0" style="font-size: 0.75rem;">{{ $info }}</p>
                                    <div class="progress-line"><div class="progress-fill" style="width: 15%;"></div></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    {{-- 4. Textbooks --}}
    <section id="textbooks">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="fw-bold display-5">Digital Library</h2>
                <p class="text-muted">High-resolution textbooks available on all your devices.</p>
            </div>
            <div class="row g-4 text-start justify-content-center">
                @php
                    $images = [asset('image/quran_front.jpg'), asset('image/hadith_front.jpg'), asset('image/akidah_front.jpg'), asset('image/fiqh_front.png'), asset('image/sirah_front.jpg'), asset('image/akhlak_front.jpg')];
                @endphp
                @foreach($subjects as $index => $subject)
                <div class="col-md-4 col-lg-2" data-aos="flip-left" data-aos-delay="{{ $loop->index * 100 }}">
                    <div class="textbook-card shadow-sm">
                        <img src="{{ $images[$index % 6] }}" class="textbook-img" alt="{{ $subject->subject_name }}">
                        <div class="textbook-body">
                            <h6 class="fw-bold text-dark text-truncate mb-0" style="font-size: 0.85rem;">{{ $subject->subject_name }}</h6>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="text-center mt-5">
                <a href="{{ route('student.textbooks.index') }}" class="btn-primary-custom px-5 py-3 shadow-lg">Start Reading</a>
            </div>
        </div>
    </section>

    {{-- 5. Upgraded About Section with Maroon Background --}}
    <section id="about" class="about-section" style="background-color: var(--primary-red); padding: 100px 0; border-top: 2px solid var(--gold-accent);">
        <div class="container">
            <div class="row align-items-center g-5" data-aos="fade-up">
                <div class="col-lg-5 text-start">
                    <span class="text-uppercase fw-bold text-white small mb-2 d-block" style="letter-spacing: 2px; color: var(--gold-accent) !important;">Our Heritage</span>
                    <h2 class="fw-bold display-5 mb-4 text-white">About Platform</h2>
                    <p class="text-white opacity-75 mb-4">Developed specifically for students at MRSM Terendak, Melaka. This platform serves as a digital bridge, combining traditional Islamic education with modern learning technology to help you excel in your studies.</p>
                    
                    <div class="row g-4 text-center justify-content-start">
                        <div class="col-5">
                            <div class="stat-icon shadow-sm mb-3" style="background: var(--gold-accent); color: var(--primary-red);"><i class="fas fa-user-graduate"></i></div>
                            <h5 class="fw-bold text-white" style="font-size: 1rem;">Student-Centric</h5>
                        </div>
                        <div class="col-5">
                            <div class="stat-icon shadow-sm mb-3" style="background: var(--gold-accent); color: var(--primary-red);"><i class="fas fa-check-double"></i></div>
                            <h5 class="fw-bold text-white" style="font-size: 1rem;">Interactive</h5>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="map-container" style="border: 5px solid var(--gold-accent); box-shadow: 0 20px 40px rgba(0,0,0,0.3);" onclick="window.open('https://maps.google.com/?q=MRSM+Terendak', '_blank')">
                        <img src="{{ asset('image/MRSM-map.png') }}" class="img-fluid w-100" style="min-height: 350px; object-fit: cover;" alt="Map of MRSM Terendak Location">
                        <div class="p-3 text-center border-top" style="background: var(--gold-accent);">
                            <small class="fw-bold" style="color: var(--primary-red);"><i class="fas fa-hand-pointer me-2"></i>Click to open MRSM Terendak in Google Maps</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="text-center">
        <div class="container border-top pt-4">
            <p class="small">© {{ date('Y') }} PAI Learning Platform. All Rights Reserved.</p>
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
</html>