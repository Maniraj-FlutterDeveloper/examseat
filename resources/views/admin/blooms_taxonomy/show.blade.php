@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Bloom's Taxonomy Level Details</h1>
        <div>
            <a href="{{ route('admin.blooms-taxonomy.edit', $bloomsTaxonomy->id) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-2"></i>Edit Level
            </a>
            <a href="{{ route('admin.blooms-taxonomy.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Bloom's Taxonomy
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Level Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 30%">ID</th>
                            <td>{{ $bloomsTaxonomy->id }}</td>
                        </tr>
                        <tr>
                            <th>Level Name</th>
                            <td>{{ $bloomsTaxonomy->level_name }}</td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td>{{ $bloomsTaxonomy->description }}</td>
                        </tr>
                        <tr>
                            <th>Order</th>
                            <td>{{ $bloomsTaxonomy->order }}</td>
                        </tr>
                        <tr>
                            <th>Example Verbs</th>
                            <td>
                                @if($bloomsTaxonomy->example_verbs)
                                    @foreach(explode(',', $bloomsTaxonomy->example_verbs) as $verb)
                                        <span class="badge bg-info me-1">{{ trim($verb) }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">No example verbs provided</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $bloomsTaxonomy->created_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <td>{{ $bloomsTaxonomy->updated_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Level Usage</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <i class="fas fa-question-circle fa-2x text-primary me-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0">Total Questions</h6>
                            <h4 class="mb-0">{{ $questionCount }}</h4>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <i class="fas fa-book fa-2x text-success me-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0">Subjects</h6>
                            <h4 class="mb-0">{{ $subjectCount }}</h4>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-file-alt fa-2x text-warning me-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0">Question Papers</h6>
                            <h4 class="mb-0">{{ $questionPaperCount }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Questions Using This Level</h5>
                    <a href="{{ route('admin.questions.create', ['blooms_taxonomy_id' => $bloomsTaxonomy->id]) }}" class="btn btn-sm btn-primary">
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
                                        <th>Subject</th>
                                        <th>Unit</th>
                                        <th>Topic</th>
                                        <th>Marks</th>
                                        <th>Difficulty</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($questions as $question)
                                        <tr>
                                            <td>{{ $question->id }}</td>
                                            <td>{{ $question->question_type }}</td>
                                            <td>{{ $question->topic->unit->subject->subject_name }}</td>
                                            <td>{{ $question->topic->unit->unit_name }}</td>
                                            <td>{{ $question->topic->topic_name }}</td>
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
                            <p>No questions found using this Bloom's Taxonomy level.</p>
                            <a href="{{ route('admin.questions.create', ['blooms_taxonomy_id' => $bloomsTaxonomy->id]) }}" class="btn btn-primary">Add Question</a>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Question Distribution</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>By Difficulty Level</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Difficulty</th>
                                            <th>Count</th>
                                            <th>Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $totalQuestions = $questionCount > 0 ? $questionCount : 1;
                                            $easyCount = $questions->where('difficulty_level', 'easy')->count();
                                            $mediumCount = $questions->where('difficulty_level', 'medium')->count();
                                            $hardCount = $questions->where('difficulty_level', 'hard')->count();
                                            
                                            $easyPercentage = round(($easyCount / $totalQuestions) * 100);
                                            $mediumPercentage = round(($mediumCount / $totalQuestions) * 100);
                                            $hardPercentage = round(($hardCount / $totalQuestions) * 100);
                                        @endphp
                                        <tr>
                                            <td><span class="badge bg-success">Easy</span></td>
                                            <td>{{ $easyCount }}</td>
                                            <td>{{ $easyPercentage }}%</td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-warning text-dark">Medium</span></td>
                                            <td>{{ $mediumCount }}</td>
                                            <td>{{ $mediumPercentage }}%</td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-danger">Hard</span></td>
                                            <td>{{ $hardCount }}</td>
                                            <td>{{ $hardPercentage }}%</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>By Question Type</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Count</th>
                                            <th>Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($questionTypes as $type => $count)
                                            <tr>
                                                <td>{{ $type }}</td>
                                                <td>{{ $count }}</td>
                                                <td>{{ round(($count / $totalQuestions) * 100) }}%</td>
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

