@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Topic Details</h1>
        <div>
            <a href="{{ route('topics.edit', $topic->id) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('topics.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Topics
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Topic Information</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th style="width: 30%">ID:</th>
                            <td>{{ $topic->id }}</td>
                        </tr>
                        <tr>
                            <th>Topic Name:</th>
                            <td>{{ $topic->topic_name }}</td>
                        </tr>
                        <tr>
                            <th>Topic Number:</th>
                            <td>{{ $topic->topic_number }}</td>
                        </tr>
                        <tr>
                            <th>Unit:</th>
                            <td>
                                <a href="{{ route('units.show', $topic->unit_id) }}">
                                    {{ $topic->unit->unit_name }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Subject:</th>
                            <td>
                                <a href="{{ route('subjects.show', $topic->unit->subject_id) }}">
                                    {{ $topic->unit->subject->subject_name }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Description:</th>
                            <td>{{ $topic->description ?: 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Questions:</th>
                            <td>{{ $topic->questions->count() }}</td>
                        </tr>
                        <tr>
                            <th>Created At:</th>
                            <td>{{ $topic->created_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At:</th>
                            <td>{{ $topic->updated_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Questions in this Topic</h5>
                    <a href="{{ route('questions.create', ['topic_id' => $topic->id]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus-circle me-1"></i>Add Question
                    </a>
                </div>
                <div class="card-body">
                    @if($topic->questions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Question</th>
                                        <th>Type</th>
                                        <th>Marks</th>
                                        <th>Difficulty</th>
                                        <th>Bloom's Level</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topic->questions as $question)
                                        <tr>
                                            <td>{{ Str::limit($question->question_text, 50) }}</td>
                                            <td>{{ $question->question_type }}</td>
                                            <td>{{ $question->marks }}</td>
                                            <td>
                                                @if($question->difficulty_level == 'easy')
                                                    <span class="badge bg-success">Easy</span>
                                                @elseif($question->difficulty_level == 'medium')
                                                    <span class="badge bg-warning">Medium</span>
                                                @else
                                                    <span class="badge bg-danger">Hard</span>
                                                @endif
                                            </td>
                                            <td>{{ $question->bloomsTaxonomy->level_name }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('questions.show', $question->id) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('questions.edit', $question->id) }}" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteQuestionModal{{ $question->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                                
                                                <!-- Delete Question Modal -->
                                                <div class="modal fade" id="deleteQuestionModal{{ $question->id }}" tabindex="-1" aria-labelledby="deleteQuestionModalLabel{{ $question->id }}" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="deleteQuestionModalLabel{{ $question->id }}">Confirm Delete</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                Are you sure you want to delete this question?
                                                                <div class="alert alert-info mt-3">
                                                                    <strong>Question:</strong> {{ Str::limit($question->question_text, 100) }}
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <form action="{{ route('questions.destroy', $question->id) }}" method="POST" class="d-inline">
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
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                            <p>No questions have been added to this topic yet.</p>
                            <a href="{{ route('questions.create', ['topic_id' => $topic->id]) }}" class="btn btn-primary">Add Question</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

