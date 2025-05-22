@extends('layouts.question-bank')

@section('title', 'Question Types')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Question Types</h1>
    <a href="{{ route('question-bank.question-types.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Question Type
    </a>
</div>

<div class="card shadow fade-in">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold">All Question Types</h6>
    </div>
    <div class="card-body">
        @if($questionTypes->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Structure</th>
                            <th>Questions</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($questionTypes as $type)
                            <tr>
                                <td>
                                    <a href="{{ route('question-bank.question-types.show', $type) }}" class="fw-bold text-decoration-none">
                                        {{ $type->name }}
                                    </a>
                                </td>
                                <td>{{ Str::limit($type->description, 50) }}</td>
                                <td>
                                    @if($type->structure)
                                        <span class="badge bg-info">{{ count($type->structure) }} fields</span>
                                    @else
                                        <span class="badge bg-secondary">No structure</span>
                                    @endif
                                </td>
                                <td>{{ $type->questions_count }}</td>
                                <td>
                                    @if($type->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('question-bank.question-types.show', $type) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('question-bank.question-types.edit', $type) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('question-bank.question-types.toggle-active', $type) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm {{ $type->is_active ? 'btn-secondary' : 'btn-success' }}" data-bs-toggle="tooltip" title="{{ $type->is_active ? 'Deactivate' : 'Activate' }}">
                                                <i class="fas {{ $type->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('question-bank.question-types.destroy', $type) }}" method="POST" class="d-inline delete-form">
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
        @else
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle me-2"></i> No question types found. 
                <a href="{{ route('question-bank.question-types.create') }}" class="alert-link">Create your first question type</a>.
            </div>
        @endif
    </div>
</div>

<div class="card shadow mt-4 fade-in">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold">Common Question Types</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Multiple Choice Question (MCQ)</h5>
                        <p class="card-text">A question with multiple options where only one option is correct.</p>
                        <ul>
                            <li>Question text</li>
                            <li>Multiple options (typically 4-5)</li>
                            <li>Single correct answer</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">True/False</h5>
                        <p class="card-text">A statement that must be marked as either true or false.</p>
                        <ul>
                            <li>Statement text</li>
                            <li>Two options: True or False</li>
                            <li>Single correct answer</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Short Answer</h5>
                        <p class="card-text">A question that requires a brief response, typically a few words or a sentence.</p>
                        <ul>
                            <li>Question text</li>
                            <li>Expected answer (for evaluation)</li>
                            <li>Word limit (optional)</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Long Answer</h5>
                        <p class="card-text">A question that requires an extended response, typically a paragraph or more.</p>
                        <ul>
                            <li>Question text</li>
                            <li>Evaluation criteria</li>
                            <li>Word limit (optional)</li>
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
        if (confirm('Are you sure you want to delete this question type? This action cannot be undone.')) {
            this.submit();
        }
    });
</script>
@endpush

