@extends('layouts.admin')

@section('title', 'Manage Seating Plans')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Manage Seating Plans</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.seating-plans.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i> Create New Seating Plan
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Filter Seating Plans</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.seating-plans.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="exam_name" class="form-label">Exam Name</label>
                    <input type="text" class="form-control" id="exam_name" name="exam_name" value="{{ request('exam_name') }}">
                </div>
                
                <div class="col-md-3">
                    <label for="exam_date" class="form-label">Exam Date</label>
                    <input type="date" class="form-control" id="exam_date" name="exam_date" value="{{ request('exam_date') }}">
                </div>
                
                <div class="col-md-3">
                    <label for="block_id" class="form-label">Block</label>
                    <select name="block_id" id="block_id" class="form-select">
                        <option value="">All Blocks</option>
                        @foreach($blocks as $block)
                            <option value="{{ $block->id }}" {{ request('block_id') == $block->id ? 'selected' : '' }}>
                                {{ $block->block_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-2"></i> Filter
                    </button>
                    <a href="{{ route('admin.seating-plans.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-undo me-2"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">All Seating Plans</h5>
            <span class="badge bg-primary">{{ $seatingPlans->total() }} Plans</span>
        </div>
        <div class="card-body">
            @if($seatingPlans->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Exam Name</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Blocks</th>
                            <th>Rooms</th>
                            <th>Students</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($seatingPlanGroups as $group)
                        <tr>
                            <td>{{ $group->exam_name }}</td>
                            <td>{{ \Carbon\Carbon::parse($group->exam_date)->format('M d, Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($group->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($group->end_time)->format('h:i A') }}</td>
                            <td>{{ $group->blocks_count }}</td>
                            <td>{{ $group->rooms_count }}</td>
                            <td>{{ $group->students_count }}</td>
                            <td>
                                <span class="badge bg-{{ $group->status === 'active' ? 'success' : ($group->status === 'completed' ? 'info' : 'secondary') }}">
                                    {{ ucfirst($group->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.seating-plans.show', ['seating_plan' => $group->id]) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.seating-plans.edit', ['seating_plan' => $group->id]) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.seating-plans.print', ['exam_name' => $group->exam_name, 'exam_date' => $group->exam_date]) }}" class="btn btn-sm btn-success" target="_blank">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $group->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>

                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal{{ $group->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $group->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteModalLabel{{ $group->id }}">Confirm Delete</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete the seating plan for <strong>{{ $group->exam_name }}</strong> on <strong>{{ \Carbon\Carbon::parse($group->exam_date)->format('M d, Y') }}</strong>?
                                                <div class="alert alert-warning mt-3">
                                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                                    This will delete all seating assignments for this exam. This action cannot be undone.
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('admin.seating-plans.destroy-group', ['exam_name' => $group->exam_name, 'exam_date' => $group->exam_date]) }}" method="POST">
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
            <div class="mt-4">
                {{ $seatingPlans->links() }}
            </div>
            @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                No seating plans found. Click the "Create New Seating Plan" button to create one.
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
