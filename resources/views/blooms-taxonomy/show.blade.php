@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Bloom's Taxonomy Level Details</h1>
        <div>
            <a href="{{ route('blooms-taxonomy.edit', $bloomsTaxonomy->id) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('blooms-taxonomy.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Bloom's Taxonomy
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Level Information</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th style="width: 30%">ID:</th>
                            <td>{{ $bloomsTaxonomy->id }}</td>
                        </tr>
                        <tr>
                            <th>Level Name:</th>
                            <td>{{ $bloomsTaxonomy->level_name }}</td>
                        </tr>
                        <tr>
                            <th>Level Order:</th>
                            <td>{{ $bloomsTaxonomy->level_order }}</td>
                        </tr>
                        <tr>
                            <th>Description:</th>
                            <td>{{ $bloomsTaxonomy->description }}</td>
                        </tr>
                        <tr>
                            <th>Example Verbs:</th>
                            <td>
                                @if($bloomsTaxonomy->example_verbs)
                                    @foreach(explode(',', $bloomsTaxonomy->example_verbs) as $verb)
                                        <span class="badge bg-primary me-1">{{ trim($verb) }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">No example verbs provided</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Questions:</th>
                            <td>{{ $bloomsTaxonomy->questions_count }}</td>
                        </tr>
                        <tr>
                            <th>Created At:</th>
                            <td>{{ $bloomsTaxonomy->created_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At:</th>
                            <td>{{ $bloomsTaxonomy->updated_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Questions using this Bloom's Level</h5>
                    <a href="{{ route('questions.create', ['blooms_taxonomy_id' => $bloomsTaxonomy->id]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus-circle me-1"></i>Add Question
                    </a>
                </div>
                <div class="card-body">
                    @if($questions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Question</th>
                                        <th>Type</th>
                                        <th>Marks</th>
                                        <th>Difficulty</th>
                                        <th>Topic</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($questions as $question)
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
                                            <td>
                                                <a href="{{ route('topics.show', $question->topic_id) }}">
                                                    {{ $question->topic->topic_name }}
                                                </a>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('questions.show', $question->id) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('questions.edit', $question->id) }}" class="btn btn-sm btn-warning">
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
                        <div class="text-center py-4">
                            <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                            <p>No questions have been added with this Bloom's Taxonomy level yet.</p>
                            <a href="{{ route('questions.create', ['blooms_taxonomy_id' => $bloomsTaxonomy->id]) }}" class="btn btn-primary">Add Question</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

