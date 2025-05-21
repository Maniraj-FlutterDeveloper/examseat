<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Exam Seat Management System</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #0a2463; /* Navy Blue */
            --secondary-color: #3e92cc;
            --accent-color: #d8315b;
            --light-color: #fffaff;
            --dark-color: #1e1b18;
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
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1.25rem;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar .nav-link.active {
            color: white;
            background-color: var(--secondary-color);
            border-left: 4px solid white;
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
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
            background-color: #081f54;
            border-color: #081f54;
        }
        
        .sidebar-toggle {
            display: none;
        }
        
        /* Notification styles */
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            font-size: 0.7rem;
            padding: 0.25rem 0.4rem;
        }
        
        .notification-dropdown {
            width: 320px;
            max-height: 400px;
            overflow-y: auto;
        }
        
        .notification-item {
            border-left: 3px solid transparent;
            transition: all 0.2s;
        }
        
        .notification-item:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }
        
        .notification-item.unread {
            border-left-color: var(--primary-color);
            background-color: rgba(62, 146, 204, 0.1);
        }
        
        .notification-item .notification-time {
            font-size: 0.75rem;
            color: #6c757d;
        }
        
        .notification-item .notification-title {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .notification-item .notification-message {
            font-size: 0.875rem;
            color: #495057;
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
            
            .sidebar-toggle {
                display: block;
            }
            
            .notification-dropdown {
                width: 280px;
            }
        }
        
        /* Animation for alerts */
        .alert {
            animation: fadeIn 0.5s;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Custom styles for tables */
        .table {
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .table th {
            background-color: #f8f9fa;
            border-top: none;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(62, 146, 204, 0.1);
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="p-3 border-bottom">
                <h4 class="text-center">Exam Seat System</h4>
            </div>
            <ul class="nav flex-column mt-3">
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                
                <li class="nav-item mt-3">
                    <h6 class="text-uppercase px-4 text-muted small">Seat Plan</h6>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.blocks.index') }}" class="nav-link {{ request()->routeIs('admin.blocks.*') ? 'active' : '' }}">
                        <i class="fas fa-building"></i> Blocks
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.rooms.index') }}" class="nav-link {{ request()->routeIs('admin.rooms.*') ? 'active' : '' }}">
                        <i class="fas fa-door-open"></i> Rooms
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.courses.index') }}" class="nav-link {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}">
                        <i class="fas fa-graduation-cap"></i> Courses
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.students.index') }}" class="nav-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
                        <i class="fas fa-user-graduate"></i> Students
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.seating_plans.index') }}" class="nav-link {{ request()->routeIs('admin.seating_plans.*') ? 'active' : '' }}">
                        <i class="fas fa-chair"></i> Seating Plans
                    </a>
                </li>
                
                <li class="nav-item mt-3">
                    <h6 class="text-uppercase px-4 text-muted small">Question Bank</h6>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.subjects.index') }}" class="nav-link {{ request()->routeIs('admin.subjects.*') ? 'active' : '' }}">
                        <i class="fas fa-book"></i> Subjects
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.units.index') }}" class="nav-link {{ request()->routeIs('admin.units.*') ? 'active' : '' }}">
                        <i class="fas fa-bookmark"></i> Units
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.topics.index') }}" class="nav-link {{ request()->routeIs('admin.topics.*') ? 'active' : '' }}">
                        <i class="fas fa-lightbulb"></i> Topics
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.questions.index') }}" class="nav-link {{ request()->routeIs('admin.questions.*') ? 'active' : '' }}">
                        <i class="fas fa-question-circle"></i> Questions
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.blooms_taxonomies.index') }}" class="nav-link {{ request()->routeIs('admin.blooms_taxonomies.*') ? 'active' : '' }}">
                        <i class="fas fa-brain"></i> Bloom's Taxonomy
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.blueprints.index') }}" class="nav-link {{ request()->routeIs('admin.blueprints.*') ? 'active' : '' }}">
                        <i class="fas fa-drafting-compass"></i> Blueprints
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.question_papers.index') }}" class="nav-link {{ request()->routeIs('admin.question_papers.*') ? 'active' : '' }}">
                        <i class="fas fa-file-alt"></i> Question Papers
                    </a>
                </li>
                
                <li class="nav-item mt-3">
                    <h6 class="text-uppercase px-4 text-muted small">System</h6>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.notifications.index') }}" class="nav-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
                        <i class="fas fa-bell"></i> Notifications
                        <span id="sidebar-notification-badge" class="badge bg-danger rounded-pill ms-2 d-none">0</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i> Users
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                </li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Navbar -->
            <nav class="navbar navbar-expand-lg mb-4">
                <div class="container-fluid">
                    <button class="btn sidebar-toggle" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <div class="ms-auto d-flex align-items-center">
                        <!-- Notifications Dropdown -->
                        <div class="dropdown me-3">
                            <a class="nav-link position-relative" href="#" role="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-bell"></i>
                                <span id="notification-badge" class="badge bg-danger rounded-pill notification-badge d-none">0</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end notification-dropdown p-0" aria-labelledby="notificationDropdown">
                                <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                                    <h6 class="mb-0">Notifications</h6>
                                    <a href="{{ route('admin.notifications.index') }}" class="text-decoration-none small">View All</a>
                                </div>
                                <div id="notification-list" class="overflow-auto" style="max-height: 300px;">
                                    <div class="text-center p-3">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mb-0 mt-2">Loading notifications...</p>
                                    </div>
                                </div>
                                <div class="p-2 border-top text-center">
                                    <form action="{{ route('admin.notifications.mark_all_as_read') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-light w-100">
                                            <i class="fas fa-check-double me-1"></i>Mark All as Read
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <!-- User Dropdown -->
                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-2"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fas fa-user me-2"></i> Profile</a></li>
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
            
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i> {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i> {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <!-- Page Content -->
            @yield('content')
        </div>
    </div>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar toggle
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                    mainContent.classList.toggle('active');
                });
            }
            
            // Auto-dismiss alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    const closeButton = alert.querySelector('.btn-close');
                    if (closeButton) {
                        closeButton.click();
                    }
                }, 5000);
            });
            
            // Fetch unread notifications count
            function fetchUnreadCount() {
                fetch('{{ route('admin.notifications.unread_count') }}')
                    .then(response => response.json())
                    .then(data => {
                        const count = data.count;
                        const badge = document.getElementById('notification-badge');
                        const sidebarBadge = document.getElementById('sidebar-notification-badge');
                        
                        if (count > 0) {
                            badge.textContent = count;
                            badge.classList.remove('d-none');
                            
                            sidebarBadge.textContent = count;
                            sidebarBadge.classList.remove('d-none');
                        } else {
                            badge.classList.add('d-none');
                            sidebarBadge.classList.add('d-none');
                        }
                    })
                    .catch(error => console.error('Error fetching notification count:', error));
            }
            
            // Fetch recent notifications
            function fetchRecentNotifications() {
                fetch('{{ route('admin.notifications.recent_unread') }}')
                    .then(response => response.json())
                    .then(data => {
                        const notificationList = document.getElementById('notification-list');
                        const notifications = data.notifications;
                        
                        if (notifications.length > 0) {
                            let html = '';
                            
                            notifications.forEach(notification => {
                                const date = new Date(notification.created_at);
                                const formattedDate = date.toLocaleString();
                                
                                html += `
                                    <a href="{{ route('admin.notifications.show', '') }}/${notification.id}" class="dropdown-item notification-item unread p-3 border-bottom">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="notification-title">${notification.title}</span>
                                            <span class="badge bg-${notification.type === 'info' ? 'info' : (notification.type === 'success' ? 'success' : (notification.type === 'warning' ? 'warning' : 'danger'))}">${notification.type}</span>
                                        </div>
                                        <p class="notification-message mb-1">${notification.message.length > 50 ? notification.message.substring(0, 50) + '...' : notification.message}</p>
                                        <small class="notification-time">${formattedDate}</small>
                                    </a>
                                `;
                            });
                            
                            notificationList.innerHTML = html;
                        } else {
                            notificationList.innerHTML = `
                                <div class="text-center p-3">
                                    <i class="fas fa-bell-slash text-muted mb-2" style="font-size: 2rem;"></i>
                                    <p class="mb-0">No new notifications</p>
                                </div>
                            `;
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching notifications:', error);
                        const notificationList = document.getElementById('notification-list');
                        notificationList.innerHTML = `
                            <div class="text-center p-3">
                                <i class="fas fa-exclamation-circle text-danger mb-2" style="font-size: 2rem;"></i>
                                <p class="mb-0">Failed to load notifications</p>
                            </div>
                        `;
                    });
            }
            
            // Initial fetch
            fetchUnreadCount();
            
            // Fetch notifications when dropdown is opened
            const notificationDropdown = document.getElementById('notificationDropdown');
            if (notificationDropdown) {
                notificationDropdown.addEventListener('click', function() {
                    fetchRecentNotifications();
                });
            }
            
            // Refresh notification count every 60 seconds
            setInterval(fetchUnreadCount, 60000);
        });
    </script>
    
    @stack('scripts')
</body>
</html>

