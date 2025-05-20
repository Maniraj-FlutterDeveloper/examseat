@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Question Paper Blueprints</h1>
        <a href="{{ route('admin.blueprints.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i>Create New Blueprint
        </a>
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
            <h5 class="card-title mb-0">Filter Blueprints</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.blueprints.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
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
                <div class="col-md-4">
                    <label for="total_marks" class="form-label">Total Marks</label>
                    <select class="form-select" id="total_marks" name="total_marks">
                        <option value="">All</option>
                        @foreach([10, 20, 25, 50, 70, 75, 80, 100] as $marks)
                            <option value="{{ $marks }}" {{ request('total_marks') == $marks ? 'selected' : '' }}>{{ $marks }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                    <a href="{{ route('admin.blueprints.index') }}" class="btn btn-secondary">Reset</a>
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
                                    <a href="{{ route('admin.subjects.show', $blueprint->subject_id) }}">
                                        {{ $blueprint->subject->subject_name }}
                                    </a>
                                </td>
                                <td>{{ $blueprint->total_marks }}</td>
                                <td>{{ $blueprint->duration }}</td>
                                <td>{{ $blueprint->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.blueprints.show', $blueprint->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.blueprints.edit', $blueprint->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.blueprints.generate', $blueprint->id) }}" class="btn btn-sm btn-success">
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
                                                    <p>Are you sure you want to delete the blueprint <strong>{{ $blueprint->title }}</strong>?</p>
                                                    <div class="alert alert-warning">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                        This action cannot be undone. The blueprint will be permanently removed from the database.
                                                    </div>
                                                    @if($blueprint->questionPapers()->count() > 0)
                                                        <div class="alert alert-danger">
                                                            <i class="fas fa-exclamation-circle me-2"></i>
                                                            This blueprint is used in {{ $blueprint->questionPapers()->count() }} question papers. Deleting it may affect existing question papers.
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('admin.blueprints.destroy', $blueprint->id) }}" method="POST" class="d-inline">
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
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                    <p>No blueprints found.</p>
                                    <a href="{{ route('admin.blueprints.create') }}" class="btn btn-primary">Create Blueprint</a>
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
    
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="card-title mb-0">What is a Blueprint?</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>Definition</h6>
                    <p>A blueprint is a template that defines the structure and composition of a question paper. It specifies the distribution of questions across different topics, difficulty levels, and cognitive domains.</p>
                    
                    <h6 class="mt-3">Benefits</h6>
                    <ul>
                        <li>Ensures consistent question paper structure</li>
                        <li>Balances question difficulty and cognitive levels</li>
                        <li>Provides comprehensive coverage of the syllabus</li>
                        <li>Saves time in question paper creation</li>
                        <li>Maintains quality standards across different exams</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6>How to Use</h6>
                    <ol>
                        <li>Create a blueprint by defining sections and question distributions</li>
                        <li>Specify the number of questions from each topic, difficulty level, and Bloom's taxonomy level</li>
                        <li>Set the total marks and duration for the exam</li>
                        <li>Generate question papers based on the blueprint</li>
                        <li>Review and finalize the generated question papers</li>
                    </ol>
                    
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Tip:</strong> Create multiple blueprints for different types of assessments (e.g., unit tests, mid-terms, finals) to maintain consistency across similar exams.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

