<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MRSM Terendak | Teacher Portal</title>
  <!-- 🟢 Website Browser Tab Icon (Favicon) -->
  <link rel="icon" type="image/png" href="{{ asset('image/logo-badge.png') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Amiri:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="{{ asset('admin/plugins/fontawesome-free/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('admin/dist/css/adminlte.min.css') }}">
  <link rel="stylesheet" href="{{ asset('admin/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  

  <style>
    /* --- THEME NUR CONFIGURATION --- */
    :root {
      --bg-sidebar: #4a0404; /* Deep Maroon */
      --sidebar-text: #e5e5e5;
      --sidebar-active-bg: rgba(255, 193, 7, 0.2);
      --sidebar-active-text: #fbbf24;
      --text-main: #292524;
    }

    body {
      font-family: 'Inter', sans-serif;
      color: var(--text-main);
      background-image: linear-gradient(rgba(253, 251, 247, 0.35), rgba(253, 251, 247, 0.35)), url('{{ asset("admin/dist/img/pngbgimage.png") }}');
      background-repeat: no-repeat;
      background-position: center center;
      background-attachment: fixed;
      background-size: cover; 
    }

    .content-wrapper {
      background: transparent !important;
      min-height: 100vh;
    }
    
    .website-container { max-width: 1400px; margin: 0 auto; padding: 30px; }

    /* --- SIDEBAR CUSTOMIZATION --- */
    .main-sidebar {
      background: var(--bg-sidebar) !important;
      box-shadow: none !important;
      border-right: 1px solid rgba(0,0,0,0.1);
      display: flex;
      flex-direction: column;
      height: 100vh !important;
      position: fixed;
    }

    /* Centers the whole logo + text group and keeps it static at the top */
    .brand-link {
      background-color: var(--bg-sidebar) !important; 
      border-bottom: 1px solid rgba(255,255,255,0.1) !important;
      padding: 1.2rem 1rem !important;
      display: flex !important;
      flex-direction: row !important;
      align-items: center !important;
      justify-content: center !important; 
      height: 70px !important;
    }

    .custom-sidebar-logo {
      width: 45px !important;
      height: 45px !important;
      object-fit: cover !important;
      border-radius: 50% !important;
      border: 2px solid rgba(255,255,255,0.2) !important;
      box-shadow: 0 4px 6px rgba(0,0,0,0.15) !important;
      margin-right: 10px !important;
      margin-left: 0 !important;
      margin-bottom: 0 !important;
    }

    /* 🟢 FIXED: Removed strict overflows and set height to auto to prevent any clipping */
    .user-panel {
        border-bottom: 1px solid rgba(255,255,255,0.05) !important;
        margin: 90px 10px 15px 10px !important; 
        padding: 0 !important;
        text-align: center !important;
        display: block !important;
        height: auto !important;         /* 👈 Overrides AdminLTE's fixed pixel max-height cap */
        min-height: 30px !important;    /* 👈 Ensures full structural clearance for the 120px cat profile image */
        overflow: visible !important;    /* 👈 Prevents the container bounding box from cutting off the image */
    }

    /* SCROLLABLE LIST: Only navigation links scroll now */
    .sidebar {
      flex: 1;
      overflow-y: auto;
      padding-top: 10px !important; 
    }

    /* 🟢 FIXED: Added clear display block parameters to guarantee the picture renders unconstrained */
    .sidebar-profile-img {
        width: 180px !important; 
        height: 120px !important;    
        object-fit: cover !important; 
        border-radius: 12px !important; 
        border: 3px solid rgba(255,255,255,0.2) !important;
        display: inline-block !important;
        margin: 0 auto !important;
        box-shadow: 0 4px 8px rgba(0,0,0,0.3) !important;
        float: none !important;
    }

    .sidebar-profile-placeholder {
        width: 180px;
        height: 120px;
        background: linear-gradient(135deg, #7f1d1d, #b91c1c);
        color: white;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        font-weight: bold;
        margin: 0 auto;
    }

    /* Nav Items */
    .nav-sidebar .nav-item { margin-bottom: 2px; }
    .nav-sidebar .nav-link {
      color: var(--sidebar-text) !important;
      border-radius: 8px !important;
      padding: 12px 15px;
      font-weight: 400;
      transition: all 0.2s ease;
    }
    .nav-sidebar .nav-link:hover {
      background-color: rgba(255,255,255,0.08) !important;
      color: #fff !important;
      padding-left: 20px;
    }
    .nav-sidebar .nav-link.active {
      background-color: var(--sidebar-active-bg) !important;
      color: var(--sidebar-active-text) !important;
      font-weight: 600;
      box-shadow: none !important;
    }
    .nav-sidebar .nav-link.active i { color: var(--sidebar-active-text) !important; }
    .nav-header {
      color: #a3a3a3 !important;
      font-size: 0.7rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      padding: 1.5rem 1rem 0.5rem;
      opacity: 0.7;
    }

    .main-header {
      background: rgba(255, 255, 255, 0.75) !important;
      backdrop-filter: blur(15px);
      -webkit-backdrop-filter: blur(15px);
      border-bottom: 1px solid rgba(0, 0, 0, 0.06) !important;
    }

    /* 🖥️ DESKTOP VIEW: Bottom bar completely invisible */
    @media (min-width: 768px) {
      .mobile-teacher-nav {
        display: none !important;
      }
    }

    /* 📱 MOBILE RESPONSIVE TUNING */
    @media (max-width: 767px) {
      .main-sidebar {
        display: none !important; /* Strips desktop sidebar framework */
      }

      .main-header {
        margin-left: 0 !important;
        padding: 5px 10px !important;
      }

      .main-header .nav-link[data-widget="pushmenu"] {
        display: none !important; /* Hides pushmenu icon since sidebar is inactive */
      }

      .content-wrapper {
        margin-left: 0 !important;
        padding-bottom: 85px !important; /* Prevents text elements from slipping underneath bottom bar */
      }

      .website-container {
        padding: 15px 10px !important;
      }

      /* Bottom App Layout Styles */
      .mobile-teacher-nav {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        background-color: #4a0404; /* Matches Teacher Portal deep maroon theme */
        display: flex !important;
        justify-content: space-around;
        align-items: center;
        padding: 8px 0;
        z-index: 9999;
        box-shadow: 0 -2px 12px rgba(0, 0, 0, 0.2);
      }

      .mobile-teacher-item {
        color: #e5e5e5 !important;
        display: flex;
        flex-direction: column;
        align-items: center;
        font-size: 10px;
        font-weight: 500;
        text-decoration: none !important;
        transition: 0.2s;
      }

      .mobile-teacher-item i {
        font-size: 16px;
        margin-bottom: 3px;
      }

      .mobile-teacher-item.active {
        color: #fbbf24 !important; /* Gold selection state color */
      }
    }
  </style>
</head>

<body class="hold-transition layout-fixed layout-navbar-fixed">
<div class="wrapper">

  <!-- 📱 Mobile App Bottom Navbar -->
  <div class="mobile-teacher-nav d-block d-md-none">
    <a href="{{ route('admin.dashboard') }}" class="mobile-teacher-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
      <i class="fas fa-th-large"></i>
      <span>Dashboard</span>
    </a>
    <a href="{{ route('messages.index') }}" class="mobile-teacher-item {{ request()->routeIs('messages.*') ? 'active' : '' }}">
      <i class="fas fa-comment-alt"></i>
      <span>Messages</span>
    </a>
    <a href="{{ route('quizzes.index') }}" class="mobile-teacher-item {{ request()->routeIs('quizzes.*') ? 'active' : '' }}">
      <i class="fas fa-question-circle"></i>
      <span>Quizzes</span>
    </a>
    <a href="{{ route('resources.index') }}" class="mobile-teacher-item {{ request()->routeIs('resources.*') ? 'active' : '' }}">
      <i class="fas fa-folder"></i>
      <span>Resources</span>
    </a>
    <a href="#" class="mobile-teacher-item text-danger" onclick="event.preventDefault(); document.getElementById('mobile-teacher-logout').submit();">
      <i class="fas fa-power-off"></i>
      <span>Exit</span>
    </a>
  </div>

  <!-- Mobile Form Anchor Link Logic -->
  <form id="mobile-teacher-logout" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>

  <nav class="main-header navbar navbar-expand navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars text-dark"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <span class="nav-link font-weight-bold" style="font-family: 'Amiri', serif; font-size: 1.3rem; color: #5b1a1a;">Teacher Portal</span>
      </li>
    </ul>

    <ul class="navbar-nav ml-auto">
      @auth
      <li class="nav-item dropdown">
          <a class="nav-link d-flex align-items-center" data-toggle="dropdown" href="#">
             @if(Auth::user()->profile_image)
               <img src="{{ asset('storage/profile_images/' . Auth::user()->profile_image) }}" class="img-circle border" style="width: 35px; height: 35px; object-fit: cover;">
             @else
               <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center text-dark font-weight-bold" style="width: 35px; height: 35px;">
                 {{ substr(Auth::user()->name, 0, 1) }}
               </div>
             @endif
             <span class="ml-2 d-none d-md-inline font-weight-bold text-dark">{{ Auth::user()->name }}</span>
          </a>
          <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right mt-2 shadow-lg border-0 rounded-lg">
             <div class="px-4 py-3 bg-light border-bottom">
                 <p class="mb-0 font-weight-bold text-dark">{{ Auth::user()->name }}</p>
                 <p class="text-xs text-muted mb-0">{{ Auth::user()->email }}</p>
             </div>
             <a href="{{ route('profile.show') }}" class="dropdown-item py-2"><i class="fas fa-user mr-2 text-muted"></i> My Profile</a>
             <a href="{{ route('profile.edit') }}" class="dropdown-item py-2"><i class="fas fa-cog mr-2 text-muted"></i> Edit Profile</a>
             <div class="dropdown-divider m-0"></div>
             <a class="dropdown-item text-danger py-2" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                 <i class="fas fa-sign-out-alt mr-2"></i> Sign Out
             </a>
             <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
          </div>
      </li>
      @endauth
    </ul>
  </nav>

  <aside class="main-sidebar elevation-0">
    <a href="{{ route('admin.dashboard') }}" class="brand-link">
      <img src="{{ asset('admin/dist/img/Ilmora.png') }}" alt="Logo" class="custom-sidebar-logo">
      <span class="brand-text font-weight-bold text-white fs-5">Ilmora <span style="color: #fbbf24;">PAI</span></span>
    </a>

    <!-- LOCKED PANEL: The profile image block is outside the scroll container -->
    <div class="user-panel">
      @if(Auth::user()->profile_image)
          <img src="{{ asset('storage/profile_images/' . Auth::user()->profile_image) }}" 
               class="sidebar-profile-img" 
               alt="User Image">
      @else
          <div class="sidebar-profile-placeholder">
              {{ substr(Auth::user()->name, 0, 1) }}
          </div>
      @endif
    </div>

    <!-- SCROLLABLE LIST: Only navigation links scroll now -->
    <div class="sidebar">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
          
          <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
              <i class="nav-icon fas fa-th-large"></i> <p>Dashboard</p>
            </a>
          </li>

          <li class="nav-header">COMMUNICATION</li>
          <li class="nav-item">
            <a href="{{ route('messages.index') }}" class="nav-link {{ request()->routeIs('messages.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-comment-alt"></i> <p>Messages</p>
            </a>
          </li>

          <li class="nav-header">MANAGEMENT</li>
          <li class="nav-item {{ request()->routeIs('students.*') || request()->routeIs('teachers.*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->routeIs('students.*') || request()->routeIs('teachers.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-users"></i>
              <p>People <i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview pl-3">
              <li class="nav-item"><a href="{{ route('students.index') }}" class="nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}"><i class="far fa-circle nav-icon text-xs"></i> <p>Students</p></a></li>
              <li class="nav-item"><a href="{{ route('teachers.index') }}" class="nav-link {{ request()->routeIs('teachers.*') ? 'active' : '' }}"><i class="far fa-circle nav-icon text-xs"></i> <p>Teachers</p></a></li>
            </ul>
          </li>

          <li class="nav-item {{ request()->routeIs('subjects.*') || request()->routeIs('groups.*') || request()->routeIs('timetables.*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->routeIs('subjects.*') || request()->routeIs('groups.*') || request()->routeIs('timetables.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-chalkboard"></i>
              <p>Academic <i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview pl-3">
              <li class="nav-item"><a href="{{ route('groups.index') }}" class="nav-link {{ request()->routeIs('groups.*') ? 'active' : '' }}"><i class="far fa-circle nav-icon text-xs"></i> <p>Classes</p></a></li>
              <li class="nav-item"><a href="{{ route('subjects.index') }}" class="nav-link {{ request()->routeIs('subjects.*') ? 'active' : '' }}"><i class="far fa-circle nav-icon text-xs"></i> <p>Subjects</p></a></li>
              <li class="nav-item"><a href="{{ route('timetables.index') }}" class="nav-link {{ request()->routeIs('timetables.*') ? 'active' : '' }}"><i class="far fa-circle nav-icon text-xs"></i> <p>Timetables</p></a></li>
            </ul>
          </li>

          <li class="nav-header">CONTENT</li>
          <li class="nav-item"><a href="{{ route('resources.index') }}" class="nav-link {{ request()->routeIs('resources.*') ? 'active' : '' }}"><i class="nav-icon fas fa-folder"></i> <p>Resources</p></a></li>
          <li class="nav-item"><a href="{{ route('quizzes.index') }}" class="nav-link {{ request()->routeIs('quizzes.*') ? 'active' : '' }}"><i class="nav-icon fas fa-question-circle"></i> <p>Quizzes</p></a></li>
          <li class="nav-item"><a href="{{ route('flashcards.index') }}" class="nav-link {{ request()->routeIs('flashcards.*') ? 'active' : '' }}"><i class="nav-icon fas fa-layer-group"></i> <p>Flashcards</p></a></li>
          <li class="nav-item"><a href="{{ route('results.index') }}" class="nav-link {{ request()->routeIs('teacher.results.*') ? 'active' : '' }}"><i class="nav-icon fas fa-poll"></i> <p>Results</p></a></li>

        </ul>
      </nav>
    </div>
  </aside>

  <div class="content-wrapper">
    <section class="content pt-4">
      <div class="website-container" data-aos="fade-up" data-aos-duration="800">
         @yield('content')
      </div>
    </section>
  </div>
</div>

<script src="{{ asset('admin/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('admin/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('admin/dist/js/adminlte.js') }}"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  $(document).ready(function() {
      AOS.init({ duration: 800, easing: 'ease-out-cubic', once: true });
  });
</script>
</body>
</html>