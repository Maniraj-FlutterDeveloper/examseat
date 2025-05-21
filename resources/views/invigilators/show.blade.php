@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Invigilator Details</h1>
        <div>
            <a href="{{ route('invigilators.edit', $invigilator->id) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('invigilators.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Invigilators
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Invigilator Information</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th style="width: 40%">ID:</th>
                            <td>{{ $invigilator->id }}</td>
                        </tr>
                        <tr>
                            <th>Name:</th>
                            <td>{{ $invigilator->name }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $invigilator->email }}</td>
                        </tr>
                        <tr>
                            <th>Phone:</th>
                            <td>{{ $invigilator->phone }}</td>
                        </tr>
                        <tr>
                            <th>Department:</th>
                            <td>{{ $invigilator->department }}</td>
                        </tr>
                        <tr>
                            <th>Designation:</th>
                            <td>{{ $invigilator->designation }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                @if($invigilator->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Created At:</th>
                            <td>{{ $invigilator->created_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At:</th>
                            <td>{{ $invigilator->updated_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Address</h5>
                </div>
                <div class="card-body">
                    @if($invigilator->address)
                        <p>{{ $invigilator->address }}</p>
                    @else
                        <p class="text-muted">No address provided.</p>
                    @endif
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Notes</h5>
                </div>
                <div class="card-body">
                    @if($invigilator->notes)
                        <p>{{ $invigilator->notes }}</p>
                    @else
                        <p class="text-muted">No notes provided.</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Room Assignments</h5>
                    <a href="{{ route('room-invigilator-assignments.create', ['invigilator_id' => $invigilator->id]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus-circle me-1"></i>Assign to Room
                    </a>
                </div>
                <div class="card-body">
                    @if($invigilator->roomInvigilatorAssignments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Room</th>
                                        <th>Block</th>
                                        <th>Exam Date</th>
                                        <th>Time Slot</th>
                                        <th>Role</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invigilator->roomInvigilatorAssignments as $assignment)
                                        <tr>
                                            <td>{{ $assignment->id }}</td>
                                            <td>
                                                <a href="{{ route('rooms.show', $assignment->room_id) }}">
                                                    {{ $assignment->room->room_number }}
                                                </a>
                                            </td>
                                            <td>{{ $assignment->room->block->block_name }}</td>
                                            <td>{{ $assignment->exam_date->format('M d, Y') }}</td>
                                            <td>{{ $assignment->time_slot }}</td>
                                            <td>
                                                @if($assignment->is_chief_invigilator)
                                                    <span class="badge bg-primary">Chief Invigilator</span>
                                                @else
                                                    <span class="badge bg-secondary">Assistant Invigilator</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('room-invigilator-assignments.edit', $assignment->id) }}" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAssignmentModal{{ $assignment->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                                
                                                <!-- Delete Modal -->
                                                <div class="modal fade" id="deleteAssignmentModal{{ $assignment->id }}" tabindex="-1" aria-labelledby="deleteAssignmentModalLabel{{ $assignment->id }}" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="deleteAssignmentModalLabel{{ $assignment->id }}">Confirm Delete</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Are you sure you want to delete this room assignment?</p>
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
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                            <p>No room assignments found for this invigilator.</p>
                            <a href="{{ route('room-invigilator-assignments.create', ['invigilator_id' => $invigilator->id]) }}" class="btn btn-primary">Assign to Room</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
