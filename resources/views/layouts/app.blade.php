<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'PAI Platform') }}</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        body {
            background-image: linear-gradient(rgba(10, 10, 12, 0.75), rgba(10, 10, 12, 0.75)), url('/image/loginbg.jpg');
            background-size: cover !important;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: 'Nunito', sans-serif;
            min-height: 100vh;
        }

        /* Frosted Glass Floating Navbar styling */
        .custom-navbar {
            background: rgba(255, 255, 255, 0.06) !important;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .custom-navbar .navbar-brand {
            color: #ffffff !important;
            font-weight: 700;
            letter-spacing: 0.5px;
            font-size: 0.95rem;
            transition: opacity 0.2s;
        }

        .custom-navbar .navbar-brand:hover {
            opacity: 0.8;
        }

        .custom-navbar .nav-link {
            color: rgba(255, 255, 255, 0.75) !important;
            font-weight: 600;
            font-size: 0.9rem;
            transition: color 0.2s;
        }

        .custom-navbar .nav-link:hover {
            color: #ffffff !important;
        }

        /* Glassmorphism Card Style Utility */
        .glass-card {
            background: rgba(255, 255, 255, 0.07) !important;
            backdrop-filter: blur(16px) saturate(120%);
            -webkit-backdrop-filter: blur(16px) saturate(120%);
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            border-radius: 16px !important;
        }

        /* Clean Customized Form Fields */
        .glass-input-group .form-control {
            background-color: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
            color: #ffffff !important;
            border-radius: 10px;
            transition: all 0.2s ease-in-out;
        }

        .glass-input-group .form-control:focus {
            background-color: rgba(255, 255, 255, 0.09) !important;
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.25) !important;
        }

        .glass-input-group label {
            color: rgba(255, 255, 255, 0.5) !important;
        }

        .glass-input-group .form-control:focus ~ label,
        .glass-input-group .form-control:not(:placeholder-shown) ~ label {
            color: #3b82f6 !important;
            transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
        }
    </style>
</head>
<body>
    <div id="app" class="d-flex flex-column min-height-100vh">
        <nav class="navbar navbar-expand-md navbar-dark custom-navbar shadow-sm py-3">
            <div class="container">
                <a class="navbar-brand text-uppercase" href="{{ url('/') }}">
                    <i class="fas fa-arrow-left mr-2 small"></i> {{ __('Back to home') }}
                </a>
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto align-items-md-center">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item px-1">
                                    <a class="nav-link {{ request()->routeIs('login') ? 'text-white font-weight-bold' : '' }}" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item px-1">
                                    <a class="nav-link {{ request()->routeIs('register') ? 'text-white font-weight-bold' : '' }}" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle text-white font-weight-bold" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <i class="fas fa-user-circle mr-1"></i> {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end bg-dark border-secondary p-2" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item text-light rounded" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt mr-2 text-danger"></i> {{ __('Logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="flex-grow-1 d-flex align-items-center py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>