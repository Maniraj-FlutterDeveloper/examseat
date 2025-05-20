@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Question Paper Blueprints</h1>
        <a href="{{ route('blueprints.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i>Create New Blueprint
        </a>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Filter Blueprints</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('blueprints.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="subject_id" class="form-label">Subject</label>
                        <select class="form-select" id="subject_id" name="subject_id">
                            <option value="">All Subjects</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->subject_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Search by title or description">
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
                            <th>Title</th>
                            <th>Subject</th>
                            <th>Total Marks</th>
                            <th>Duration (mins)</th>
                            <th>Question Papers</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($blueprints as $blueprint)
                            <tr>
                                <td>{{ $blueprint->id }}</td>
                                <td>{{ $blueprint->title }}</td>
                                <td>
                                    <a href="{{ route('subjects.show', $blueprint->subject_id) }}">
                                        {{ $blueprint->subject->subject_name }}
                                    </a>
                                </td>
                                <td>{{ $blueprint->total_marks }}</td>
                                <td>{{ $blueprint->duration }}</td>
                                <td>{{ $blueprint->questionPapers->count() }}</td>
                                <td>{{ $blueprint->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('blueprints.show', $blueprint->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('blueprints.edit', $blueprint->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('question-papers.create', ['blueprint_id' => $blueprint->id]) }}" class="btn btn-sm btn-success">
                                            <i class="fas fa-file-alt"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $blueprint->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal{{ $blueprint->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $blueprint->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $blueprint->id }}">Confirm Delete</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to delete the blueprint <strong>{{ $blueprint->title }}</strong>?
                                                    @if($blueprint->questionPapers->count() > 0)
                                                        <div class="alert alert-warning mt-3">
                                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                                            This blueprint has {{ $blueprint->questionPapers->count() }} question papers associated with it. Deleting this blueprint will not delete the associated question papers.
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('blueprints.destroy', $blueprint->id) }}" method="POST" class="d-inline">
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
                                    <i class="fas fa-drafting-compass fa-3x text-muted mb-3"></i>
                                    <p>No blueprints found.</p>
                                    <a href="{{ route('blueprints.create') }}" class="btn btn-primary">Create Blueprint</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $blueprints->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

