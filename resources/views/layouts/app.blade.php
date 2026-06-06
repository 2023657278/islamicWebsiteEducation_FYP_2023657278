<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'PAI Learning Platform') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        body {
            /* Friendly, soothing academic pastel gradient vibe */
            background: linear-gradient(135deg, #f6f8fd 0%, #eef2f9 100%);
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            color: #2d3748;
            position: relative;
            overflow-x: hidden;
        }

        /* Decorative academic backdrop elements */
        body::before {
            content: "";
            position: absolute;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.08) 0%, rgba(255,255,255,0) 70%);
            top: -50px;
            right: -50px;
            z-index: -1;
        }
        body::after {
            content: "";
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.06) 0%, rgba(255,255,255,0) 70%);
            bottom: -100px;
            left: -100px;
            z-index: -1;
        }

        /* Clean, White Classroom Navbar */
        .edu-navbar {
            background: rgba(255, 255, 255, 0.85) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid #e2e8f0;
        }

        .edu-navbar .navbar-brand {
            color: #1e293b !important;
            font-weight: 700;
            font-size: 0.9rem;
            letter-spacing: 0.3px;
        }

        .edu-navbar .nav-link {
            color: #475569 !important;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 8px 16px !important;
            transition: all 0.2s;
        }

        .edu-navbar .nav-link:hover {
            color: #3b82f6 !important;
        }

        /* Soft Friendly Classroom Cards */
        .edu-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 20px !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.03) !important;
        }

        /* Clean, Friendly Input Fields with Soft Labeling */
        .edu-input-group .form-control {
            background-color: #f8fafc !important;
            border: 1px solid #cbd5e1 !important;
            color: #1e293b !important;
            border-radius: 12px;
            padding: 14px 16px;
            height: auto;
            font-size: 0.95rem;
            transition: all 0.2s ease-in-out;
        }

        .edu-input-group .form-control:focus {
            background-color: #ffffff !important;
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12) !important;
        }

        .edu-input-group label {
            color: #64748b !important;
            padding: 14px 16px;
        }

        .edu-input-group .form-control:focus ~ label,
        .edu-input-group .form-control:not(:placeholder-shown) ~ label {
            color: #3b82f6 !important;
            transform: scale(0.85) translateY(-0.6rem) translateX(0.15rem);
        }
    </style>
</head>
<body>
    <div id="app" class="d-flex flex-column min-vh-100">
        <nav class="navbar navbar-expand-md navbar-light edu-navbar shadow-sm py-3">
            <div class="container">
                <a class="navbar-brand text-uppercase" href="{{ url('/') }}">
                    <i class="fas fa-graduation-cap text-primary mr-2"></i> {{ __('PAI Learning Home') }}
                </a>
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto align-items-center">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('login') ? 'text-primary font-weight-bold' : '' }}" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item ms-md-2">
                                    <a class="nav-link btn btn-primary text-white px-4 shadow-sm" style="border-radius: 10px; background-color: #3b82f6; border: none;" href="{{ route('register') }}">{{ __('Get Started') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle font-weight-bold text-dark" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <i class="fas fa-user-circle text-primary mr-1"></i> {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2" style="border-radius: 12px;" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item rounded py-2 text-secondary" href="{{ route('logout') }}"
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

        <main class="flex-grow-1 d-flex align-items-center py-5">
            @yield('content')
        </main>
    </div>
</body>
</html>