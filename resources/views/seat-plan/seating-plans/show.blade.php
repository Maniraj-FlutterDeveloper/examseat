@extends('layouts.app')

@section('title', 'Seating Plan Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chair mr-2"></i> Seating Plan: {{ $seatingPlan->title }}
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
                        <div class="col-md-12">
                            <div class="btn-toolbar justify-content-between" role="toolbar">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('seating-plans.edit', $seatingPlan) }}" class="btn btn-primary">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </a>
                                    <a href="{{ route('seating-plans.print', $seatingPlan) }}" class="btn btn-warning" target="_blank">
                                        <i class="fas fa-print mr-1"></i> Print
                                    </a>
                                    <div class="btn-group" role="group">
                                        <button id="actionDropdown" type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-cog mr-1"></i> Actions
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="actionDropdown">
                                            <a class="dropdown-item" href="{{ route('seating-plans.duplicate', $seatingPlan) }}">
                                                <i class="fas fa-copy mr-1"></i> Duplicate
                                            </a>
                                            <a class="dropdown-item" href="{{ route('seating-plans.export', $seatingPlan) }}">
                                                <i class="fas fa-file-export mr-1"></i> Export
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            @if($seatingPlan->status == 'draft')
                                                <form action="{{ route('seating-plans.publish', $seatingPlan) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="fas fa-check-circle mr-1"></i> Publish
                                                    </button>
                                                </form>
                                            @endif
                                            @if($seatingPlan->status == 'published')
                                                <form action="{{ route('seating-plans.complete', $seatingPlan) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="fas fa-flag-checkered mr-1"></i> Mark as Completed
                                                    </button>
                                                </form>
                                            @endif
                                            @if($seatingPlan->status != 'cancelled')
                                                <form action="{{ route('seating-plans.cancel', $seatingPlan) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="dropdown-item" onclick="return confirm('Are you sure you want to cancel this seating plan?');">
                                                        <i class="fas fa-ban mr-1"></i> Cancel
                                                    </button>
                                                </form>
                                            @endif
                                            <div class="dropdown-divider"></div>
                                            <form action="{{ route('seating-plans.destroy', $seatingPlan) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this seating plan? This action cannot be undone.');">
                                                    <i class="fas fa-trash mr-1"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('seating-plans.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left mr-1"></i> Back to List
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="card mb-4">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">Exam Information</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 40%">Title</th>
                                            <td>{{ $seatingPlan->title }}</td>
                                        </tr>
                                        <tr>
                                            <th>Exam Code</th>
                                            <td>{{ $seatingPlan->exam_code ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Date</th>
                                            <td>{{ \Carbon\Carbon::parse($seatingPlan->exam_date)->format('M d, Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Time</th>
                                            <td>
                                                {{ \Carbon\Carbon::parse($seatingPlan->start_time)->format('h:i A') }} - 
                                                {{ \Carbon\Carbon::parse($seatingPlan->end_time)->format('h:i A') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Duration</th>
                                            <td>
                                                @php
                                                    $start = \Carbon\Carbon::parse($seatingPlan->start_time);
                                                    $end = \Carbon\Carbon::parse($seatingPlan->end_time);
                                                    $duration = $end->diffInMinutes($start);
                                                    $hours = floor($duration / 60);
                                                    $minutes = $duration % 60;
                                                @endphp
                                                {{ $hours > 0 ? $hours . ' hour(s) ' : '' }}{{ $minutes > 0 ? $minutes . ' minute(s)' : '' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                @if($seatingPlan->status == 'draft')
                                                    <span class="badge badge-secondary">Draft</span>
                                                @elseif($seatingPlan->status == 'published')
                                                    <span class="badge badge-success">Published</span>
                                                @elseif($seatingPlan->status == 'completed')
                                                    <span class="badge badge-info">Completed</span>
                                                @elseif($seatingPlan->status == 'cancelled')
                                                    <span class="badge badge-danger">Cancelled</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Created By</th>
                                            <td>{{ $seatingPlan->created_by_name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Created At</th>
                                            <td>{{ $seatingPlan->created_at->format('M d, Y h:i A') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Last Updated</th>
                                            <td>{{ $seatingPlan->updated_at->format('M d, Y h:i A') }}</td>
                                        </tr>
                                    </table>

                                    @if($seatingPlan->description)
                                        <div class="mt-3">
                                            <h6>Description</h6>
                                            <p>{{ $seatingPlan->description }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">Allocation Settings</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 50%">Allocation Method</th>
                                            <td>
                                                @if($seatingPlan->allocation_method == 'course')
                                                    <span class="badge badge-primary">Course-based</span>
                                                @elseif($seatingPlan->allocation_method == 'mixed')
                                                    <span class="badge badge-success">Mixed</span>
                                                @elseif($seatingPlan->allocation_method == 'manual')
                                                    <span class="badge badge-warning">Manual</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Seating Gap</th>
                                            <td>
                                                @if($seatingPlan->seating_gap == 0)
                                                    No Gap (Every Seat)
                                                @elseif($seatingPlan->seating_gap == 1)
                                                    1 Seat Gap (Alternate)
                                                @elseif($seatingPlan->seating_gap == 2)
                                                    2 Seat Gap
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Special Needs Handling</th>
                                            <td>
                                                @if($seatingPlan->special_needs_handling == 'prioritize')
                                                    Prioritize Accessible Seating
                                                @elseif($seatingPlan->special_needs_handling == 'separate')
                                                    Separate Room
                                                @elseif($seatingPlan->special_needs_handling == 'ignore')
                                                    No Special Handling
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Auto-assign Invigilators</th>
                                            <td>
                                                <span class="badge badge-{{ $seatingPlan->auto_assign_invigilators ? 'success' : 'secondary' }}">
                                                    {{ $seatingPlan->auto_assign_invigilators ? 'Yes' : 'No' }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">Summary</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="card bg-light">
                                                <div class="card-body text-center p-3">
                                                    <h3 class="mb-0">{{ $seatingPlan->rooms_count }}</h3>
                                                    <p class="small mb-0">Rooms</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-light">
                                                <div class="card-body text-center p-3">
                                                    <h3 class="mb-0">{{ $seatingPlan->students_count }}</h3>
                                                    <p class="small mb-0">Students</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-light">
                                                <div class="card-body text-center p-3">
                                                    <h3 class="mb-0">{{ $seatingPlan->invigilators_count }}</h3>
                                                    <p class="small mb-0">Invigilators</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-light">
                                                <div class="card-body text-center p-3">
                                                    <h3 class="mb-0">{{ $seatingPlan->courses_count }}</h3>
                                                    <p class="small mb-0">Courses</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <canvas id="roomCapacityChart" width="100%" height="200"></canvas>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header bg-info text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">Rooms and Invigilators</h6>
                                        <a href="{{ route('seating-plans.rooms.edit', $seatingPlan) }}" class="btn btn-sm btn-light">
                                            <i class="fas fa-edit mr-1"></i> Edit Rooms
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>Room</th>
                                                    <th>Block</th>
                                                    <th>Capacity</th>
                                                    <th>Students</th>
                                                    <th>Invigilators</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($seatingPlan->rooms as $room)
                                                    <tr>
                                                        <td>
                                                            <a href="{{ route('rooms.show', $room) }}">
                                                                {{ $room->name }} ({{ $room->code }})
                                                            </a>
                                                        </td>
                                                        <td>{{ $room->block->name ?? 'N/A' }}</td>
                                                        <td>{{ $room->capacity }}</td>
                                                        <td>
                                                            <span class="badge badge-primary">
                                                                {{ $room->pivot->students_count ?? 0 }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            @foreach($room->invigilators as $invigilator)
                                                                <span class="badge badge-info mr-1" data-toggle="tooltip" title="{{ $invigilator->name }}">
                                                                    {{ $invigilator->is_chief_invigilator ? 'Chief: ' : '' }}{{ $invigilator->employee_id }}
                                                                </span>
                                                            @endforeach
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('seating-plans.room-layout', [$seatingPlan, $room]) }}" class="btn btn-sm btn-primary">
                                                                <i class="fas fa-th"></i> Layout
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center">No rooms assigned yet.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">Courses and Students</h6>
                                        <a href="{{ route('seating-plans.students.edit', $seatingPlan) }}" class="btn btn-sm btn-light">
                                            <i class="fas fa-edit mr-1"></i> Edit Students
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <ul class="nav nav-tabs" id="coursesTabs" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="courses-tab" data-toggle="tab" href="#courses" role="tab" aria-controls="courses" aria-selected="true">
                                                Courses
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="students-tab" data-toggle="tab" href="#students" role="tab" aria-controls="students" aria-selected="false">
                                                Students
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="special-needs-tab" data-toggle="tab" href="#special-needs" role="tab" aria-controls="special-needs" aria-selected="false">
                                                Special Needs
                                            </a>
                                        </li>
                                    </ul>

                                    <div class="tab-content mt-3" id="coursesTabsContent">
                                        <div class="tab-pane fade show active" id="courses" role="tabpanel" aria-labelledby="courses-tab">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped">
                                                    <thead class="thead-dark">
                                                        <tr>
                                                            <th>Course</th>
                                                            <th>Code</th>
                                                            <th>Students</th>
                                                            <th>Years</th>
                                                            <th>Sections</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($seatingPlan->courses as $course)
                                                            <tr>
                                                                <td>
                                                                    <a href="{{ route('courses.show', $course) }}">
                                                                        {{ $course->name }}
                                                                    </a>
                                                                </td>
                                                                <td>{{ $course->code }}</td>
                                                                <td>
                                                                    <span class="badge badge-primary">
                                                                        {{ $course->pivot->students_count ?? 0 }}
                                                                    </span>
                                                                </td>
                                                                <td>{{ $course->pivot->years ?? 'All' }}</td>
                                                                <td>{{ $course->pivot->sections ?? 'All' }}</td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="5" class="text-center">No courses assigned yet.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="students" role="tabpanel" aria-labelledby="students-tab">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped" id="students-table">
                                                    <thead class="thead-dark">
                                                        <tr>
                                                            <th>Roll Number</th>
                                                            <th>Name</th>
                                                            <th>Course</th>
                                                            <th>Room</th>
                                                            <th>Seat</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($seatingPlan->students as $student)
                                                            <tr>
                                                                <td>{{ $student->roll_number }}</td>
                                                                <td>
                                                                    <a href="{{ route('students.show', $student) }}">
                                                                        {{ $student->name }}
                                                                    </a>
                                                                </td>
                                                                <td>{{ $student->course->code ?? 'N/A' }}</td>
                                                                <td>
                                                                    @if($student->pivot->room_id)
                                                                        <a href="{{ route('rooms.show', $student->pivot->room_id) }}">
                                                                            {{ $student->pivot->room_code }}
                                                                        </a>
                                                                    @else
                                                                        Not Assigned
                                                                    @endif
                                                                </td>
                                                                <td>{{ $student->pivot->seat_number ?? 'N/A' }}</td>
                                                                <td>
                                                                    <a href="{{ route('seating-plans.student-card', [$seatingPlan, $student]) }}" class="btn btn-sm btn-info" target="_blank">
                                                                        <i class="fas fa-id-card"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="6" class="text-center">No students assigned yet.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="special-needs" role="tabpanel" aria-labelledby="special-needs-tab">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped">
                                                    <thead class="thead-dark">
                                                        <tr>
                                                            <th>Roll Number</th>
                                                            <th>Name</th>
                                                            <th>Special Needs</th>
                                                            <th>Room</th>
                                                            <th>Seat</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $specialNeedsStudents = $seatingPlan->students->filter(function($student) {
                                                                return $student->has_special_needs;
                                                            });
                                                        @endphp

                                                        @forelse($specialNeedsStudents as $student)
                                                            <tr>
                                                                <td>{{ $student->roll_number }}</td>
                                                                <td>
                                                                    <a href="{{ route('students.show', $student) }}">
                                                                        {{ $student->name }}
                                                                    </a>
                                                                </td>
                                                                <td>{{ $student->special_needs_details }}</td>
                                                                <td>
                                                                    @if($student->pivot->room_id)
                                                                        <a href="{{ route('rooms.show', $student->pivot->room_id) }}">
                                                                            {{ $student->pivot->room_code }}
                                                                        </a>
                                                                    @else
                                                                        Not Assigned
                                                                    @endif
                                                                </td>
                                                                <td>{{ $student->pivot->seat_number ?? 'N/A' }}</td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="5" class="text-center">No students with special needs found.</td>
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
                "zeroRecords": "No matching students found"
            }
        });
        
        $('[data-toggle="tooltip"]').tooltip();
        
        // Room Capacity Chart
        var roomCapacityCtx = document.getElementById('roomCapacityChart').getContext('2d');
        var roomCapacityChart = new Chart(roomCapacityCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($roomCapacityData['labels']) !!},
                datasets: [
                    {
                        label: 'Total Capacity',
                        data: {!! json_encode($roomCapacityData['capacity']) !!},
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Students Assigned',
                        data: {!! json_encode($roomCapacityData['assigned']) !!},
                        backgroundColor: 'rgba(255, 99, 132, 0.5)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }
                ]
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
                    title: {
                        display: true,
                        text: 'Room Capacity vs. Assigned Students'
                    }
                }
            }
        });
    });
</script>
@endsection

