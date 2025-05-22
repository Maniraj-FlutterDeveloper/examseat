@extends('layouts.question-bank')

@section('title', $questionPaper->title)

@section('content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('question-bank.question-papers.index') }}">Question Papers</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $questionPaper->title }}</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ $questionPaper->title }}</h1>
    <div>
        <a href="{{ route('question-bank.question-papers.edit', $questionPaper) }}" class="btn btn-warning me-2">
            <i class="fas fa-edit"></i> Edit
        </a>
        <a href="{{ route('question-bank.question-papers.export', $questionPaper) }}" class="btn btn-primary">
            <i class="fas fa-file-pdf"></i> Export PDF
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow mb-4 fade-in">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Question Paper Details</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="fw-bold">Subject:</label>
                    <p>{{ $questionPaper->subject->name ?? 'Multiple Subjects' }}</p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Description:</label>
                    <p>{{ $questionPaper->description ?? 'No description available.' }}</p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Total Marks:</label>
                    <p>{{ $questionPaper->total_marks }}</p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Time Limit:</label>
                    <p>{{ $questionPaper->time_limit ? $questionPaper->time_limit . ' minutes' : 'Not specified' }}</p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Blueprint:</label>
                    <p>
                        @if($questionPaper->blueprint)
                            <a href="{{ route('question-bank.blueprints.show', $questionPaper->blueprint) }}">
                                {{ $questionPaper->blueprint->title }}
                            </a>
                        @else
                            <span class="badge bg-secondary">Random Generation</span>
                        @endif
                    </p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Status:</label>
                    @if($questionPaper->status === 'draft')
                        <span class="badge bg-secondary">Draft</span>
                    @elseif($questionPaper->status === 'published')
                        <span class="badge bg-success">Published</span>
                    @elseif($questionPaper->status === 'archived')
                        <span class="badge bg-warning">Archived</span>
                    @endif
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Created:</label>
                    <p>{{ $questionPaper->created_at->format('M d, Y h:i A') }}</p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Last Updated:</label>
                    <p>{{ $questionPaper->updated_at->format('M d, Y h:i A') }}</p>
                </div>
            </div>
        </div>
        
        <div class="card shadow mb-4 fade-in">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Statistics</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <h5 class="fw-bold text-primary">{{ $questionPaper->paperQuestions->count() }}</h5>
                            <small class="text-muted">Questions</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <h5 class="fw-bold text-success">{{ $questionPaper->total_marks }}</h5>
                            <small class="text-muted">Total Marks</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <h5 class="fw-bold text-info">{{ $questionPaper->sections_count }}</h5>
                            <small class="text-muted">Sections</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card shadow fade-in">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Question Paper Preview</h6>
            </div>
            <div class="card-body">
                <div class="question-paper-preview">
                    <div class="text-center mb-4">
                        <h4 class="fw-bold">{{ $questionPaper->title }}</h4>
                        @if($questionPaper->subject)
                            <h5>{{ $questionPaper->subject->name }}</h5>
                        @endif
                        <div class="d-flex justify-content-center mt-2">
                            <div class="me-4">
                                <span class="fw-bold">Total Marks:</span> {{ $questionPaper->total_marks }}
                            </div>
                            @if($questionPaper->time_limit)
                                <div>
                                    <span class="fw-bold">Time:</span> {{ $questionPaper->time_limit }} minutes
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    @if($questionPaper->instructions)
                        <div class="instructions-box p-3 bg-light rounded mb-4">
                            <h6 class="fw-bold">Instructions:</h6>
                            {!! nl2br(e($questionPaper->instructions)) !!}
                        </div>
                    @endif
                    
                    @php
                        $currentSection = null;
                        $questionNumber = 1;
                    @endphp
                    
                    @foreach($questionPaper->paperQuestions as $paperQuestion)
                        @if($paperQuestion->section !== $currentSection)
                            @php $currentSection = $paperQuestion->section; @endphp
                            <div class="section-header mt-4 mb-3">
                                <h5 class="fw-bold">{{ $currentSection ?: 'Section' }}</h5>
                            </div>
                        @endif
                        
                        <div class="question-item mb-4 p-3 border rounded">
                            <div class="d-flex justify-content-between">
                                <div class="question-number fw-bold">Q{{ $questionNumber }}.</div>
                                <div class="question-marks">[{{ $paperQuestion->question->marks }} marks]</div>
                            </div>
                            <div class="question-text mt-2">
                                {!! nl2br(e($paperQuestion->question->question_text)) !!}
                            </div>
                            
                            @if($paperQuestion->question->questionType->isMultipleChoice() && $paperQuestion->question->content && isset($paperQuestion->question->content['options']))
                                <div class="question-options mt-3">
                                    <ol type="A" class="mb-0">
                                        @foreach($paperQuestion->question->content['options'] as $option)
                                            <li>{{ $option }}</li>
                                        @endforeach
                                    </ol>
                                </div>
                            @elseif($paperQuestion->question->questionType->isTrueFalse())
                                <div class="question-options mt-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" disabled>
                                        <label class="form-check-label">True</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" disabled>
                                        <label class="form-check-label">False</label>
                                    </div>
                                </div>
                            @elseif($paperQuestion->question->questionType->isMatching() && $paperQuestion->question->content && isset($paperQuestion->question->content['column_a']) && isset($paperQuestion->question->content['column_b']))
                                <div class="question-matching mt-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Column A</h6>
                                            <ol class="mb-0">
                                                @foreach($paperQuestion->question->content['column_a'] as $item)
                                                    <li>{{ $item }}</li>
                                                @endforeach
                                            </ol>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Column B</h6>
                                            <ol class="mb-0">
                                                @foreach($paperQuestion->question->content['column_b'] as $item)
                                                    <li>{{ $item }}</li>
                                                @endforeach
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="question-metadata mt-3 small text-muted">
                                <span class="badge bg-secondary me-2">{{ $paperQuestion->question->questionType->name }}</span>
                                <span class="badge bg-info me-2">{{ $paperQuestion->question->bloomsTaxonomy->name ?? 'N/A' }}</span>
                                <span class="badge bg-warning">
                                    Difficulty: 
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $paperQuestion->question->difficulty_level)
                                            <i class="fas fa-star"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                </span>
                            </div>
                        </div>
                        
                        @php $questionNumber++; @endphp
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

