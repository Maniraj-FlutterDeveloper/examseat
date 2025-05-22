@extends('layouts.question-bank')

@section('title', $topic->name)

@section('content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('question-bank.subjects.index') }}">Subjects</a></li>
        <li class="breadcrumb-item"><a href="{{ route('question-bank.subjects.show', $subject) }}">{{ $subject->name }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('question-bank.subjects.units.show', [$subject, $unit]) }}">{{ $unit->name }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $topic->name }}</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ $topic->name }}</h1>
    <div>
        <a href="{{ route('question-bank.units.topics.edit', [$unit, $topic]) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit Topic
        </a>
        <a href="{{ route('question-bank.topics.questions.create', $topic) }}" class="btn btn-primary ms-2">
            <i class="fas fa-plus"></i> Add Question
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow mb-4 fade-in">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Topic Details</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="fw-bold">Subject:</label>
                    <p>
                        <a href="{{ route('question-bank.subjects.show', $subject) }}">
                            {{ $subject->name }}
                        </a>
                    </p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Unit:</label>
                    <p>
                        <a href="{{ route('question-bank.subjects.units.show', [$subject, $unit]) }}">
                            {{ $unit->name }}
                        </a>
                    </p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Code:</label>
                    <p>{{ $topic->code ?? 'N/A' }}</p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Description:</label>
                    <p>{{ $topic->description ?? 'No description available.' }}</p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Order:</label>
                    <p>{{ $topic->order }}</p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Status:</label>
                    @if($topic->is_active)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-danger">Inactive</span>
                    @endif
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Created:</label>
                    <p>{{ $topic->created_at->format('M d, Y h:i A') }}</p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Last Updated:</label>
                    <p>{{ $topic->updated_at->format('M d, Y h:i A') }}</p>
                </div>
            </div>
        </div>
        
        <div class="card shadow mb-4 fade-in">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Statistics</h6>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <div class="mb-3">
                        <h5 class="fw-bold text-info">{{ $topic->questions_count }}</h5>
                        <small class="text-muted">Questions</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card shadow fade-in">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">Questions</h6>
                <a href="{{ route('question-bank.topics.questions.create', $topic) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> Add Question
                </a>
            </div>
            <div class="card-body">
                @if($questions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Question</th>
                                    <th>Bloom's Level</th>
                                    <th>Difficulty</th>
                                    <th>Marks</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($questions as $question)
                                    <tr>
                                        <td>{{ $question->questionType->name }}</td>
                                        <td>
                                            <a href="{{ route('question-bank.questions.show', $question) }}" class="fw-bold text-decoration-none">
                                                {{ Str::limit($question->question_text, 50) }}
                                            </a>
                                        </td>
                                        <td>{{ $question->bloomsTaxonomy->name ?? 'N/A' }}</td>
                                        <td>
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $question->difficulty_level)
                                                    <i class="fas fa-star text-warning"></i>
                                                @else
                                                    <i class="far fa-star text-muted"></i>
                                                @endif
                                            @endfor
                                        </td>
                                        <td>{{ $question->marks }}</td>
                                        <td>
                                            @if($question->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('question-bank.questions.show', $question) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('question-bank.questions.edit', $question) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('question-bank.questions.toggle-active', $question) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm {{ $question->is_active ? 'btn-secondary' : 'btn-success' }}" data-bs-toggle="tooltip" title="{{ $question->is_active ? 'Deactivate' : 'Activate' }}">
                                                        <i class="fas {{ $question->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('question-bank.questions.clone', $question) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Clone">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('question-bank.questions.destroy', $question) }}" method="POST" class="d-inline delete-form">
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
                        {{ $questions->links() }}
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i> No questions found for this topic. 
                        <a href="{{ route('question-bank.topics.questions.create', $topic) }}" class="alert-link">Create your first question</a>.
                    </div>
                @endif
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
        if (confirm('Are you sure you want to delete this question? This action cannot be undone.')) {
            this.submit();
        }
    });
</script>
@endpush

