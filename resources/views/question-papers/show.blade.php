@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Question Paper Details</h1>
        <div>
            <a href="{{ route('question-papers.download', $questionPaper->id) }}" class="btn btn-success me-2">
                <i class="fas fa-download me-2"></i>Download PDF
            </a>
            <a href="{{ route('question-papers.edit', $questionPaper->id) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('question-papers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Question Papers
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Question Paper Information</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th style="width: 40%">ID:</th>
                            <td>{{ $questionPaper->id }}</td>
                        </tr>
                        <tr>
                            <th>Title:</th>
                            <td>{{ $questionPaper->title }}</td>
                        </tr>
                        <tr>
                            <th>Subject:</th>
                            <td>
                                <a href="{{ route('subjects.show', $questionPaper->subject_id) }}">
                                    {{ $questionPaper->subject->subject_name }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Blueprint:</th>
                            <td>
                                @if($questionPaper->blueprint_id)
                                    <a href="{{ route('blueprints.show', $questionPaper->blueprint_id) }}">
                                        {{ $questionPaper->blueprint->title }}
                                    </a>
                                @else
                                    <span class="badge bg-secondary">Custom</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Total Marks:</th>
                            <td>{{ $questionPaper->total_marks }}</td>
                        </tr>
                        <tr>
                            <th>Duration:</th>
                            <td>{{ $questionPaper->duration }} minutes</td>
                        </tr>
                        <tr>
                            <th>Passing Percentage:</th>
                            <td>{{ $questionPaper->passing_percentage }}%</td>
                        </tr>
                        <tr>
                            <th>Questions:</th>
                            <td>{{ $questionPaper->questions->count() }}</td>
                        </tr>
                        <tr>
                            <th>Created At:</th>
                            <td>{{ $questionPaper->created_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At:</th>
                            <td>{{ $questionPaper->updated_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Instructions</h5>
                </div>
                <div class="card-body">
                    @if($questionPaper->instructions)
                        <p>{{ $questionPaper->instructions }}</p>
                    @else
                        <p class="text-muted">No instructions provided.</p>
                    @endif
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Question Distribution</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Question Type</th>
                                    <th>Count</th>
                                    <th>Total Marks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $questionTypes = $questionPaper->questions->groupBy('question_type');
                                    $totalMarks = 0;
                                @endphp
                                
                                @foreach($questionTypes as $type => $questions)
                                    @php
                                        $typeMarks = $questions->sum('marks');
                                        $totalMarks += $typeMarks;
                                    @endphp
                                    <tr>
                                        <td>
                                            @if($type == 'mcq')
                                                Multiple Choice
                                            @elseif($type == 'true_false')
                                                True/False
                                            @elseif($type == 'short_answer')
                                                Short Answer
                                            @elseif($type == 'long_answer')
                                                Long Answer
                                            @elseif($type == 'fill_in_the_blank')
                                                Fill in the Blank
                                            @elseif($type == 'matching')
                                                Matching
                                            @endif
                                        </td>
                                        <td>{{ $questions->count() }}</td>
                                        <td>{{ $typeMarks }}</td>
                                    </tr>
                                @endforeach
                                
                                <tr class="table-secondary">
                                    <td colspan="2" class="text-end fw-bold">Total:</td>
                                    <td class="fw-bold">{{ $totalMarks }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Questions</h5>
                </div>
                <div class="card-body">
                    @if($questionPaper->questions->count() > 0)
                        <div class="accordion" id="questionsAccordion">
                            @php
                                $questionNumber = 1;
                                $sections = $questionPaper->questions->groupBy('section');
                            @endphp
                            
                            @foreach($sections as $sectionName => $sectionQuestions)
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">{{ $sectionName ?: 'Section ' . $loop->iteration }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <ol class="list-group list-group-numbered">
                                            @foreach($sectionQuestions as $question)
                                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                                    <div class="ms-2 me-auto">
                                                        <div class="fw-bold">{{ $question->question_text }}</div>
                                                        
                                                        @if($question->question_type == 'mcq')
                                                            <div class="mt-2">
                                                                @foreach(json_decode($question->options) as $option)
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="radio" disabled>
                                                                        <label class="form-check-label">
                                                                            {{ $option }}
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @elseif($question->question_type == 'true_false')
                                                            <div class="mt-2">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" disabled>
                                                                    <label class="form-check-label">True</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" disabled>
                                                                    <label class="form-check-label">False</label>
                                                                </div>
                                                            </div>
                                                        @elseif($question->question_type == 'fill_in_the_blank')
                                                            <div class="mt-2">
                                                                <p>Fill in the blank: _________________</p>
                                                            </div>
                                                        @elseif($question->question_type == 'matching')
                                                            <div class="mt-2">
                                                                <div class="row">
                                                                    <div class="col-md-5">
                                                                        <ul class="list-group">
                                                                            @foreach(json_decode($question->options)->column_a as $item)
                                                                                <li class="list-group-item">{{ $item }}</li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-md-2 text-center">
                                                                        <i class="fas fa-arrows-alt-h fa-2x text-muted"></i>
                                                                    </div>
                                                                    <div class="col-md-5">
                                                                        <ul class="list-group">
                                                                            @foreach(json_decode($question->options)->column_b as $item)
                                                                                <li class="list-group-item">{{ $item }}</li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @elseif($question->question_type == 'short_answer')
                                                            <div class="mt-2">
                                                                <p class="text-muted">Short answer question</p>
                                                            </div>
                                                        @elseif($question->question_type == 'long_answer')
                                                            <div class="mt-2">
                                                                <p class="text-muted">Long answer question</p>
                                                            </div>
                                                        @endif
                                                        
                                                        <div class="mt-2">
                                                            <span class="badge bg-secondary">
                                                                {{ ucfirst($question->question_type) }}
                                                            </span>
                                                            
                                                            @if($question->difficulty_level == 'easy')
                                                                <span class="badge bg-success">Easy</span>
                                                            @elseif($question->difficulty_level == 'medium')
                                                                <span class="badge bg-warning">Medium</span>
                                                            @elseif($question->difficulty_level == 'hard')
                                                                <span class="badge bg-danger">Hard</span>
                                                            @endif
                                                            
                                                            @if($question->bloom_level)
                                                                <span class="badge bg-info">
                                                                    {{ $question->bloomsTaxonomy->level_name }}
                                                                </span>
                                                            @endif
                                                            
                                                            <span class="badge bg-primary">
                                                                {{ $question->topic->topic_name }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <span class="badge bg-primary rounded-pill">{{ $question->marks }} marks</span>
                                                </li>
                                                @php $questionNumber++; @endphp
                                            @endforeach
                                        </ol>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-exclamation-circle fa-3x text-muted mb-3"></i>
                            <p>No questions found in this question paper.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
