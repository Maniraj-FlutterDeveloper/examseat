@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Topic Details</h1>
        <div>
            <a href="{{ route('admin.topics.edit', $topic->id) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-2"></i>Edit Topic
            </a>
            <a href="{{ route('admin.topics.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Topics
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Topic Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 30%">ID</th>
                            <td>{{ $topic->id }}</td>
                        </tr>
                        <tr>
                            <th>Topic Name</th>
                            <td>{{ $topic->topic_name }}</td>
                        </tr>
                        <tr>
                            <th>Topic Code</th>
                            <td>{{ $topic->topic_code ?: 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Unit</th>
                            <td>
                                <a href="{{ route('admin.units.show', $topic->unit_id) }}">
                                    {{ $topic->unit->unit_name }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Subject</th>
                            <td>
                                <a href="{{ route('admin.subjects.show', $topic->unit->subject_id) }}">
                                    {{ $topic->unit->subject->subject_name }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td>{{ $topic->description ?: 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Order</th>
                            <td>{{ $topic->order }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($topic->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $topic->created_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <td>{{ $topic->updated_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Questions</h5>
                    <a href="{{ route('admin.questions.create', ['topic_id' => $topic->id]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus-circle me-1"></i>Add Question
                    </a>
                </div>
                <div class="card-body">
                    @if($questions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Question Type</th>
                                        <th>Marks</th>
                                        <th>Difficulty</th>
                                        <th>Bloom's Level</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($questions as $question)
                                        <tr>
                                            <td>{{ $question->id }}</td>
                                            <td>{{ $question->question_type }}</td>
                                            <td>{{ $question->marks }}</td>
                                            <td>
                                                @if($question->difficulty_level == 'easy')
                                                    <span class="badge bg-success">Easy</span>
                                                @elseif($question->difficulty_level == 'medium')
                                                    <span class="badge bg-warning text-dark">Medium</span>
                                                @else
                                                    <span class="badge bg-danger">Hard</span>
                                                @endif
                                            </td>
                                            <td>{{ $question->bloomsTaxonomy->level_name ?? 'N/A' }}</td>
                                            <td>
                                                @if($question->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.questions.show', $question->id) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.questions.edit', $question->id) }}" class="btn btn-sm btn-warning">
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
                            <p>No questions found for this topic.</p>
                            <a href="{{ route('admin.questions.create', ['topic_id' => $topic->id]) }}" class="btn btn-primary">Add Question</a>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Question Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Total</h5>
                                    <h2 class="mb-0">{{ $questions->total() }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Easy</h5>
                                    <h2 class="mb-0">{{ $topic->questions()->where('difficulty_level', 'easy')->count() }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-warning text-dark">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Medium</h5>
                                    <h2 class="mb-0">{{ $topic->questions()->where('difficulty_level', 'medium')->count() }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Hard</h5>
                                    <h2 class="mb-0">{{ $topic->questions()->where('difficulty_level', 'hard')->count() }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5>Question Types</h5>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Count</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($questionTypes as $type => $count)
                                            <tr>
                                                <td>{{ $type }}</td>
                                                <td>{{ $count }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

