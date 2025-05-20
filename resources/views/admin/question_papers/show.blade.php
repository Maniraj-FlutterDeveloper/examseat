@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Question Paper Details</h1>
        <div>
            <a href="{{ route('admin.question-papers.export-pdf', $questionPaper->id) }}" class="btn btn-primary me-2" target="_blank">
                <i class="fas fa-file-pdf me-2"></i>Export PDF
            </a>
            <a href="{{ route('admin.question-papers.edit', $questionPaper->id) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-2"></i>Edit Paper
            </a>
            <a href="{{ route('admin.question-papers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Question Papers
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Paper Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 40%">ID</th>
                            <td>{{ $questionPaper->id }}</td>
                        </tr>
                        <tr>
                            <th>Title</th>
                            <td>{{ $questionPaper->title }}</td>
                        </tr>
                        <tr>
                            <th>Subject</th>
                            <td>
                                <a href="{{ route('admin.subjects.show', $questionPaper->subject_id) }}">
                                    {{ $questionPaper->subject->subject_name }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Blueprint</th>
                            <td>
                                @if($questionPaper->blueprint_id)
                                    <a href="{{ route('admin.blueprints.show', $questionPaper->blueprint_id) }}">
                                        {{ $questionPaper->blueprint->title }}
                                    </a>
                                @else
                                    <span class="badge bg-secondary">Custom</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Total Marks</th>
                            <td>{{ $questionPaper->total_marks }}</td>
                        </tr>
                        <tr>
                            <th>Duration</th>
                            <td>{{ $questionPaper->duration }} minutes</td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $questionPaper->created_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <td>{{ $questionPaper->updated_at->format('M d, Y H:i:s') }}</td>
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
                        <a href="{{ route('admin.question-papers.export-pdf', $questionPaper->id) }}" class="btn btn-primary" target="_blank">
                            <i class="fas fa-file-pdf me-2"></i>Export PDF
                        </a>
                        <a href="{{ route('admin.question-papers.edit', $questionPaper->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Edit Question Paper
                        </a>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash me-2"></i>Delete Question Paper
                        </button>
                        <a href="{{ route('admin.question-papers.create', ['duplicate' => $questionPaper->id]) }}" class="btn btn-info">
                            <i class="fas fa-copy me-2"></i>Duplicate Question Paper
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
                                    <p>Are you sure you want to delete this question paper?</p>
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        This action cannot be undone. The question paper will be permanently removed from the database.
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <form action="{{ route('admin.question-papers.destroy', $questionPaper->id) }}" method="POST" class="d-inline">
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
        
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Question Paper Preview</h5>
                </div>
                <div class="card-body">
                    <div class="question-paper-preview">
                        <div class="text-center mb-4">
                            <h4>{{ $questionPaper->title }}</h4>
                            <p class="mb-1">Subject: {{ $questionPaper->subject->subject_name }}</p>
                            <p class="mb-1">Total Marks: {{ $questionPaper->total_marks }}</p>
                            <p>Duration: {{ $questionPaper->duration }} minutes</p>
                        </div>
                        
                        @if($questionPaper->instructions)
                            <div class="instructions mb-4">
                                <h5>Instructions:</h5>
                                <div class="card">
                                    <div class="card-body bg-light">
                                        {!! $questionPaper->instructions !!}
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        @php
                            $sections = json_decode($questionPaper->sections);
                            $questionNumber = 1;
                        @endphp
                        
                        @foreach($sections as $sectionIndex => $section)
                            <div class="section mb-4">
                                <h5>Section {{ $sectionIndex + 1 }}: {{ $section->title }}</h5>
                                
                                @if(!empty($section->instructions))
                                    <div class="section-instructions mb-3">
                                        <p><strong>Instructions:</strong> {{ $section->instructions }}</p>
                                    </div>
                                @endif
                                
                                <div class="questions">
                                    @foreach($section->questions as $questionId)
                                        @php
                                            $question = $questions->firstWhere('id', $questionId);
                                        @endphp
                                        
                                        @if($question)
                                            <div class="question mb-4">
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <strong>Q{{ $questionNumber }}.</strong>
                                                        <span class="text-muted">[{{ $question->marks }} marks]</span>
                                                    </div>
                                                    <div>
                                                        <a href="{{ route('admin.questions.show', $question->id) }}" class="btn btn-sm btn-outline-info" target="_blank">
                                                            <i class="fas fa-external-link-alt"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="question-content mt-2">
                                                    {!! $question->question_text !!}
                                                    
                                                    @if($question->question_type == 'mcq')
                                                        <div class="options mt-2">
                                                            @foreach(json_decode($question->options) as $index => $option)
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" disabled>
                                                                    <label class="form-check-label">
                                                                        {{ $option }}
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @elseif($question->question_type == 'true_false')
                                                        <div class="options mt-2">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" disabled>
                                                                <label class="form-check-label">True</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" disabled>
                                                                <label class="form-check-label">False</label>
                                                            </div>
                                                        </div>
                                                    @elseif($question->question_type == 'matching')
                                                        <div class="matching mt-2">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <h6 class="text-muted">Column A</h6>
                                                                    <ul class="list-group">
                                                                        @foreach(json_decode($question->options)->column_a as $index => $item)
                                                                            <li class="list-group-item">{{ chr(65 + $index) }}. {{ $item }}</li>
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <h6 class="text-muted">Column B</h6>
                                                                    <ul class="list-group">
                                                                        @foreach(json_decode($question->options)->column_b as $index => $item)
                                                                            <li class="list-group-item">{{ $index + 1 }}. {{ $item }}</li>
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            @php $questionNumber++; @endphp
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Question Distribution</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h6>By Difficulty Level</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Difficulty</th>
                                            <th>Count</th>
                                            <th>Marks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $difficultyStats = $questions->groupBy('difficulty_level')
                                                ->map(function($group) {
                                                    return [
                                                        'count' => $group->count(),
                                                        'marks' => $group->sum('marks')
                                                    ];
                                                });
                                        @endphp
                                        
                                        <tr>
                                            <td><span class="badge bg-success">Easy</span></td>
                                            <td>{{ $difficultyStats->get('easy')['count'] ?? 0 }}</td>
                                            <td>{{ $difficultyStats->get('easy')['marks'] ?? 0 }}</td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-warning text-dark">Medium</span></td>
                                            <td>{{ $difficultyStats->get('medium')['count'] ?? 0 }}</td>
                                            <td>{{ $difficultyStats->get('medium')['marks'] ?? 0 }}</td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-danger">Hard</span></td>
                                            <td>{{ $difficultyStats->get('hard')['count'] ?? 0 }}</td>
                                            <td>{{ $difficultyStats->get('hard')['marks'] ?? 0 }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h6>By Question Type</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Count</th>
                                            <th>Marks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $typeStats = $questions->groupBy('question_type')
                                                ->map(function($group) {
                                                    return [
                                                        'count' => $group->count(),
                                                        'marks' => $group->sum('marks')
                                                    ];
                                                });
                                        @endphp
                                        
                                        @foreach($typeStats as $type => $stats)
                                            <tr>
                                                <td>
                                                    @if($type == 'mcq')
                                                        <span class="badge bg-primary">Multiple Choice</span>
                                                    @elseif($type == 'true_false')
                                                        <span class="badge bg-info">True/False</span>
                                                    @elseif($type == 'short_answer')
                                                        <span class="badge bg-success">Short Answer</span>
                                                    @elseif($type == 'long_answer')
                                                        <span class="badge bg-warning text-dark">Long Answer</span>
                                                    @elseif($type == 'fill_in_the_blank')
                                                        <span class="badge bg-secondary">Fill in the Blank</span>
                                                    @elseif($type == 'matching')
                                                        <span class="badge bg-dark">Matching</span>
                                                    @else
                                                        <span class="badge bg-light text-dark">{{ ucfirst(str_replace('_', ' ', $type)) }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $stats['count'] }}</td>
                                                <td>{{ $stats['marks'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h6>By Bloom's Taxonomy</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Level</th>
                                            <th>Count</th>
                                            <th>Marks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $bloomsStats = $questions->groupBy('blooms_taxonomy_id')
                                                ->map(function($group) {
                                                    return [
                                                        'count' => $group->count(),
                                                        'marks' => $group->sum('marks')
                                                    ];
                                                });
                                        @endphp
                                        
                                        @foreach($bloomsStats as $bloomId => $stats)
                                            <tr>
                                                <td>
                                                    @if($bloomId)
                                                        {{ $bloomsTaxonomies->firstWhere('id', $bloomId)->level_name ?? 'Unknown' }}
                                                    @else
                                                        <span class="text-muted">Not Specified</span>
                                                    @endif
                                                </td>
                                                <td>{{ $stats['count'] }}</td>
                                                <td>{{ $stats['marks'] }}</td>
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

