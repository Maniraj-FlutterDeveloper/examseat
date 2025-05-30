@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Topics</h1>
        <a href="{{ route('topics.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i>Add New Topic
        </a>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Filter Topics</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('topics.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-3 mb-3">
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
                    <div class="col-md-3 mb-3">
                        <label for="unit_id" class="form-label">Unit</label>
                        <select class="form-select" id="unit_id" name="unit_id">
                            <option value="">All Units</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->unit_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Topic name or number">
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
                            <th>Topic Name</th>
                            <th>Topic Number</th>
                            <th>Unit</th>
                            <th>Subject</th>
                            <th>Questions</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topics as $topic)
                            <tr>
                                <td>{{ $topic->id }}</td>
                                <td>{{ $topic->topic_name }}</td>
                                <td>{{ $topic->topic_number }}</td>
                                <td>
                                    <a href="{{ route('units.show', $topic->unit_id) }}">
                                        {{ $topic->unit->unit_name }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('subjects.show', $topic->unit->subject_id) }}">
                                        {{ $topic->unit->subject->subject_name }}
                                    </a>
                                </td>
                                <td>{{ $topic->questions_count }}</td>
                                <td>{{ $topic->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('topics.show', $topic->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('topics.edit', $topic->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $topic->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal{{ $topic->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $topic->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $topic->id }}">Confirm Delete</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to delete the topic <strong>{{ $topic->topic_name }}</strong>?
                                                    @if($topic->questions_count > 0)
                                                        <div class="alert alert-warning mt-3">
                                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                                            This topic has {{ $topic->questions_count }} questions associated with it. Deleting this topic will also delete all associated questions.
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('topics.destroy', $topic->id) }}" method="POST" class="d-inline">
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
                                    <i class="fas fa-book-reader fa-3x text-muted mb-3"></i>
                                    <p>No topics found.</p>
                                    <a href="{{ route('topics.create') }}" class="btn btn-primary">Add Topic</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $topics->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const subjectSelect = document.getElementById('subject_id');
        const unitSelect = document.getElementById('unit_id');
        
        subjectSelect.addEventListener('change', function() {
            const subjectId = this.value;
            
            // Clear unit select
            unitSelect.innerHTML = '<option value="">All Units</option>';
            
            if (subjectId) {
                // Fetch units for the selected subject
                fetch(`/api/subjects/${subjectId}/units`)
                    .then(response => response.json())
                    .then(units => {
                        units.forEach(unit => {
                            const option = document.createElement('option');
                            option.value = unit.id;
                            option.textContent = unit.unit_name;
                            unitSelect.appendChild(option);
                        });
                    });
            }
        });
    });
</script>
@endpush
@endsection

