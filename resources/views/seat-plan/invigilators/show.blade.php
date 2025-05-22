@extends('layouts.app')

@section('title', 'Invigilator Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user-tie mr-2"></i> Invigilator Details: {{ $invigilator->name }}
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
                            <a href="{{ route('invigilators.edit', $invigilator) }}" class="btn btn-primary">
                                <i class="fas fa-edit mr-1"></i> Edit Invigilator
                            </a>
                            <a href="{{ route('invigilators.print-duty-card', $invigilator) }}" class="btn btn-warning" target="_blank">
                                <i class="fas fa-print mr-1"></i> Print Duty Card
                            </a>
                            <a href="{{ route('invigilators.index') }}" class="btn btn-secondary ml-2">
                                <i class="fas fa-arrow-left mr-1"></i> Back to List
                            </a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="card mb-4">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">Personal Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="text-center mb-4">
                                        <div class="avatar-circle">
                                            <span class="avatar-text">{{ strtoupper(substr($invigilator->name, 0, 2)) }}</span>
                                        </div>
                                        <h4 class="mt-3">{{ $invigilator->name }}</h4>
                                        <p class="text-muted">{{ $invigilator->employee_id }}</p>
                                        <span class="badge badge-{{ $invigilator->is_active ? 'success' : 'danger' }}">
                                            {{ $invigilator->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                        @if($invigilator->is_external)
                                            <span class="badge badge-warning ml-2">External</span>
                                        @endif
                                    </div>

                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 40%">Department</th>
                                            <td>{{ $invigilator->department ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Designation</th>
                                            <td>{{ $invigilator->designation ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td>{{ $invigilator->email ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Phone</th>
                                            <td>{{ $invigilator->phone ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Address</th>
                                            <td>{{ $invigilator->address ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Max Duties/Day</th>
                                            <td>{{ $invigilator->max_assignments_per_day }}</td>
                                        </tr>
                                        <tr>
                                            <th>Notes</th>
                                            <td>{{ $invigilator->notes ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">Invigilation Assignments</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-4">
                                        <div class="col-md-3">
                                            <div class="card bg-light">
                                                <div class="card-body text-center p-3">
                                                    <h3 class="mb-0">{{ $invigilator->assignments->count() }}</h3>
                                                    <p class="small mb-0">Total Assignments</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-light">
                                                <div class="card-body text-center p-3">
                                                    <h3 class="mb-0">{{ $invigilator->assignments->where('status', 'completed')->count() }}</h3>
                                                    <p class="small mb-0">Completed</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-light">
                                                <div class="card-body text-center p-3">
                                                    <h3 class="mb-0">{{ $invigilator->assignments->where('status', 'upcoming')->count() }}</h3>
                                                    <p class="small mb-0">Upcoming</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-light">
                                                <div class="card-body text-center p-3">
                                                    <h3 class="mb-0">{{ $invigilator->assignments->where('is_chief_invigilator', true)->count() }}</h3>
                                                    <p class="small mb-0">As Chief</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <ul class="nav nav-tabs" id="assignmentTabs" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="upcoming-tab" data-toggle="tab" href="#upcoming" role="tab" aria-controls="upcoming" aria-selected="true">
                                                Upcoming Assignments
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="past-tab" data-toggle="tab" href="#past" role="tab" aria-controls="past" aria-selected="false">
                                                Past Assignments
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="all-tab" data-toggle="tab" href="#all" role="tab" aria-controls="all" aria-selected="false">
                                                All Assignments
                                            </a>
                                        </li>
                                    </ul>

                                    <div class="tab-content mt-3" id="assignmentTabsContent">
                                        <div class="tab-pane fade show active" id="upcoming" role="tabpanel" aria-labelledby="upcoming-tab">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped">
                                                    <thead class="thead-dark">
                                                        <tr>
                                                            <th>Date</th>
                                                            <th>Time</th>
                                                            <th>Exam</th>
                                                            <th>Room</th>
                                                            <th>Role</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $upcomingAssignments = $invigilator->assignments
                                                                ->filter(function($assignment) {
                                                                    return $assignment->seatingPlan->exam_date >= date('Y-m-d');
                                                                })
                                                                ->sortBy('seatingPlan.exam_date');
                                                        @endphp

                                                        @forelse($upcomingAssignments as $assignment)
                                                            <tr>
                                                                <td>{{ \Carbon\Carbon::parse($assignment->seatingPlan->exam_date)->format('M d, Y') }}</td>
                                                                <td>{{ \Carbon\Carbon::parse($assignment->seatingPlan->start_time)->format('h:i A') }}</td>
                                                                <td>
                                                                    <a href="{{ route('seating-plans.show', $assignment->seatingPlan) }}">
                                                                        {{ $assignment->seatingPlan->title }}
                                                                    </a>
                                                                </td>
                                                                <td>
                                                                    <a href="{{ route('rooms.show', $assignment->room) }}">
                                                                        {{ $assignment->room->name }} ({{ $assignment->room->code }})
                                                                    </a>
                                                                </td>
                                                                <td>
                                                                    @if($assignment->is_chief_invigilator)
                                                                        <span class="badge badge-danger">Chief Invigilator</span>
                                                                    @else
                                                                        <span class="badge badge-info">Invigilator</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <a href="{{ route('seating-plans.show', $assignment->seatingPlan) }}" class="btn btn-sm btn-info">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="6" class="text-center">No upcoming assignments found.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="past" role="tabpanel" aria-labelledby="past-tab">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped">
                                                    <thead class="thead-dark">
                                                        <tr>
                                                            <th>Date</th>
                                                            <th>Time</th>
                                                            <th>Exam</th>
                                                            <th>Room</th>
                                                            <th>Role</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $pastAssignments = $invigilator->assignments
                                                                ->filter(function($assignment) {
                                                                    return $assignment->seatingPlan->exam_date < date('Y-m-d');
                                                                })
                                                                ->sortByDesc('seatingPlan.exam_date');
                                                        @endphp

                                                        @forelse($pastAssignments as $assignment)
                                                            <tr>
                                                                <td>{{ \Carbon\Carbon::parse($assignment->seatingPlan->exam_date)->format('M d, Y') }}</td>
                                                                <td>{{ \Carbon\Carbon::parse($assignment->seatingPlan->start_time)->format('h:i A') }}</td>
                                                                <td>
                                                                    <a href="{{ route('seating-plans.show', $assignment->seatingPlan) }}">
                                                                        {{ $assignment->seatingPlan->title }}
                                                                    </a>
                                                                </td>
                                                                <td>
                                                                    <a href="{{ route('rooms.show', $assignment->room) }}">
                                                                        {{ $assignment->room->name }} ({{ $assignment->room->code }})
                                                                    </a>
                                                                </td>
                                                                <td>
                                                                    @if($assignment->is_chief_invigilator)
                                                                        <span class="badge badge-danger">Chief Invigilator</span>
                                                                    @else
                                                                        <span class="badge badge-info">Invigilator</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <span class="badge badge-{{ $assignment->status == 'completed' ? 'success' : 'secondary' }}">
                                                                        {{ ucfirst($assignment->status) }}
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="6" class="text-center">No past assignments found.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="all" role="tabpanel" aria-labelledby="all-tab">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped" id="all-assignments-table">
                                                    <thead class="thead-dark">
                                                        <tr>
                                                            <th>Date</th>
                                                            <th>Time</th>
                                                            <th>Exam</th>
                                                            <th>Room</th>
                                                            <th>Role</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($invigilator->assignments->sortByDesc('seatingPlan.exam_date') as $assignment)
                                                            <tr>
                                                                <td>{{ \Carbon\Carbon::parse($assignment->seatingPlan->exam_date)->format('M d, Y') }}</td>
                                                                <td>{{ \Carbon\Carbon::parse($assignment->seatingPlan->start_time)->format('h:i A') }}</td>
                                                                <td>
                                                                    <a href="{{ route('seating-plans.show', $assignment->seatingPlan) }}">
                                                                        {{ $assignment->seatingPlan->title }}
                                                                    </a>
                                                                </td>
                                                                <td>
                                                                    <a href="{{ route('rooms.show', $assignment->room) }}">
                                                                        {{ $assignment->room->name }} ({{ $assignment->room->code }})
                                                                    </a>
                                                                </td>
                                                                <td>
                                                                    @if($assignment->is_chief_invigilator)
                                                                        <span class="badge badge-danger">Chief Invigilator</span>
                                                                    @else
                                                                        <span class="badge badge-info">Invigilator</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <span class="badge badge-{{ $assignment->status == 'completed' ? 'success' : ($assignment->status == 'upcoming' ? 'warning' : 'secondary') }}">
                                                                        {{ ucfirst($assignment->status) }}
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="6" class="text-center">No assignments found.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header bg-warning text-white">
                                    <h6 class="mb-0">Duty Schedule</h6>
                                </div>
                                <div class="card-body">
                                    <div id="calendar"></div>
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

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css" rel="stylesheet">
<style>
    .avatar-circle {
        width: 100px;
        height: 100px;
        background-color: #3e92cc;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0 auto;
    }
    
    .avatar-text {
        color: white;
        font-size: 36px;
        font-weight: bold;
    }
    
    #calendar {
        height: 400px;
    }
    
    .fc-event {
        cursor: pointer;
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
<script>
    $(document).ready(function() {
        $('#all-assignments-table').DataTable({
            "responsive": true,
            "language": {
                "search": "Search assignments:",
                "zeroRecords": "No matching assignments found"
            }
        });
        
        // Initialize FullCalendar
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listMonth'
            },
            events: [
                @foreach($invigilator->assignments as $assignment)
                {
                    title: '{{ $assignment->seatingPlan->title }}',
                    start: '{{ $assignment->seatingPlan->exam_date }}T{{ $assignment->seatingPlan->start_time }}',
                    end: '{{ $assignment->seatingPlan->exam_date }}T{{ $assignment->seatingPlan->end_time }}',
                    url: '{{ route('seating-plans.show', $assignment->seatingPlan) }}',
                    backgroundColor: '{{ $assignment->is_chief_invigilator ? "#e74a3b" : "#3e92cc" }}',
                    borderColor: '{{ $assignment->is_chief_invigilator ? "#e74a3b" : "#3e92cc" }}',
                    textColor: '#ffffff',
                    extendedProps: {
                        room: '{{ $assignment->room->name }} ({{ $assignment->room->code }})',
                        role: '{{ $assignment->is_chief_invigilator ? "Chief Invigilator" : "Invigilator" }}'
                    }
                },
                @endforeach
            ],
            eventClick: function(info) {
                if (info.event.url) {
                    window.open(info.event.url);
                    info.jsEvent.preventDefault();
                }
            },
            eventDidMount: function(info) {
                $(info.el).tooltip({
                    title: info.event.title + ' - ' + info.event.extendedProps.room + ' - ' + info.event.extendedProps.role,
                    placement: 'top',
                    trigger: 'hover',
                    container: 'body'
                });
            }
        });
        calendar.render();
    });
</script>
@endsection

