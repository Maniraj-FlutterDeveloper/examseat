@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Units</h1>
        <a href="{{ route('units.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i>Add New Unit
        </a>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Filter Units</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('units.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="subject_id" class="form-label">Subject</label>
                        <select class="form-select" id="subject_id" name="subject_id">
                            <option value="">All Subjects</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->subject_name }} ({{ $subject->subject_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Unit name or number">
                    </div>
                    <div class="col-md-4 d-flex align-items-end mb-3">
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
                            <th>Unit Name</th>
                            <th>Unit Number</th>
                            <th>Subject</th>
                            <th>Topics</th>
                            <th>Questions</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($units as $unit)
                            <tr>
                                <td>{{ $unit->id }}</td>
                                <td>{{ $unit->unit_name }}</td>
                                <td>{{ $unit->unit_number }}</td>
                                <td>
                                    <a href="{{ route('subjects.show', $unit->subject_id) }}">
                                        {{ $unit->subject->subject_name }}
                                    </a>
                                </td>
                                <td>{{ $unit->topics_count }}</td>
                                <td>{{ $unit->questions_count }}</td>
                                <td>{{ $unit->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('units.show', $unit->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('units.edit', $unit->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $unit->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal{{ $unit->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $unit->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $unit->id }}">Confirm Delete</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to delete the unit <strong>{{ $unit->unit_name }}</strong>?
                                                    @if($unit->topics_count > 0)
                                                        <div class="alert alert-warning mt-3">
                                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                                            This unit has {{ $unit->topics_count }} topics and {{ $unit->questions_count }} questions associated with it. Deleting this unit will also delete all associated topics and questions.
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('units.destroy', $unit->id) }}" method="POST" class="d-inline">
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
                                    <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                                    <p>No units found.</p>
                                    <a href="{{ route('units.create') }}" class="btn btn-primary">Add Unit</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $units->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

