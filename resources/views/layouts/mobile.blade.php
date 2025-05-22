<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title') - {{ config('app.name', 'Exam Seat Management System') }}</title>
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #000080;
            --secondary-color: #4a5568;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            padding-bottom: 70px; /* For bottom navbar */
        }
        
        .navbar {
            background-color: var(--primary-color);
        }
        
        .navbar-brand {
            font-weight: bold;
            color: white !important;
        }
        
        .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
        }
        
        .nav-link.active {
            color: white !important;
            font-weight: bold;
        }
        
        .bottom-nav {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: white;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }
        
        .bottom-nav .nav-item {
            flex: 1;
            text-align: center;
        }
        
        .bottom-nav .nav-link {
            color: var(--secondary-color) !important;
            padding: 0.5rem 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 0.8rem;
        }
        
        .bottom-nav .nav-link i {
            font-size: 1.2rem;
            margin-bottom: 0.2rem;
        }
        
        .bottom-nav .nav-link.active {
            color: var(--primary-color) !important;
        }
        
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        
        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            font-weight: bold;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #000066;
            border-color: #000066;
        }
        
        .badge-primary {
            background-color: var(--primary-color);
            color: white;
        }
        
        .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 0.2rem 0.5rem;
            font-size: 0.7rem;
        }
        
        .profile-picture {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary-color);
        }
        
        .exam-card {
            border-left: 4px solid var(--primary-color);
        }
        
        .notification-card {
            transition: all 0.3s ease;
        }
        
        .notification-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        
        .unread {
            background-color: rgba(0, 0, 128, 0.05);
            font-weight: bold;
        }
        
        /* Custom CSS for specific pages */
        @yield('custom-css')
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('mobile.dashboard') }}">
                {{ config('app.name', 'Exam Seat Management System') }}
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth('student')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('mobile.profile') }}">
                                <i class="fas fa-user"></i> {{ Auth::guard('student')->user()->name }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                            <form id="logout-form" action="{{ route('mobile.logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Content -->
    <div class="container py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @yield('content')
    </div>
    
    <!-- Bottom Navigation -->
    @auth('student')
        <nav class="navbar navbar-expand navbar-light bottom-nav">
            <div class="container">
                <ul class="navbar-nav w-100">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('mobile.dashboard') ? 'active' : '' }}" href="{{ route('mobile.dashboard') }}">
                            <i class="fas fa-home"></i>
                            <span>Home</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('mobile.seating_plans*') ? 'active' : '' }}" href="{{ route('mobile.seating_plans') }}">
                            <i class="fas fa-chair"></i>
                            <span>Seats</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('mobile.exam_schedule') ? 'active' : '' }}" href="{{ route('mobile.exam_schedule') }}">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Schedule</span>
                        </a>
                    </li>
                    <li class="nav-item position-relative">
                        <a class="nav-link {{ request()->routeIs('mobile.notifications*') ? 'active' : '' }}" href="{{ route('mobile.notifications') }}">
                            <i class="fas fa-bell"></i>
                            <span>Alerts</span>
                            @if(Auth::guard('student')->user()->unreadNotifications()->count() > 0)
                                <span class="notification-badge">{{ Auth::guard('student')->user()->unreadNotifications()->count() }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('mobile.question_papers*') ? 'active' : '' }}" href="{{ route('mobile.question_papers') }}">
                            <i class="fas fa-file-alt"></i>
                            <span>Papers</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    @endauth
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    @yield('scripts')
</body>
</html>

