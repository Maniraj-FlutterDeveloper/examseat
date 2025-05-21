@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Question Paper Details</h1>
        <div>
            <a href="{{ route('admin.pdf.question_paper', $questionPaper->id) }}" class="btn btn-primary me-2" target="_blank">
                <i class="fas fa-file-pdf me-2"></i>Generate PDF
            </a>
            <a href="{{ route('admin.question_papers.edit', $questionPaper->id) }}" class="btn btn-info me-2">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('admin.question_papers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Question Papers
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Question Paper Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>Title</th>
                            <td>{{ $questionPaper->title }}</td>
                        </tr>
                        <tr>
                            <th>Subject</th>
                            <td>{{ $questionPaper->subject->subject_name }}</td>
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
                            <th>Exam Date</th>
                            <td>{{ $questionPaper->exam_date ? $questionPaper->exam_date->format('d/m/Y') : 'Not specified' }}</td>
                        </tr>
                        <tr>
                            <th>Generation Method</th>
                            <td>{{ ucfirst($questionPaper->generation_method) }}</td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $questionPaper->created_at->format('d/m/Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <td>{{ $questionPaper->updated_at->format('d/m/Y H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Options</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Shuffle Questions
                            <span>
                                @if($questionPaper->shuffle_questions)
                                    <i class="fas fa-check-circle text-success"></i>
                                @else
                                    <i class="fas fa-times-circle text-danger"></i>
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Include Answer Key
                            <span>
                                @if($questionPaper->include_answer_key)
                                    <i class="fas fa-check-circle text-success"></i>
                                @else
                                    <i class="fas fa-times-circle text-danger"></i>
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Include Marking Scheme
                            <span>
                                @if($questionPaper->include_marking_scheme)
                                    <i class="fas fa-check-circle text-success"></i>
                                @else
                                    <i class="fas fa-times-circle text-danger"></i>
                                @endif
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
            
            @if($questionPaper->additional_instructions)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Additional Instructions</h5>
                    </div>
                    <div class="card-body">
                        {!! $questionPaper->additional_instructions !!}
                    </div>
                </div>
            @endif
            
            @if($questionPaper->blueprint)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Blueprint</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Title:</strong> {{ $questionPaper->blueprint->title }}</p>
                        <a href="{{ route('admin.blueprints.show', $questionPaper->blueprint_id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye me-1"></i>View Blueprint
                        </a>
                    </div>
                </div>
            @endif
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Question Paper Preview</h5>
                </div>
                <div class="card-body">
                    <div class="question-paper-preview">
                        <div class="text-center mb-4">
                            <h4>{{ $questionPaper->title }}</h4>
                            <p>
                                <strong>Subject:</strong> {{ $questionPaper->subject->subject_name }} | 
                                <strong>Total Marks:</strong> {{ $questionPaper->total_marks }} | 
                                <strong>Duration:</strong> {{ $questionPaper->duration }} minutes
                                @if($questionPaper->exam_date)
                                    | <strong>Date:</strong> {{ $questionPaper->exam_date->format('d/m/Y') }}
                                @endif
                            </p>
                        </div>
                        
                        @if($questionPaper->additional_instructions)
                            <div class="alert alert-light mb-4">
                                <strong>General Instructions:</strong>
                                <div>{!! $questionPaper->additional_instructions !!}</div>
                            </div>
                        @endif
                        
                        @foreach($questionPaper->sections as $sectionIndex => $section)
                            <div class="section-preview mb-4">
                                <h5 class="border-bottom pb-2">{{ $section->title }} ({{ $section->total_marks }} marks)</h5>
                                
                                @if($section->instructions)
                                    <p class="text-muted fst-italic">{{ $section->instructions }}</p>
                                @endif
                                
                                @foreach($section->questions as $questionIndex => $question)
                                    <div class="question-preview mb-3">
                                        <div class="row">
                                            <div class="col-md-1">
                                                <strong>{{ $questionIndex + 1 }}.</strong>
                                            </div>
                                            <div class="col-md-11">
                                                <div class="question-text mb-2">
                                                    {!! $question->question_text !!}
                                                </div>
                                                
                                                @if($question->question_type == 'mcq' && $question->options)
                                                    <div class="options-list">
                                                        @foreach(json_decode($question->options) as $optionIndex => $option)
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" disabled>
                                                                <label class="form-check-label">
                                                                    {{ chr(97 + $optionIndex) }}) {{ $option }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @elseif($question->question_type == 'true_false')
                                                    <div class="options-list">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" disabled>
                                                            <label class="form-check-label">a) True</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" disabled>
                                                            <label class="form-check-label">b) False</label>
                                                        </div>
                                                    </div>
                                                @endif
                                                
                                                <div class="question-meta mt-2">
                                                    <span class="badge bg-primary">{{ $question->marks }} marks</span>
                                                    <span class="badge bg-secondary">{{ ucfirst($question->question_type) }}</span>
                                                    <span class="badge bg-secondary">{{ ucfirst($question->difficulty_level) }}</span>
                                                    @if($question->blooms_taxonomy)
                                                        <span class="badge bg-secondary">{{ $question->blooms_taxonomy->level_name }}</span>
                                                    @endif
                                                    @if($question->topic)
                                                        <span class="badge bg-secondary">{{ $question->topic->topic_name }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
