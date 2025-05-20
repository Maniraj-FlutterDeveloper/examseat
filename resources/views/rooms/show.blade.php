@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Room Details</h1>
        <div>
            <a href="{{ route('rooms.edit', $room->id) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('rooms.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Rooms
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Room Information</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th style="width: 30%">ID:</th>
                            <td>{{ $room->id }}</td>
                        </tr>
                        <tr>
                            <th>Room Number:</th>
                            <td>{{ $room->room_number }}</td>
                        </tr>
                        <tr>
                            <th>Block:</th>
                            <td>
                                <a href="{{ route('blocks.show', $room->block_id) }}">
                                    {{ $room->block->block_name }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Capacity:</th>
                            <td>{{ $room->capacity }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                @if($room->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Description:</th>
                            <td>{{ $room->description ?: 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Created At:</th>
                            <td>{{ $room->created_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At:</th>
                            <td>{{ $room->updated_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Seating Plans</h5>
                    <a href="{{ route('seating-plans.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus-circle me-1"></i>Create Seating Plan
                    </a>
                </div>
                <div class="card-body">
                    @if($room->seatingPlans->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Exam</th>
                                        <th>Student</th>
                                        <th>Seat Number</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($room->seatingPlans as $plan)
                                        <tr>
                                            <td>{{ $plan->exam_name }}</td>
                                            <td>{{ $plan->student->name }}</td>
                                            <td>{{ $plan->seat_number }}</td>
                                            <td>{{ $plan->exam_date->format('M d, Y') }}</td>
                                            <td>
                                                <a href="{{ route('seating-plans.show', $plan->id) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-chair fa-3x text-muted mb-3"></i>
                            <p>No seating plans have been created for this room yet.</p>
                            <a href="{{ route('seating-plans.create') }}" class="btn btn-primary">Create Seating Plan</a>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Invigilator Assignments</h5>
                    <a href="{{ route('room-invigilator-assignments.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus-circle me-1"></i>Assign Invigilator
                    </a>
                </div>
                <div class="card-body">
                    @if($room->invigilatorAssignments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Invigilator</th>
                                        <th>Exam</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($room->invigilatorAssignments as $assignment)
                                        <tr>
                                            <td>{{ $assignment->invigilator->name }}</td>
                                            <td>{{ $assignment->exam_name }}</td>
                                            <td>{{ $assignment->exam_date->format('M d, Y') }}</td>
                                            <td>
                                                <a href="{{ route('room-invigilator-assignments.show', $assignment->id) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-user-tie fa-3x text-muted mb-3"></i>
                            <p>No invigilators have been assigned to this room yet.</p>
                            <a href="{{ route('room-invigilator-assignments.create') }}" class="btn btn-primary">Assign Invigilator</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

