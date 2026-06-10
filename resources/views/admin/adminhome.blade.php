<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MRSM Terendak | Teacher Portal</title>

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

    /* 🟢 FIXED: Removed white opaque background to let your main asset show behind */
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
    }

    .brand-link {
      background-color: transparent !important;
      border-bottom: 1px solid rgba(255,255,255,0.1) !important;
      padding: 1.5rem 1rem;
    }

    /* Rectangular Profile Image Styling */
    .user-panel {
        border-bottom: 1px solid rgba(255,255,255,0.05) !important;
        margin: 15px 10px;
        padding: 0;
        text-align: center;
    }

    .sidebar-profile-img {
        width: 100% !important;
        max-width: 180px !important; 
        height: 120px !important;    
        object-fit: cover !important; 
        border-radius: 12px !important; 
        border: 3px solid rgba(255,255,255,0.2) !important;
        display: block !important;
        margin: 10px auto !important;
        box-shadow: 0 4px 8px rgba(0,0,0,0.3) !important;
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

    /* 🟢 FIXED: Translucent frosted glass look for the main navbar link bar */
    .main-header {
      background: rgba(255, 255, 255, 0.75) !important;
      backdrop-filter: blur(15px);
      -webkit-backdrop-filter: blur(15px);
      border-bottom: 1px solid rgba(0, 0, 0, 0.06) !important;
    }
  </style>
</head>

<body class="hold-transition layout-fixed layout-navbar-fixed">
<div class="wrapper">

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
    <a href="{{ route('admin.dashboard') }}" class="brand-link d-flex align-items-center">
      
      <!-- 🟢 CHANGED: Removed restricting classes and added border-radius: 50% with clean dimension constraints -->
      <img src="{{ asset('admin/dist/img/Ilmora.png') }}" alt="Logo" 
           style="opacity: 0.9; width: 120px; height: 120px; min-width: 60px; border-radius: 50%; object-fit: cover;">
      
      <span class="brand-text font-weight-bold text-white ml-3">Ilmora <span style="color: #fbbf24;">PAI</span></span>
    </a>

    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 border-0">
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