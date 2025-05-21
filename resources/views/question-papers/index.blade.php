@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Question Papers</h1>
        <a href="{{ route('question-papers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i>Generate New Question Paper
        </a>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Filter Question Papers</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('question-papers.index') }}" method="GET">
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
                        <label for="blueprint_id" class="form-label">Blueprint</label>
                        <select class="form-select" id="blueprint_id" name="blueprint_id">
                            <option value="">All Blueprints</option>
                            @foreach($blueprints as $blueprint)
                                <option value="{{ $blueprint->id }}" {{ request('blueprint_id') == $blueprint->id ? 'selected' : '' }}>
                                    {{ $blueprint->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Search by title or description">
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
                            <th>Title</th>
                            <th>Subject</th>
                            <th>Blueprint</th>
                            <th>Total Marks</th>
                            <th>Questions</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($questionPapers as $paper)
                            <tr>
                                <td>{{ $paper->id }}</td>
                                <td>{{ $paper->title }}</td>
                                <td>
                                    <a href="{{ route('subjects.show', $paper->subject_id) }}">
                                        {{ $paper->subject->subject_name }}
                                    </a>
                                </td>
                                <td>
                                    @if($paper->blueprint_id)
                                        <a href="{{ route('blueprints.show', $paper->blueprint_id) }}">
                                            {{ $paper->blueprint->title }}
                                        </a>
                                    @else
                                        <span class="badge bg-secondary">Custom</span>
                                    @endif
                                </td>
                                <td>{{ $paper->total_marks }}</td>
                                <td>{{ $paper->questions->count() }}</td>
                                <td>{{ $paper->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('question-papers.show', $paper->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('question-papers.edit', $paper->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('question-papers.download', $paper->id) }}" class="btn btn-sm btn-success">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $paper->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal{{ $paper->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $paper->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $paper->id }}">Confirm Delete</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to delete the question paper <strong>{{ $paper->title }}</strong>?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('question-papers.destroy', $paper->id) }}" method="POST" class="d-inline">
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
                                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                    <p>No question papers found.</p>
                                    <a href="{{ route('question-papers.create') }}" class="btn btn-primary">Generate Question Paper</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $questionPapers->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const subjectSelect = document.getElementById('subject_id');
        const blueprintSelect = document.getElementById('blueprint_id');
        
        // Subject change event
        subjectSelect.addEventListener('change', function() {
            const subjectId = this.value;
            
            // Clear blueprint select
            blueprintSelect.innerHTML = '<option value="">All Blueprints</option>';
            
            if (subjectId) {
                // Fetch blueprints for the selected subject
                fetch(`/api/subjects/${subjectId}/blueprints`)
                    .then(response => response.json())
                    .then(blueprints => {
                        blueprints.forEach(blueprint => {
                            const option = document.createElement('option');
                            option.value = blueprint.id;
                            option.textContent = blueprint.title;
                            blueprintSelect.appendChild(option);
                        });
                    });
            }
        });
    });
</script>
@endpush
@endsection

