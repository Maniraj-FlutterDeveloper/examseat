@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Room Invigilator Assignments</h1>
        <a href="{{ route('room-invigilator-assignments.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i>New Assignment
        </a>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Filter Assignments</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('room-invigilator-assignments.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="exam_date" class="form-label">Exam Date</label>
                        <input type="date" class="form-control" id="exam_date" name="exam_date" value="{{ request('exam_date') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="time_slot" class="form-label">Time Slot</label>
                        <select class="form-select" id="time_slot" name="time_slot">
                            <option value="">All Time Slots</option>
                            <option value="Morning (9:00 AM - 12:00 PM)" {{ request('time_slot') == 'Morning (9:00 AM - 12:00 PM)' ? 'selected' : '' }}>Morning (9:00 AM - 12:00 PM)</option>
                            <option value="Afternoon (2:00 PM - 5:00 PM)" {{ request('time_slot') == 'Afternoon (2:00 PM - 5:00 PM)' ? 'selected' : '' }}>Afternoon (2:00 PM - 5:00 PM)</option>
                            <option value="Evening (6:00 PM - 9:00 PM)" {{ request('time_slot') == 'Evening (6:00 PM - 9:00 PM)' ? 'selected' : '' }}>Evening (6:00 PM - 9:00 PM)</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="block_id" class="form-label">Block</label>
                        <select class="form-select" id="block_id" name="block_id">
                            <option value="">All Blocks</option>
                            @foreach($blocks as $block)
                                <option value="{{ $block->id }}" {{ request('block_id') == $block->id ? 'selected' : '' }}>
                                    {{ $block->block_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="department" class="form-label">Department</label>
                        <select class="form-select" id="department" name="department">
                            <option value="">All Departments</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>
                                    {{ $dept }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Search by invigilator name, room number, etc.">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role">
                            <option value="">All Roles</option>
                            <option value="chief" {{ request('role') == 'chief' ? 'selected' : '' }}>Chief Invigilator</option>
                            <option value="assistant" {{ request('role') == 'assistant' ? 'selected' : '' }}>Assistant Invigilator</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end mb-3">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end w-100">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Filter
                            </button>
                            <a href="{{ route('room-invigilator-assignments.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Clear
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Exam Date</th>
                            <th>Time Slot</th>
                            <th>Block</th>
                            <th>Room</th>
                            <th>Invigilator</th>
                            <th>Department</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignments as $assignment)
                            <tr>
                                <td>{{ $assignment->id }}</td>
                                <td>{{ $assignment->exam_date->format('M d, Y') }}</td>
                                <td>{{ $assignment->time_slot }}</td>
                                <td>{{ $assignment->room->block->block_name }}</td>
                                <td>
                                    <a href="{{ route('rooms.show', $assignment->room_id) }}">
                                        {{ $assignment->room->room_number }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('invigilators.show', $assignment->invigilator_id) }}">
                                        {{ $assignment->invigilator->name }}
                                    </a>
                                </td>
                                <td>{{ $assignment->invigilator->department }}</td>
                                <td>
                                    @if($assignment->is_chief_invigilator)
                                        <span class="badge bg-primary">Chief Invigilator</span>
                                    @else
                                        <span class="badge bg-secondary">Assistant Invigilator</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('room-invigilator-assignments.show', $assignment->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('room-invigilator-assignments.edit', $assignment->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $assignment->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal{{ $assignment->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $assignment->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $assignment->id }}">Confirm Delete</h5>
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
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                                    <p>No invigilator assignments found.</p>
                                    <a href="{{ route('room-invigilator-assignments.create') }}" class="btn btn-primary">Create Assignment</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $assignments->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
