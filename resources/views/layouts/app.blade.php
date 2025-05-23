<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Exam Seat Management System')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #000080;
            --primary-dark: #00006b;
            --secondary-color: #f8f9fa;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        .sidebar {
            background-color: var(--primary-color);
            min-height: 100vh;
            color: white;
            position: fixed;
            width: 250px;
            transition: all 0.3s;
            z-index: 1000;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 10px 20px;
            margin: 5px 0;
            border-radius: 5px;
            transition: all 0.2s;
        }
        
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: all 0.3s;
        }
        
        .navbar {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand {
            color: var(--primary-color);
            font-weight: bold;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 20px;
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
            font-weight: 600;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }
        
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-header h3 {
            margin: 0;
            font-size: 1.5rem;
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .dropdown-item:active {
            background-color: var(--primary-color);
        }
        
        .nav-category {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255, 255, 255, 0.5);
            padding: 20px 20px 10px;
            margin-top: 10px;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -250px;
            }
            .sidebar.active {
                margin-left: 0;
            }
            .main-content {
                margin-left: 0;
            }
            .main-content.active {
                margin-left: 250px;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <h3>Exam Seat</h3>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            
            <div class="nav-category">Seat Management</div>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('blocks*') ? 'active' : '' }}" href="{{ route('blocks.index') }}">
                    <i class="fas fa-building"></i> Blocks
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('rooms*') ? 'active' : '' }}" href="{{ route('rooms.index') }}">
                    <i class="fas fa-door-open"></i> Rooms
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('seating-plans*') ? 'active' : '' }}" href="{{ route('seating-plans.index') }}">
                    <i class="fas fa-chair"></i> Seating Plans
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('invigilators*') ? 'active' : '' }}" href="{{ route('invigilators.index') }}">
                    <i class="fas fa-user-tie"></i> Invigilators
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('room-invigilator-assignments*') ? 'active' : '' }}" href="{{ route('room-invigilator-assignments.index') }}">
                    <i class="fas fa-tasks"></i> Assignments
                </a>
            </li>
            
            <div class="nav-category">Academic</div>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('courses*') ? 'active' : '' }}" href="{{ route('courses.index') }}">
                    <i class="fas fa-graduation-cap"></i> Courses
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('students*') ? 'active' : '' }}" href="{{ route('students.index') }}">
                    <i class="fas fa-user-graduate"></i> Students
                </a>
            </li>
            
            <div class="nav-category">Question Bank</div>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('subjects*') ? 'active' : '' }}" href="{{ route('subjects.index') }}">
                    <i class="fas fa-book"></i> Subjects
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('units*') ? 'active' : '' }}" href="{{ route('units.index') }}">
                    <i class="fas fa-layer-group"></i> Units
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('topics*') ? 'active' : '' }}" href="{{ route('topics.index') }}">
                    <i class="fas fa-list"></i> Topics
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('blooms-taxonomy*') ? 'active' : '' }}" href="{{ route('blooms-taxonomy.index') }}">
                    <i class="fas fa-brain"></i> Bloom's Taxonomy
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('questions*') ? 'active' : '' }}" href="{{ route('questions.index') }}">
                    <i class="fas fa-question-circle"></i> Questions
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('blueprints*') ? 'active' : '' }}" href="{{ route('blueprints.index') }}">
                    <i class="fas fa-drafting-compass"></i> Blueprints
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('question-papers*') ? 'active' : '' }}" href="{{ route('question-papers.index') }}">
                    <i class="fas fa-file-alt"></i> Question Papers
                </a>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <nav class="navbar navbar-expand-lg navbar-light mb-4">
            <div class="container-fluid">
                <button class="btn btn-outline-secondary" id="sidebar-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="ms-auto d-flex align-items-center">
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <div class="container-fluid">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('active');
                mainContent.classList.toggle('active');
            });
        });
    </script>
    @yield('scripts')
</body>
</html>

