@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Question Papers</h1>
        <div>
            <a href="{{ route('admin.question-papers.create') }}" class="btn btn-primary me-2">
                <i class="fas fa-plus-circle me-2"></i>Create New Question Paper
            </a>
            <a href="{{ route('admin.blueprints.index') }}" class="btn btn-info">
                <i class="fas fa-file-alt me-2"></i>Manage Blueprints
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
    
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Filter Question Papers</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.question-papers.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
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
                <div class="col-md-3">
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
                <div class="col-md-3">
                    <label for="total_marks" class="form-label">Total Marks</label>
                    <select class="form-select" id="total_marks" name="total_marks">
                        <option value="">All</option>
                        @foreach([10, 20, 25, 50, 70, 75, 80, 100] as $marks)
                            <option value="{{ $marks }}" {{ request('total_marks') == $marks ? 'selected' : '' }}>{{ $marks }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                    <a href="{{ route('admin.question-papers.index') }}" class="btn btn-secondary">Reset</a>
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
                            <th>Duration</th>
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
                                    <a href="{{ route('admin.subjects.show', $paper->subject_id) }}">
                                        {{ $paper->subject->subject_name }}
                                    </a>
                                </td>
                                <td>
                                    @if($paper->blueprint_id)
                                        <a href="{{ route('admin.blueprints.show', $paper->blueprint_id) }}">
                                            {{ $paper->blueprint->title }}
                                        </a>
                                    @else
                                        <span class="badge bg-secondary">Custom</span>
                                    @endif
                                </td>
                                <td>{{ $paper->total_marks }}</td>
                                <td>{{ $paper->duration }} mins</td>
                                <td>{{ $paper->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.question-papers.show', $paper->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.question-papers.edit', $paper->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.question-papers.export-pdf', $paper->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-file-pdf"></i>
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
                                                    <p>Are you sure you want to delete the question paper <strong>{{ $paper->title }}</strong>?</p>
                                                    <div class="alert alert-warning">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                        This action cannot be undone. The question paper will be permanently removed from the database.
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('admin.question-papers.destroy', $paper->id) }}" method="POST" class="d-inline">
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
                                    <div>
                                        <a href="{{ route('admin.question-papers.create') }}" class="btn btn-primary me-2">Create Question Paper</a>
                                        <a href="{{ route('admin.blueprints.index') }}" class="btn btn-info">Manage Blueprints</a>
                                    </div>
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
    
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Question Paper Creation Methods</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Blueprint-Based Generation</h5>
                            <p class="card-text">Create question papers based on predefined blueprints that specify the distribution of questions across topics, difficulty levels, and cognitive domains.</p>
                            <ul>
                                <li>Ensures consistent question paper structure</li>
                                <li>Balances question difficulty and cognitive levels</li>
                                <li>Provides comprehensive coverage of the syllabus</li>
                                <li>Saves time in question paper creation</li>
                            </ul>
                            <a href="{{ route('admin.blueprints.index') }}" class="btn btn-primary">View Blueprints</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Custom Question Paper</h5>
                            <p class="card-text">Create question papers by manually selecting questions from the question bank or by using random selection with specific filters.</p>
                            <ul>
                                <li>Complete control over question selection</li>
                                <li>Flexibility to create unique question papers</li>
                                <li>Ability to mix questions from different topics</li>
                                <li>Option to reuse questions from previous papers</li>
                            </ul>
                            <a href="{{ route('admin.question-papers.create') }}" class="btn btn-primary">Create Custom Paper</a>
                        </div>
                    </div>
                </div>
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
                fetch(`{{ url('admin/blueprints/by-subject') }}/${subjectId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(blueprint => {
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

