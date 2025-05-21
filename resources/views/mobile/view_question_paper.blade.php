@extends('layouts.mobile')

@section('title', 'View Question Paper')

@section('custom-css')
.question-paper {
    background-color: white;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
}

.question-paper-header {
    text-align: center;
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid #dee2e6;
}

.question-paper-title {
    font-size: 1.5rem;
    font-weight: bold;
    color: var(--primary-color);
    margin-bottom: 10px;
}

.question-paper-subtitle {
    font-size: 1.2rem;
    margin-bottom: 10px;
}

.question-paper-info {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

.question-paper-info-item {
    text-align: center;
}

.question-paper-info-label {
    font-weight: bold;
    color: var(--primary-color);
}

.question {
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid #dee2e6;
}

.question:last-child {
    border-bottom: none;
}

.question-number {
    font-weight: bold;
    color: var(--primary-color);
    margin-bottom: 5px;
}

.question-text {
    margin-bottom: 10px;
}

.question-marks {
    text-align: right;
    font-weight: bold;
    color: var(--primary-color);
}

.options {
    margin-top: 10px;
}

.option {
    margin-bottom: 5px;
    padding: 5px 10px;
    border-radius: 5px;
    background-color: #f8f9fa;
}

.option-label {
    font-weight: bold;
    margin-right: 5px;
}

.instructions {
    background-color: #f8f9fa;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 20px;
}

.instructions-title {
    font-weight: bold;
    color: var(--primary-color);
    margin-bottom: 10px;
}
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-file-alt me-2"></i> Question Paper</span>
                <a href="{{ route('admin.pdf.question_paper', $questionPaper->id) }}" class="btn btn-sm btn-primary" target="_blank">
                    <i class="fas fa-download me-1"></i> PDF
                </a>
            </div>
            <div class="card-body">
                <div class="question-paper">
                    <div class="question-paper-header">
                        <div class="question-paper-title">{{ $questionPaper->title }}</div>
                        <div class="question-paper-subtitle">{{ $questionPaper->subject->name }}</div>
                        
                        <div class="question-paper-info">
                            <div class="question-paper-info-item">
                                <div class="question-paper-info-label">Duration</div>
                                <div>{{ $questionPaper->duration }} minutes</div>
                            </div>
                            <div class="question-paper-info-item">
                                <div class="question-paper-info-label">Total Marks</div>
                                <div>{{ $questionPaper->total_marks }}</div>
                            </div>
                            <div class="question-paper-info-item">
                                <div class="question-paper-info-label">Questions</div>
                                <div>{{ $questionPaper->questions->count() }}</div>
                            </div>
                        </div>
                    </div>
                    
                    @if($questionPaper->instructions)
                        <div class="instructions">
                            <div class="instructions-title">Instructions:</div>
                            <div>{!! nl2br(e($questionPaper->instructions)) !!}</div>
                        </div>
                    @endif
                    
                    @foreach($questionPaper->questions as $index => $question)
                        <div class="question">
                            <div class="question-number">Q{{ $index + 1 }}.</div>
                            <div class="question-text">{!! nl2br(e($question->text)) !!}</div>
                            
                            @if($question->type === 'mcq')
                                <div class="options">
                                    @foreach(json_decode($question->options) as $key => $option)
                                        <div class="option">
                                            <span class="option-label">{{ chr(65 + $key) }}.</span>
                                            {{ $option }}
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            
                            <div class="question-marks">{{ $question->marks }} marks</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

