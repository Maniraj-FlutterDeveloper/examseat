<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Exam Seat Management') | {{ config('app.name', 'ExamSeat') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <style>
        :root {
            --primary-color: #0a2463;
            --secondary-color: #3e92cc;
            --accent-color: #1e5f74;
            --light-color: #fffaff;
            --dark-color: #1c1c1c;
        }

        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f9fa;
        }

        .navbar {
            background-color: var(--primary-color);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--light-color) !important;
        }

        .nav-link {
            color: var(--light-color) !important;
            font-weight: 600;
            transition: all 0.3s;
        }

        .nav-link:hover {
            color: rgba(255, 255, 255, 0.8) !important;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .dropdown-item:hover {
            background-color: var(--secondary-color);
            color: var(--light-color);
        }

        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .card-header {
            border-radius: 8px 8px 0 0 !important;
            font-weight: 600;
        }

        .btn {
            border-radius: 4px;
            font-weight: 600;
            padding: 8px 16px;
            transition: all 0.3s;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #081f54;
            border-color: #081f54;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        .table th {
            font-weight: 600;
            background-color: #f8f9fa;
        }

        .badge {
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 4px;
        }

        .form-control {
            border-radius: 4px;
            border: 1px solid #ced4da;
            padding: 8px 12px;
        }

        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(62, 146, 204, 0.25);
        }

        .sidebar {
            background-color: var(--primary-color);
            min-height: calc(100vh - 56px);
            padding-top: 20px;
        }

        .sidebar-link {
            color: var(--light-color);
            padding: 10px 15px;
            display: block;
            text-decoration: none;
            transition: all 0.3s;
        }

        .sidebar-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--light-color);
            text-decoration: none;
        }

        .sidebar-link.active {
            background-color: var(--secondary-color);
            color: var(--light-color);
            border-left: 4px solid var(--light-color);
        }

        .content-wrapper {
            padding: 20px;
        }

        .footer {
            background-color: var(--primary-color);
            color: var(--light-color);
            padding: 15px 0;
            text-align: center;
            margin-top: 30px;
        }

        /* Animations */
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* DataTables Customization */
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--secondary-color);
            color: white !important;
            border: none;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: var(--primary-color);
            color: white !important;
            border: none;
        }

        /* Select2 Customization */
        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #ced4da;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
    </style>

    @yield('styles')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <i class="fas fa-graduation-cap mr-2"></i>{{ config('app.name', 'ExamSeat') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('home') }}">
                                <i class="fas fa-tachometer-alt mr-1"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a id="seatPlanDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                <i class="fas fa-chair mr-1"></i>Seat Plan
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="seatPlanDropdown">
                                <a class="dropdown-item" href="{{ route('seating-plans.index') }}">
                                    <i class="fas fa-list mr-1"></i>Seating Plans
                                </a>
                                <a class="dropdown-item" href="{{ route('blocks.index') }}">
                                    <i class="fas fa-building mr-1"></i>Blocks
                                </a>
                                <a class="dropdown-item" href="{{ route('rooms.index') }}">
                                    <i class="fas fa-door-open mr-1"></i>Rooms
                                </a>
                                <a class="dropdown-item" href="{{ route('courses.index') }}">
                                    <i class="fas fa-book mr-1"></i>Courses
                                </a>
                                <a class="dropdown-item" href="{{ route('students.index') }}">
                                    <i class="fas fa-user-graduate mr-1"></i>Students
                                </a>
                                <a class="dropdown-item" href="{{ route('invigilators.index') }}">
                                    <i class="fas fa-user-tie mr-1"></i>Invigilators
                                </a>
                                <a class="dropdown-item" href="{{ route('seating-rules.index') }}">
                                    <i class="fas fa-cogs mr-1"></i>Seating Rules
                                </a>
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <a id="questionBankDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                <i class="fas fa-question-circle mr-1"></i>Question Bank
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="questionBankDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-book-open mr-1"></i>Subjects
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-layer-group mr-1"></i>Units
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-bookmark mr-1"></i>Topics
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-question mr-1"></i>Questions
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-brain mr-1"></i>Bloom's Taxonomy
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-file-alt mr-1"></i>Question Papers
                                </a>
                            </div>
                        </li>
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">
                                    <i class="fas fa-sign-in-alt mr-1"></i>{{ __('Login') }}
                                </a>
                            </li>
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <i class="fas fa-user-circle mr-1"></i>{{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="#">
                                        <i class="fas fa-user-cog mr-1"></i>Profile
                                    </a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt mr-1"></i>{{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>

        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <p class="mb-0">&copy; {{ date('Y') }} {{ config('app.name', 'ExamSeat') }}. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="{{ asset('js/app.js') }}" defer></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap4'
            });

            // Tooltip initialization
            $('[data-toggle="tooltip"]').tooltip();

            // Active link detection
            const currentUrl = window.location.href;
            $('.nav-link, .dropdown-item').each(function() {
                if (currentUrl.includes($(this).attr('href'))) {
                    $(this).addClass('active');
                    $(this).closest('.nav-item.dropdown').find('.nav-link').addClass('active');
                }
            });
        });
    </script>

    @yield('scripts')
</body>
</html>

