@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Invigilator Assignment Details</h1>
        <div>
            <a href="{{ route('room-invigilator-assignments.edit', $assignment->id) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('room-invigilator-assignments.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Assignments
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Assignment Information</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th style="width: 40%">ID:</th>
                            <td>{{ $assignment->id }}</td>
                        </tr>
                        <tr>
                            <th>Exam Date:</th>
                            <td>{{ $assignment->exam_date->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <th>Time Slot:</th>
                            <td>{{ $assignment->time_slot }}</td>
                        </tr>
                        <tr>
                            <th>Role:</th>
                            <td>
                                @if($assignment->is_chief_invigilator)
                                    <span class="badge bg-primary">Chief Invigilator</span>
                                @else
                                    <span class="badge bg-secondary">Assistant Invigilator</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Created At:</th>
                            <td>{{ $assignment->created_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At:</th>
                            <td>{{ $assignment->updated_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Notes</h5>
                </div>
                <div class="card-body">
                    @if($assignment->notes)
                        <p>{{ $assignment->notes }}</p>
                    @else
                        <p class="text-muted">No notes provided.</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Invigilator Information</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <h6>{{ $assignment->invigilator->name }}</h6>
                        <a href="{{ route('invigilators.show', $assignment->invigilator_id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-external-link-alt me-1"></i>View Profile
                        </a>
                    </div>
                    
                    <table class="table">
                        <tr>
                            <th style="width: 40%">Email:</th>
                            <td>{{ $assignment->invigilator->email }}</td>
                        </tr>
                        <tr>
                            <th>Phone:</th>
                            <td>{{ $assignment->invigilator->phone }}</td>
                        </tr>
                        <tr>
                            <th>Department:</th>
                            <td>{{ $assignment->invigilator->department }}</td>
                        </tr>
                        <tr>
                            <th>Designation:</th>
                            <td>{{ $assignment->invigilator->designation }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                @if($assignment->invigilator->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Room Information</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <h6>{{ $assignment->room->room_number }}</h6>
                        <a href="{{ route('rooms.show', $assignment->room_id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-external-link-alt me-1"></i>View Room
                        </a>
                    </div>
                    
                    <table class="table">
                        <tr>
                            <th style="width: 40%">Block:</th>
                            <td>
                                <a href="{{ route('blocks.show', $assignment->room->block_id) }}">
                                    {{ $assignment->room->block->block_name }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Capacity:</th>
                            <td>{{ $assignment->room->capacity }} students</td>
                        </tr>
                        <tr>
                            <th>Floor:</th>
                            <td>{{ $assignment->room->floor }}</td>
                        </tr>
                        <tr>
                            <th>Room Type:</th>
                            <td>{{ $assignment->room->room_type }}</td>
                        </tr>
                    </table>
                    
                    <div class="mt-3">
                        <h6>Other Invigilators Assigned to This Room</h6>
                        @php
                            $otherAssignments = $otherInvigilators->where('room_id', $assignment->room_id)
                                ->where('exam_date', $assignment->exam_date)
                                ->where('time_slot', $assignment->time_slot)
                                ->where('id', '!=', $assignment->id);
                        @endphp
                        
                        @if($otherAssignments->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Department</th>
                                            <th>Role</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($otherAssignments as $otherAssignment)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('room-invigilator-assignments.show', $otherAssignment->id) }}">
                                                        {{ $otherAssignment->invigilator->name }}
                                                    </a>
                                                </td>
                                                <td>{{ $otherAssignment->invigilator->department }}</td>
                                                <td>
                                                    @if($otherAssignment->is_chief_invigilator)
                                                        <span class="badge bg-primary">Chief</span>
                                                    @else
                                                        <span class="badge bg-secondary">Assistant</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">No other invigilators assigned to this room for this exam session.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">Other Assignments for This Invigilator</h5>
        </div>
        <div class="card-body">
            @php
                $invigilatorAssignments = $otherInvigilators->where('invigilator_id', $assignment->invigilator_id)
                    ->where('id', '!=', $assignment->id);
            @endphp
            
            @if($invigilatorAssignments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Exam Date</th>
                                <th>Time Slot</th>
                                <th>Block</th>
                                <th>Room</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invigilatorAssignments as $invigilatorAssignment)
                                <tr>
                                    <td>{{ $invigilatorAssignment->exam_date->format('M d, Y') }}</td>
                                    <td>{{ $invigilatorAssignment->time_slot }}</td>
                                    <td>{{ $invigilatorAssignment->room->block->block_name }}</td>
                                    <td>{{ $invigilatorAssignment->room->room_number }}</td>
                                    <td>
                                        @if($invigilatorAssignment->is_chief_invigilator)
                                            <span class="badge bg-primary">Chief Invigilator</span>
                                        @else
                                            <span class="badge bg-secondary">Assistant Invigilator</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('room-invigilator-assignments.show', $invigilatorAssignment->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted">No other assignments found for this invigilator.</p>
            @endif
        </div>
    </div>
    
    <div class="mt-4 d-flex justify-content-between">
        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
            <i class="fas fa-trash me-2"></i>Delete Assignment
        </button>
        
        <a href="{{ route('room-invigilator-assignments.print', $assignment->id) }}" class="btn btn-success">
            <i class="fas fa-print me-2"></i>Print Assignment Letter
        </a>
    </div>
    
    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this assignment?</p>
                    <p><strong>Invigilator:</strong> {{ $assignment->invigilator->name }}</p>
                    <p><strong>Room:</strong> {{ $assignment->room->room_number }}</p>
                    <p><strong>Date:</strong> {{ $assignment->exam_date->format('M d, Y') }}</p>
                    <p><strong>Time Slot:</strong> {{ $assignment->time_slot }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('room-invigilator-assignments.destroy', $assignment->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
