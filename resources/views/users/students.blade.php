<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard | PAI Platform</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-red: #8B1E24; 
            --accent-orange: #C05621;
            --bg-cream: #FDFBF7;
            --sidebar-width: 260px;
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--bg-cream); }
        
        /* SIDEBAR STYLING */
        .sidebar {
            width: var(--sidebar-width); height: 100vh; position: fixed; top: 0; left: 0;
            background: white; border-right: 1px solid #eee; padding: 25px; z-index: 1000;
            display: flex; flex-direction: column;
        }
        .logo-section { display: flex; align-items: center; gap: 12px; margin-bottom: 40px; }
        .logo-icon { background: var(--primary-red); color: white; padding: 10px; border-radius: 10px; font-size: 1.2rem; }
        
        .nav-link {
            color: #64748B; font-weight: 600; padding: 12px 15px; border-radius: 10px;
            margin-bottom: 5px; display: flex; align-items: center; gap: 12px; transition: 0.3s;
        }
        .nav-link:hover, .nav-link.active { background-color: #FFF5F5; color: var(--primary-red); }
        
        .sidebar-footer { margin-top: auto; padding-top: 20px; border-top: 1px solid #eee; }
        .logout-btn {
            width: 100%; text-align: left; background: none; border: none; color: #dc3545;
            font-weight: 600; padding: 10px 15px; border-radius: 10px; transition: 0.3s;
            display: flex; align-items: center; gap: 10px;
        }
        .logout-btn:hover { background-color: #FFF5F5; color: #b02a37; }
        
        /* CONTENT STYLING */
        .main-content { margin-left: var(--sidebar-width); padding: 30px; }
        .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .search-bar input { border: 1px solid #eee; border-radius: 50px; padding: 10px 20px; width: 350px; }
        
        .profile-section { display: flex; align-items: center; gap: 15px; }
        
        /* ✅ AVATAR STYLE - Ensures Image Fits Perfectly */
        .avatar {
            width: 40px; height: 40px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: bold; color: white;
            background-color: #008f78; /* Green theme */
            overflow: hidden; /* Clips the image */
            border: 2px solid #008f78;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="logo-section">
            <div class="logo-icon"><i class="fas fa-book-open"></i></div>
            <div>
                <div style="font-weight: 800; color: #1A202C;">PAI Platform</div>
                <div style="font-size: 0.75rem; color: #888;">MRSM Terendak</div>
            </div>
        </div>

        <nav class="nav flex-column mb-auto">
            <a class="nav-link {{ Route::is('student.homepage') ? 'active' : '' }}" href="{{ route('student.homepage') }}"><i class="fas fa-home"></i> Homepage</a>
            <a class="nav-link {{ Route::is('student.dashboard') ? 'active' : '' }}" href="{{ route('student.dashboard') }}"><i class="fas fa-th-large"></i> Dashboard</a>
            <a class="nav-link {{ Route::is('student.quizzes.index') ? 'active' : '' }}" href="{{ route('student.quizzes.index') }}"><i class="fas fa-brain"></i> Quiz</a>
            <a class="nav-link {{ Route::is('student.textbooks.index') ? 'active' : '' }}" href="{{ route('student.textbooks.index') }}"><i class="fas fa-book"></i> Textbooks</a>
            <a class="nav-link {{ Route::is('student.ranking') ? 'active' : '' }}" href="{{ route('student.ranking') }}"><i class="fas fa-crown"></i> Leaderboard</a>
        </nav>
        
        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Log Out</button>
            </form>
        </div>
    </div>

    <div class="main-content">
        <div class="topbar">
            <div class="search-bar"><input type="text" placeholder="Search topics..."></div>
            
            <div class="profile-section">
                <i class="far fa-bell fa-lg text-muted"></i>
                
                <div class="text-end d-none d-md-block">
                    <div style="font-weight: 700; font-size: 0.9rem;">{{ Auth::user()->name }}</div>
                    <div style="font-size: 0.8rem; color: #888;">Form 4 Student</div>
                </div>
                
                <a href="{{ route('student.profile.show') }}" class="text-decoration-none">
                    <div class="avatar">
                        @if(Auth::user()->profile_image)
                            {{-- Show Uploaded Image --}}
                            <img src="{{ asset('storage/' . Auth::user()->profile_image) }}" alt="User" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            {{-- Show Initials --}}
                            {{ substr(Auth::user()->name, 0, 2) }}
                        @endif
                    </div>
                </a>
            </div>
        </div>

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    @stack('scripts')
</body>
</html>