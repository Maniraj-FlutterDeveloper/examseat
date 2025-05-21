@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Seating Plans</h1>
        <a href="{{ route('seating-plans.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i>Generate New Seating Plan
        </a>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Filter Seating Plans</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('seating-plans.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="exam_date" class="form-label">Exam Date</label>
                        <input type="date" class="form-control" id="exam_date" name="exam_date" value="{{ request('exam_date') }}">
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
                        <label for="course_id" class="form-label">Course</label>
                        <select class="form-select" id="course_id" name="course_id">
                            <option value="">All Courses</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->course_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end mb-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>Filter
                        </button>
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
                            <th>Blocks</th>
                            <th>Rooms</th>
                            <th>Students</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($seatingPlans as $plan)
                            <tr>
                                <td>{{ $plan->id }}</td>
                                <td>{{ $plan->exam_date->format('M d, Y') }}</td>
                                <td>{{ $plan->time_slot }}</td>
                                <td>{{ $plan->blocks_count }}</td>
                                <td>{{ $plan->rooms_count }}</td>
                                <td>{{ $plan->students_count }}</td>
                                <td>{{ $plan->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('seating-plans.show', $plan->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('seating-plans.edit', $plan->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('seating-plans.download', $plan->id) }}" class="btn btn-sm btn-success">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $plan->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal{{ $plan->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $plan->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $plan->id }}">Confirm Delete</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Are you sure you want to delete this seating plan for <strong>{{ $plan->exam_date->format('M d, Y') }}</strong>?</p>
                                                    <div class="alert alert-warning">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                        This will permanently delete all seating assignments for {{ $plan->students_count }} students across {{ $plan->rooms_count }} rooms.
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('seating-plans.destroy', $plan->id) }}" method="POST" class="d-inline">
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
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-chair fa-3x text-muted mb-3"></i>
                                    <p>No seating plans found.</p>
                                    <a href="{{ route('seating-plans.create') }}" class="btn btn-primary">Generate Seating Plan</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $seatingPlans->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
