@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Question Details</h1>
        <div>
            <a href="{{ route('admin.questions.edit', $question->id) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-2"></i>Edit Question
            </a>
            <a href="{{ route('admin.questions.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Questions
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Question</h5>
                </div>
                <div class="card-body">
                    <div class="question-content mb-4">
                        <div class="question-text mb-3">
                            {!! $question->question_text !!}
                        </div>
                        
                        @if($question->question_type == 'mcq')
                            <div class="options mt-3">
                                <h6>Options:</h6>
                                <div class="row">
                                    @foreach(json_decode($question->options) as $index => $option)
                                        <div class="col-md-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" disabled {{ $index == $question->correct_answer ? 'checked' : '' }}>
                                                <label class="form-check-label {{ $index == $question->correct_answer ? 'text-success fw-bold' : '' }}">
                                                    {{ $option }}
                                                    @if($index == $question->correct_answer)
                                                        <i class="fas fa-check-circle text-success ms-1"></i>
                                                    @endif
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @elseif($question->question_type == 'true_false')
                            <div class="options mt-3">
                                <h6>Answer:</h6>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" disabled {{ $question->correct_answer ? 'checked' : '' }}>
                                    <label class="form-check-label {{ $question->correct_answer ? 'text-success fw-bold' : '' }}">
                                        True
                                        @if($question->correct_answer)
                                            <i class="fas fa-check-circle text-success ms-1"></i>
                                        @endif
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" disabled {{ !$question->correct_answer ? 'checked' : '' }}>
                                    <label class="form-check-label {{ !$question->correct_answer ? 'text-success fw-bold' : '' }}">
                                        False
                                        @if(!$question->correct_answer)
                                            <i class="fas fa-check-circle text-success ms-1"></i>
                                        @endif
                                    </label>
                                </div>
                            </div>
                        @elseif($question->question_type == 'fill_in_the_blank')
                            <div class="answer mt-3">
                                <h6>Correct Answer(s):</h6>
                                <ul class="list-group">
                                    @foreach(json_decode($question->correct_answer) as $answer)
                                        <li class="list-group-item">{{ $answer }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @elseif($question->question_type == 'matching')
                            <div class="matching mt-3">
                                <h6>Matching Pairs:</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-muted">Column A</h6>
                                        <ul class="list-group">
                                            @foreach(json_decode($question->options)->column_a as $item)
                                                <li class="list-group-item">{{ $item }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted">Column B</h6>
                                        <ul class="list-group">
                                            @foreach(json_decode($question->options)->column_b as $item)
                                                <li class="list-group-item">{{ $item }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <h6 class="mt-3">Correct Matches:</h6>
                                <ul class="list-group">
                                    @foreach(json_decode($question->correct_answer) as $key => $value)
                                        <li class="list-group-item">
                                            {{ json_decode($question->options)->column_a[$key] }} → 
                                            {{ json_decode($question->options)->column_b[$value] }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @elseif($question->question_type == 'short_answer' || $question->question_type == 'long_answer')
                            <div class="answer mt-3">
                                <h6>Model Answer:</h6>
                                <div class="card">
                                    <div class="card-body bg-light">
                                        {!! $question->correct_answer !!}
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        @if($question->explanation)
                            <div class="explanation mt-4">
                                <h6>Explanation:</h6>
                                <div class="card">
                                    <div class="card-body bg-light">
                                        {!! $question->explanation !!}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Usage Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Question Papers</h5>
                                    <h2 class="mb-0">{{ $questionPaperCount }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Blueprints</h5>
                                    <h2 class="mb-0">{{ $blueprintCount }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Times Used</h5>
                                    <h2 class="mb-0">{{ $usageCount }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($questionPapers->count() > 0)
                        <div class="mt-4">
                            <h6>Recent Question Papers:</h6>
                            <ul class="list-group">
                                @foreach($questionPapers as $paper)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <a href="{{ route('admin.question-papers.show', $paper->id) }}">
                                            {{ $paper->title }}
                                        </a>
                                        <span class="badge bg-primary rounded-pill">{{ $paper->created_at->format('M d, Y') }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Question Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 40%">ID</th>
                            <td>{{ $question->id }}</td>
                        </tr>
                        <tr>
                            <th>Question Type</th>
                            <td>
                                @if($question->question_type == 'mcq')
                                    <span class="badge bg-primary">Multiple Choice</span>
                                @elseif($question->question_type == 'true_false')
                                    <span class="badge bg-info">True/False</span>
                                @elseif($question->question_type == 'short_answer')
                                    <span class="badge bg-success">Short Answer</span>
                                @elseif($question->question_type == 'long_answer')
                                    <span class="badge bg-warning text-dark">Long Answer</span>
                                @elseif($question->question_type == 'fill_in_the_blank')
                                    <span class="badge bg-secondary">Fill in the Blank</span>
                                @elseif($question->question_type == 'matching')
                                    <span class="badge bg-dark">Matching</span>
                                @else
                                    <span class="badge bg-light text-dark">{{ ucfirst(str_replace('_', ' ', $question->question_type)) }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Subject</th>
                            <td>
                                <a href="{{ route('admin.subjects.show', $question->topic->unit->subject_id) }}">
                                    {{ $question->topic->unit->subject->subject_name }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Unit</th>
                            <td>
                                <a href="{{ route('admin.units.show', $question->topic->unit_id) }}">
                                    {{ $question->topic->unit->unit_name }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Topic</th>
                            <td>
                                <a href="{{ route('admin.topics.show', $question->topic_id) }}">
                                    {{ $question->topic->topic_name }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Marks</th>
                            <td>{{ $question->marks }}</td>
                        </tr>
                        <tr>
                            <th>Difficulty Level</th>
                            <td>
                                @if($question->difficulty_level == 'easy')
                                    <span class="badge bg-success">Easy</span>
                                @elseif($question->difficulty_level == 'medium')
                                    <span class="badge bg-warning text-dark">Medium</span>
                                @else
                                    <span class="badge bg-danger">Hard</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Bloom's Taxonomy</th>
                            <td>
                                @if($question->bloomsTaxonomy)
                                    <a href="{{ route('admin.blooms-taxonomy.show', $question->blooms_taxonomy_id) }}">
                                        {{ $question->bloomsTaxonomy->level_name }}
                                    </a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($question->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $question->created_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <td>{{ $question->updated_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.questions.edit', $question->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Edit Question
                        </a>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash me-2"></i>Delete Question
                        </button>
                        <a href="{{ route('admin.questions.create', ['duplicate' => $question->id]) }}" class="btn btn-info">
                            <i class="fas fa-copy me-2"></i>Duplicate Question
                        </a>
                        <a href="{{ route('admin.questions.create', ['topic_id' => $question->topic_id]) }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-2"></i>Add Question to Same Topic
                        </a>
                    </div>
                    
                    <!-- Delete Modal -->
                    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Are you sure you want to delete this question?</p>
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        This action cannot be undone. The question will be permanently removed from the database.
                                    </div>
                                    @if($usageCount > 0)
                                        <div class="alert alert-danger">
                                            <i class="fas fa-exclamation-circle me-2"></i>
                                            This question is used in {{ $usageCount }} question papers or blueprints. Deleting it may affect existing question papers.
                                        </div>
                                    @endif
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <form action="{{ route('admin.questions.destroy', $question->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

