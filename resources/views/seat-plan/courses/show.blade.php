@extends('layouts.app')

@section('title', 'Course Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-book mr-2"></i> Course Details: {{ $course->name }}
                    </h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-12 text-right">
                            <a href="{{ route('courses.edit', $course) }}" class="btn btn-primary">
                                <i class="fas fa-edit mr-1"></i> Edit Course
                            </a>
                            <a href="{{ route('courses.index') }}" class="btn btn-secondary ml-2">
                                <i class="fas fa-arrow-left mr-1"></i> Back to List
                            </a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">Course Information</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 30%">ID</th>
                                            <td>{{ $course->id }}</td>
                                        </tr>
                                        <tr>
                                            <th>Name</th>
                                            <td>{{ $course->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Code</th>
                                            <td>{{ $course->code }}</td>
                                        </tr>
                                        <tr>
                                            <th>Department</th>
                                            <td>{{ $course->department ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Duration</th>
                                            <td>{{ $course->duration ?? 'N/A' }} {{ $course->duration == 1 ? 'year' : 'years' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Description</th>
                                            <td>{{ $course->description ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                <span class="badge badge-{{ $course->is_active ? 'success' : 'danger' }}">
                                                    {{ $course->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Created At</th>
                                            <td>{{ $course->created_at->format('M d, Y h:i A') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Updated At</th>
                                            <td>{{ $course->updated_at->format('M d, Y h:i A') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">Student Statistics</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <h1 class="display-4">{{ $course->students->count() }}</h1>
                                                    <p class="lead">Total Students</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <h1 class="display-4">{{ $course->students->where('is_active', true)->count() }}</h1>
                                                    <p class="lead">Active Students</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <h6>Students by Year</h6>
                                        <canvas id="studentsByYearChart" width="100%" height="200"></canvas>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <h6>Students by Section</h6>
                                        <canvas id="studentsBySectionChart" width="100%" height="200"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card-header bg-primary text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Students in this Course</h6>
                                <a href="{{ route('students.create') }}" class="btn btn-sm btn-light">
                                    <i class="fas fa-plus-circle mr-1"></i> Add Student
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="students-table">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Roll Number</th>
                                            <th>Name</th>
                                            <th>Year</th>
                                            <th>Section</th>
                                            <th>Gender</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($course->students as $student)
                                            <tr>
                                                <td>{{ $student->id }}</td>
                                                <td>{{ $student->roll_number }}</td>
                                                <td>{{ $student->name }}</td>
                                                <td>{{ $student->year }}</td>
                                                <td>{{ $student->section ?? 'N/A' }}</td>
                                                <td>{{ ucfirst($student->gender ?? 'N/A') }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $student->is_active ? 'success' : 'danger' }}">
                                                        {{ $student->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('students.show', $student) }}" class="btn btn-info btn-sm" title="View">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('students.edit', $student) }}" class="btn btn-primary btn-sm" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">No students found in this course.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
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
        $('#students-table').DataTable({
            "responsive": true,
            "language": {
                "search": "Search students:",
                "lengthMenu": "Show _MENU_ students per page",
                "info": "Showing _START_ to _END_ of _TOTAL_ students",
                "infoEmpty": "Showing 0 to 0 of 0 students",
                "zeroRecords": "No matching students found"
            }
        });
        
        // Students by Year Chart
        var studentsByYearCtx = document.getElementById('studentsByYearChart').getContext('2d');
        var studentsByYearChart = new Chart(studentsByYearCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_keys($studentsByYear)) !!},
                datasets: [{
                    label: 'Number of Students',
                    data: {!! json_encode(array_values($studentsByYear)) !!},
                    backgroundColor: [
                        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                        '#5a5c69', '#6f42c1', '#fd7e14', '#20c997', '#6c757d'
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
        
        // Students by Section Chart
        var studentsBySectionCtx = document.getElementById('studentsBySectionChart').getContext('2d');
        var studentsBySectionChart = new Chart(studentsBySectionCtx, {
            type: 'pie',
            data: {
                labels: {!! json_encode(array_keys($studentsBySection)) !!},
                datasets: [{
                    data: {!! json_encode(array_values($studentsBySection)) !!},
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
    });
</script>
@endsection

