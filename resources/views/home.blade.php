@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card bg-info text-white mb-4">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0">Total Blocks</h6>
                                            <h2 class="mb-0">{{ App\Models\Block::count() }}</h2>
                                        </div>
                                        <i class="fas fa-building fa-3x opacity-50"></i>
                                    </div>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="{{ route('blocks.index') }}">View Details</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white mb-4">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0">Total Rooms</h6>
                                            <h2 class="mb-0">{{ App\Models\Room::count() }}</h2>
                                        </div>
                                        <i class="fas fa-door-open fa-3x opacity-50"></i>
                                    </div>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="{{ route('rooms.index') }}">View Details</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white mb-4">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0">Total Students</h6>
                                            <h2 class="mb-0">{{ App\Models\Student::count() }}</h2>
                                        </div>
                                        <i class="fas fa-user-graduate fa-3x opacity-50"></i>
                                    </div>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="{{ route('students.index') }}">View Details</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white mb-4">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0">Seating Plans</h6>
                                            <h2 class="mb-0">{{ App\Models\SeatingPlan::count() }}</h2>
                                        </div>
                                        <i class="fas fa-chair fa-3x opacity-50"></i>
                                    </div>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="{{ route('seating-plans.index') }}">View Details</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-alt mr-2"></i> Upcoming Exams
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Title</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $upcomingExams = App\Models\SeatingPlan::where('exam_date', '>=', date('Y-m-d'))
                                        ->orderBy('exam_date', 'asc')
                                        ->orderBy('start_time', 'asc')
                                        ->take(5)
                                        ->get();
                                @endphp
                                
                                @forelse($upcomingExams as $exam)
                                    <tr>
                                        <td>{{ $exam->title }}</td>
                                        <td>{{ \Carbon\Carbon::parse($exam->exam_date)->format('M d, Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($exam->start_time)->format('h:i A') }}</td>
                                        <td>
                                            <span class="badge badge-{{ $exam->status_color }}">
                                                {{ ucfirst($exam->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('seating-plans.show', $exam) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No upcoming exams found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('seating-plans.index') }}" class="btn btn-primary">
                            <i class="fas fa-list mr-1"></i> View All Seating Plans
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie mr-2"></i> Statistics
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="card-title">Students by Course</h6>
                                    <canvas id="studentsByCourseChart" width="100%" height="150"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="card-title">Rooms by Block</h6>
                                    <canvas id="roomsByBlockChart" width="100%" height="150"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="card-title">Seating Plans by Status</h6>
                                    <canvas id="seatingPlansByStatusChart" width="100%" height="150"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-tasks mr-2"></i> Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('seating-plans.create') }}" class="btn btn-success btn-block py-3">
                                <i class="fas fa-plus-circle fa-2x mb-2"></i><br>
                                Create Seating Plan
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('students.create') }}" class="btn btn-info btn-block py-3">
                                <i class="fas fa-user-plus fa-2x mb-2"></i><br>
                                Add Student
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('rooms.create') }}" class="btn btn-warning btn-block py-3">
                                <i class="fas fa-door-open fa-2x mb-2"></i><br>
                                Add Room
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('invigilators.create') }}" class="btn btn-danger btn-block py-3">
                                <i class="fas fa-user-tie fa-2x mb-2"></i><br>
                                Add Invigilator
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        // Students by Course Chart
        @php
            $courses = App\Models\Course::withCount('students')->get();
            $courseLabels = $courses->pluck('name')->toJson();
            $courseData = $courses->pluck('students_count')->toJson();
        @endphp

        var studentsByCourseCtx = document.getElementById('studentsByCourseChart').getContext('2d');
        var studentsByCourseChart = new Chart(studentsByCourseCtx, {
            type: 'pie',
            data: {
                labels: {!! $courseLabels !!},
                datasets: [{
                    data: {!! $courseData !!},
                    backgroundColor: [
                        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                        '#5a5c69', '#6f42c1', '#fd7e14', '#20c997', '#6c757d'
                    ],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12
                        }
                    }
                }
            }
        });

        // Rooms by Block Chart
        @php
            $blocks = App\Models\Block::withCount('rooms')->get();
            $blockLabels = $blocks->pluck('name')->toJson();
            $blockData = $blocks->pluck('rooms_count')->toJson();
        @endphp

        var roomsByBlockCtx = document.getElementById('roomsByBlockChart').getContext('2d');
        var roomsByBlockChart = new Chart(roomsByBlockCtx, {
            type: 'doughnut',
            data: {
                labels: {!! $blockLabels !!},
                datasets: [{
                    data: {!! $blockData !!},
                    backgroundColor: [
                        '#1cc88a', '#4e73df', '#36b9cc', '#f6c23e', '#e74a3b',
                        '#5a5c69', '#6f42c1', '#fd7e14', '#20c997', '#6c757d'
                    ],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12
                        }
                    }
                }
            }
        });

        // Seating Plans by Status Chart
        @php
            $seatingPlansByStatus = App\Models\SeatingPlan::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get();
            $statusLabels = $seatingPlansByStatus->pluck('status')->map(function($status) {
                return ucfirst($status);
            })->toJson();
            $statusData = $seatingPlansByStatus->pluck('count')->toJson();
        @endphp

        var seatingPlansByStatusCtx = document.getElementById('seatingPlansByStatusChart').getContext('2d');
        var seatingPlansByStatusChart = new Chart(seatingPlansByStatusCtx, {
            type: 'bar',
            data: {
                labels: {!! $statusLabels !!},
                datasets: [{
                    label: 'Number of Seating Plans',
                    data: {!! $statusData !!},
                    backgroundColor: [
                        '#6c757d', '#4e73df', '#1cc88a', '#e74a3b'
                    ],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    });
</script>
@endsection

