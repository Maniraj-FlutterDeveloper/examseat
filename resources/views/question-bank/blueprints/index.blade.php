@extends('layouts.question-bank')

@section('title', 'Blueprints')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Blueprints</h1>
    <a href="{{ route('question-bank.blueprints.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Create Blueprint
    </a>
</div>

<div class="card shadow fade-in">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold">All Blueprints</h6>
        <form action="{{ route('question-bank.blueprints.index') }}" method="GET" class="d-flex">
            <input type="text" name="search" class="form-control form-control-sm me-2" placeholder="Search blueprints..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-sm btn-primary">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>
    <div class="card-body">
        @if($blueprints->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Subject</th>
                            <th>Total Marks</th>
                            <th>Conditions</th>
                            <th>Papers</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($blueprints as $blueprint)
                            <tr>
                                <td>
                                    <a href="{{ route('question-bank.blueprints.show', $blueprint) }}" class="fw-bold text-decoration-none">
                                        {{ $blueprint->title }}
                                    </a>
                                </td>
                                <td>{{ $blueprint->subject->name ?? 'Multiple Subjects' }}</td>
                                <td>{{ $blueprint->total_marks }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $blueprint->conditions_count }} conditions</span>
                                </td>
                                <td>{{ $blueprint->papers_count }}</td>
                                <td>{{ $blueprint->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('question-bank.blueprints.show', $blueprint) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('question-bank.blueprints.edit', $blueprint) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('question-bank.question-papers.create', ['blueprint_id' => $blueprint->id]) }}" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="Generate Paper">
                                            <i class="fas fa-file-alt"></i>
                                        </a>
                                        <form action="{{ route('question-bank.blueprints.destroy', $blueprint) }}" method="POST" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-4">
                {{ $blueprints->links() }}
            </div>
        @else
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle me-2"></i> No blueprints found. 
                <a href="{{ route('question-bank.blueprints.create') }}" class="alert-link">Create your first blueprint</a>.
            </div>
        @endif
    </div>
</div>

<div class="card shadow mt-4 fade-in">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold">What is a Blueprint?</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p>A blueprint is a template that defines the structure and content of a question paper. It specifies the types of questions, their difficulty levels, cognitive levels, and marks distribution.</p>
                
                <p>Blueprints help ensure consistency across different question papers and make the paper generation process more efficient and standardized.</p>
                
                <h5 class="mt-4">Benefits of Using Blueprints:</h5>
                <ul>
                    <li>Ensures balanced coverage of topics and units</li>
                    <li>Maintains consistent difficulty levels</li>
                    <li>Distributes questions across different cognitive levels</li>
                    <li>Standardizes the format and structure of question papers</li>
                    <li>Saves time in creating multiple similar question papers</li>
                </ul>
            </div>
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body">
                        <h5 class="card-title">Blueprint Components</h5>
                        <ul>
                            <li><strong>Title:</strong> A descriptive name for the blueprint</li>
                            <li><strong>Subject:</strong> The subject for which the blueprint is designed</li>
                            <li><strong>Total Marks:</strong> The total marks for the question paper</li>
                            <li><strong>Conditions:</strong> Rules that define what questions to include, such as:
                                <ul>
                                    <li>Topic or unit constraints</li>
                                    <li>Question type requirements</li>
                                    <li>Difficulty level distribution</li>
                                    <li>Bloom's Taxonomy level distribution</li>
                                    <li>Marks allocation for different sections</li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Confirm delete
    $('.delete-form').on('submit', function(e) {
        e.preventDefault();
        if (confirm('Are you sure you want to delete this blueprint? This action cannot be undone.')) {
            this.submit();
        }
    });
</script>
@endpush

