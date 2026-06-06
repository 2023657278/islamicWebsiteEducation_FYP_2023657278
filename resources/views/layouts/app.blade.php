<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'PAI Platform') }}</title>

    <!-- Fonts & FontAwesome Icons -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        /* 🖼️ ORIGINAL IMAGE BACKGROUND RETAINED WITH DARK FILTER */
        body {
            background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('/image/loginbg.jpg');
            background-size: 100% 100%;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: 'Nunito', sans-serif;
            min-vh: 100vh;
        }

        /* 🖤 SLEEK SOLID BLACK HEADER DESIGN */
        .black-header {
            background-color: #000000 !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5) !important;
        }

        .black-header .navbar-brand {
            color: #ffffff !important;
            font-weight: 800;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
        }

        .black-header .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            font-weight: 600;
            font-size: 0.9rem;
            transition: color 0.2s;
        }

        .black-header .nav-link:hover {
            color: #3b82f6 !important;
        }

        /* 🌈 RAINBOW BORDER LINE EFFECT FOR LOGIN/REGISTER BOXES */
        .rainbow-card-wrapper {
            position: relative;
            padding: 4px; /* Thickness of the rainbow line */
            background: linear-gradient(124deg, #ff2400, #e81d1d, #e8b71d, #1de840, #1ddde8, #2b1de8, #dd1de8);
            background-size: 400% 400%;
            animation: moveRainbowBorder 6s linear infinite;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        @keyframes moveRainbowBorder {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .rainbow-card-wrapper .card {
            border: none !important;
            border-radius: 16px !important;
            background: #ffffff !important;
        }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-dark black-header shadow-sm py-3">
            <div class="container">
                <!-- 🟢 CHANGED: Replaced 'Back to home' with 'MRSM PAI' -->
                <a class="navbar-brand text-uppercase" href="{{ url('/') }}">
                    {{ __('MRSM PAI') }}
                </a>
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle text-white font-weight-bold" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end border-0 p-2 shadow-lg" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item rounded" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-5">
            @yield('content')
        </main>
    </div>
</body>
</html>