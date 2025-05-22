@extends('layouts.question-bank')

@section('title', $bloomsLevel->name)

@section('content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('question-bank.blooms-taxonomy.index') }}">Bloom's Taxonomy</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $bloomsLevel->name }}</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ $bloomsLevel->name }}</h1>
    <div>
        <a href="{{ route('question-bank.blooms-taxonomy.edit', $bloomsLevel) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit Level
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow mb-4 fade-in">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Level Details</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="fw-bold">Level:</label>
                    <p>{{ $bloomsLevel->level }}</p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Description:</label>
                    <p>{{ $bloomsLevel->description ?? 'No description available.' }}</p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Status:</label>
                    @if($bloomsLevel->is_active)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-danger">Inactive</span>
                    @endif
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Created:</label>
                    <p>{{ $bloomsLevel->created_at->format('M d, Y h:i A') }}</p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Last Updated:</label>
                    <p>{{ $bloomsLevel->updated_at->format('M d, Y h:i A') }}</p>
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
                        <h5 class="fw-bold text-info">{{ $bloomsLevel->questions_count }}</h5>
                        <small class="text-muted">Questions</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card shadow fade-in">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Questions with this Cognitive Level</h6>
            </div>
            <div class="card-body">
                @if($questions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th>Type</th>
                                    <th>Question</th>
                                    <th>Difficulty</th>
                                    <th>Marks</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($questions as $question)
                                    <tr>
                                        <td>{{ $question->topic->unit->subject->name }}</td>
                                        <td>{{ $question->questionType->name }}</td>
                                        <td>
                                            <a href="{{ route('question-bank.questions.show', $question) }}" class="fw-bold text-decoration-none">
                                                {{ Str::limit($question->question_text, 50) }}
                                            </a>
                                        </td>
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
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('question-bank.questions.show', $question) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('question-bank.questions.edit', $question) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
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
                        <i class="fas fa-info-circle me-2"></i> No questions found with this cognitive level.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

