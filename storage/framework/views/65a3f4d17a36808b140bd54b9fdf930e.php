<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>System Admin | Root Control</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Amiri:wght@400;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="<?php echo e(asset('admin/plugins/fontawesome-free/css/all.min.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('admin/dist/css/adminlte.min.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('admin/plugins/overlayScrollbars/css/OverlayScrollbars.min.css')); ?>">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

  <style>
    :root {
      --admin-bg: #0a0a0c;
      --sidebar-dark: #121214;
      --accent-blue: #3b82f6;
      --text-dim: #94a3b8;
    }

    body {
      font-family: 'Inter', sans-serif;
      background-color: var(--admin-bg);
      color: #f8fafc;
    }

    .content-wrapper {
      background: var(--admin-bg) !important;
      min-height: 100vh;
    }

    .main-sidebar {
      background: var(--sidebar-dark) !important;
      border-right: 1px solid #27272a !important;
    }

    .nav-sidebar .nav-link {
      color: var(--text-dim) !important;
      border-radius: 6px !important;
      margin: 2px 10px;
      transition: all 0.3s ease;
    }

    .nav-sidebar .nav-link:hover {
      background-color: rgba(255,255,255,0.05) !important;
      color: #fff !important;
    }

    .nav-sidebar .nav-link.active {
      background-color: rgba(59, 130, 246, 0.1) !important;
      color: var(--accent-blue) !important;
      font-weight: 600;
    }

    .main-header {
      background: var(--sidebar-dark) !important;
      border-bottom: 1px solid #27272a !important;
    }

    .main-header .nav-link { color: var(--text-dim) !important; }
    
    .text-muted-dark { color: #64748b !important; }
    .modal-content input:focus {
        background-color: #1e1e24 !important;
        color: #fff !important;
        border-color: var(--accent-blue) !important;
        box-shadow: none !important;
    }
  </style>
</head>

<body class="hold-transition layout-fixed layout-navbar-fixed dark-mode">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-dark">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <span class="nav-link font-weight-bold" style="color: var(--accent-blue); letter-spacing: 1px;">ADMIN CONSOLE</span>
      </li>
    </ul>
    
    <ul class="navbar-nav ml-auto">
      <li class="nav-item dropdown">
          <a class="nav-link" data-toggle="dropdown" href="#" style="cursor: pointer;">
             <span class="font-weight-bold mr-2 text-light"><?php echo e(Auth::user()->name); ?></span>
             <i class="fas fa-user-shield text-primary"></i>
          </a>
          <div class="dropdown-menu dropdown-menu-right bg-dark border-secondary p-2 shadow-lg" style="min-width: 220px;">
             <a class="dropdown-item text-light rounded py-2" href="#" data-toggle="modal" data-target="#adminProfileModal">
                <i class="fas fa-user-cog mr-2 text-info"></i> Edit My Profile
             </a>
             <div class="dropdown-divider border-secondary"></div>
             <a class="dropdown-item text-danger rounded py-2" href="<?php echo e(route('logout')); ?>" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-power-off mr-2"></i> Log Out
             </a>
             <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none"><?php echo csrf_field(); ?></form>
          </div>
      </li>
    </ul>
  </nav>

  <!-- Sidebar -->
  <aside class="main-sidebar">
    <a href="<?php echo e(route('adminreal.dashboard')); ?>" class="brand-link text-center">
      <span class="brand-text font-weight-bold text-white">SYSTEM <span style="color: var(--accent-blue);">ROOT</span></span>
    </a>

    <div class="sidebar">
      <div class="user-panel">
        
        <div class="text-center p-4 bg-dark rounded m-2 border border-secondary text-primary" style="background-color: #1a1a1e !important;">
            <i class="fas fa-terminal fa-3x"></i>
        </div>
      </div>

      <nav class="mt-4">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
          <li class="nav-item">
            <a href="<?php echo e(route('adminreal.dashboard')); ?>" class="nav-link <?php echo e(request()->routeIs('adminreal.dashboard') ? 'active' : ''); ?>">
              <i class="nav-icon fas fa-th-large"></i> 
              <p>Dashboard</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="<?php echo e(route('teachers.index')); ?>" class="nav-link <?php echo e(request()->routeIs('teachers.*') ? 'active' : ''); ?>">
                <i class="nav-icon fas fa-chalkboard-teacher"></i> 
                <p>Teachers</p>
            </a>
          </li>

          <li class="nav-item mt-5">
            <a href="<?php echo e(route('logout')); ?>" class="nav-link text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="nav-icon fas fa-sign-out-alt"></i> 
                <p>Exit Console</p>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </aside>

  <div class="content-wrapper">
    <section class="content pt-4">
      <div class="container-fluid" data-aos="fade-in" data-aos-duration="500">
          <?php echo $__env->yieldContent('content'); ?>
      </div>
    </section>
  </div>
</div>

<!-- Profile Modal (Text Only) -->
<div class="modal fade" id="adminProfileModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content bg-dark text-light border border-secondary" style="border-radius: 12px; background-color: #121214 !important;">
            <div class="modal-header border-bottom border-secondary">
                <h5 class="modal-title font-weight-bold text-primary"><i class="fas fa-sliders-h mr-2"></i> Update Admin Credentials</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form action="<?php echo e(route('teachers.update', Auth::user()->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="text-xs text-muted font-weight-bold text-uppercase mb-1">Username</label>
                        <input type="text" name="name" class="form-control bg-transparent text-white border-secondary text-sm" value="<?php echo e(Auth::user()->name); ?>" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="text-xs text-muted font-weight-bold text-uppercase mb-1">Email Address</label>
                        <input type="email" name="email" class="form-control bg-transparent text-white border-secondary text-sm" value="<?php echo e(Auth::user()->email); ?>" required>
                    </div>

                    <hr style="border-color: #27272a;" class="my-4">

                    <div class="form-group mb-3">
                        <label class="text-xs text-muted font-weight-bold text-uppercase mb-1">New Password <span class="text-xs font-weight-normal text-muted-dark text-lowercase">(leave blank to keep current)</span></label>
                        <input type="password" name="password" class="form-control bg-transparent text-white border-secondary text-sm" placeholder="••••••••">
                    </div>

                    <div class="form-group mb-2">
                        <label class="text-xs text-muted font-weight-bold text-uppercase mb-1">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control bg-transparent text-white border-secondary text-sm" placeholder="••••••••">
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary bg-transparent">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?php echo e(asset('admin/plugins/jquery/jquery.min.js')); ?>"></script>
<script src="<?php echo e(asset('admin/plugins/bootstrap/js/bootstrap.bundle.min.js')); ?>"></script>
<script src="<?php echo e(asset('admin/dist/js/adminlte.js')); ?>"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  $(document).ready(function() { AOS.init(); });
</script>
</body>
</html><?php /**PATH C:\laragon\www\islamicWebsiteEducation_FYP_2023657278\resources\views/adminreal/master.blade.php ENDPATH**/ ?>