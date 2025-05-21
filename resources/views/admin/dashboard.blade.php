<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Exam Seat Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #000080; /* Navy Blue */
            --secondary-color: #f8f9fa;
            --accent-color: #0056b3;
        }
        
        body {
            background-color: var(--secondary-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .sidebar {
            background-color: var(--primary-color);
            color: white;
            height: 100vh;
            position: fixed;
            padding-top: 20px;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 10px 20px;
            margin: 5px 0;
            border-radius: 5px;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
            margin-bottom: 20px;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 15px;
        }
        
        .stat-card {
            text-align: center;
            padding: 20px;
        }
        
        .stat-card i {
            font-size: 48px;
            color: var(--primary-color);
            margin-bottom: 15px;
        }
        
        .stat-card .count {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stat-card .label {
            color: #6c757d;
            font-size: 14px;
        }
        
        .navbar {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand {
            color: var(--primary-color);
            font-weight: bold;
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <div class="text-center mb-4">
                    <h4>Exam Seat Management</h4>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-chair"></i> Seat Plan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-question-circle"></i> Question Bank</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-building"></i> Rooms</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-users"></i> Students</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-book"></i> Courses</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-cog"></i> Settings</a>
                    </li>
                    <li class="nav-item mt-5">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="nav-link border-0 bg-transparent">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h2>Admin Dashboard</h2>
                        <p>Welcome to the Exam Seat Management System</p>
                    </div>
                </div>
                
                <!-- Stats Row -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <i class="fas fa-users"></i>
                            <div class="count">1,250</div>
                            <div class="label">Total Students</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <i class="fas fa-building"></i>
                            <div class="count">45</div>
                            <div class="label">Rooms</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <i class="fas fa-book"></i>
                            <div class="count">28</div>
                            <div class="label">Courses</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <i class="fas fa-file-alt"></i>
                            <div class="count">120</div>
                            <div class="label">Question Papers</div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <a href="#" class="btn btn-outline-primary w-100">
                                            <i class="fas fa-plus-circle me-2"></i> Create Seating Plan
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="#" class="btn btn-outline-primary w-100">
                                            <i class="fas fa-plus-circle me-2"></i> Add New Room
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="#" class="btn btn-outline-primary w-100">
                                            <i class="fas fa-plus-circle me-2"></i> Import Students
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="#" class="btn btn-outline-primary w-100">
                                            <i class="fas fa-plus-circle me-2"></i> Generate Question Paper
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Activity -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Recent Activity</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-chair text-primary me-2"></i>
                                            <span>Seating plan created for Computer Science Dept</span>
                                        </div>
                                        <span class="badge bg-primary rounded-pill">2 hours ago</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-file-alt text-primary me-2"></i>
                                            <span>Question paper generated for Mathematics</span>
                                        </div>
                                        <span class="badge bg-primary rounded-pill">5 hours ago</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-users text-primary me-2"></i>
                                            <span>50 new students imported to the system</span>
                                        </div>
                                        <span class="badge bg-primary rounded-pill">Yesterday</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-building text-primary me-2"></i>
                                            <span>New room added: Room 302, Block B</span>
                                        </div>
                                        <span class="badge bg-primary rounded-pill">2 days ago</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
