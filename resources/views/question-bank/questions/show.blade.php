@extends('layouts.question-bank')

@section('title', 'Question Details')

@section('content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('question-bank.questions.index') }}">Questions</a></li>
        <li class="breadcrumb-item active" aria-current="page">View Question</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Question Details</h1>
    <div>
        <a href="{{ route('question-bank.questions.edit', $question) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit Question
        </a>
        <form action="{{ route('question-bank.questions.clone', $question) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-primary ms-2">
                <i class="fas fa-copy"></i> Clone Question
            </button>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow mb-4 fade-in">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Question Metadata</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="fw-bold">Subject:</label>
                    <p>
                        <a href="{{ route('question-bank.subjects.show', $question->topic->unit->subject) }}">
                            {{ $question->topic->unit->subject->name }}
                        </a>
                    </p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Unit:</label>
                    <p>
                        <a href="{{ route('question-bank.subjects.units.show', [$question->topic->unit->subject, $question->topic->unit]) }}">
                            {{ $question->topic->unit->name }}
                        </a>
                    </p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Topic:</label>
                    <p>
                        <a href="{{ route('question-bank.units.topics.show', [$question->topic->unit, $question->topic]) }}">
                            {{ $question->topic->name }}
                        </a>
                    </p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Question Type:</label>
                    <p>{{ $question->questionType->name }}</p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Bloom's Taxonomy Level:</label>
                    <p>{{ $question->bloomsTaxonomy->name ?? 'N/A' }}</p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Difficulty Level:</label>
                    <p>
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $question->difficulty_level)
                                <i class="fas fa-star text-warning"></i>
                            @else
                                <i class="far fa-star text-muted"></i>
                            @endif
                        @endfor
                        ({{ ['Very Easy', 'Easy', 'Medium', 'Hard', 'Very Hard'][$question->difficulty_level - 1] }})
                    </p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Marks:</label>
                    <p>{{ $question->marks }}</p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Status:</label>
                    @if($question->is_active)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-danger">Inactive</span>
                    @endif
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Created:</label>
                    <p>{{ $question->created_at->format('M d, Y h:i A') }}</p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Last Updated:</label>
                    <p>{{ $question->updated_at->format('M d, Y h:i A') }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card shadow mb-4 fade-in">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Question Content</h6>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h5 class="fw-bold">Question:</h5>
                    <div class="p-3 bg-light rounded">
                        {!! nl2br(e($question->question_text)) !!}
                    </div>
                </div>
                
                @if($question->content)
                    <div class="mb-4">
                        <h5 class="fw-bold">Details:</h5>
                        <div class="p-3 bg-light rounded">
                            @if($question->questionType->isMultipleChoice())
                                <h6>Options:</h6>
                                <ol type="A" class="mb-0">
                                    @foreach($question->content['options'] as $index => $option)
                                        <li class="{{ $index == $question->content['correct_option'] ? 'fw-bold text-success' : '' }}">
                                            {{ $option }}
                                            @if($index == $question->content['correct_option'])
                                                <i class="fas fa-check-circle text-success ms-2"></i> (Correct Answer)
                                            @endif
                                        </li>
                                    @endforeach
                                </ol>
                            @elseif($question->questionType->isTrueFalse())
                                <h6>Correct Answer:</h6>
                                <p class="mb-0 fw-bold text-success">
                                    {{ $question->content['correct_answer'] ? 'True' : 'False' }}
                                </p>
                            @elseif($question->questionType->isShortAnswer())
                                <h6>Expected Answer:</h6>
                                <p class="mb-0">{{ $question->content['expected_answer'] ?? 'N/A' }}</p>
                                
                                @if(isset($question->content['word_limit']))
                                    <h6 class="mt-3">Word Limit:</h6>
                                    <p class="mb-0">{{ $question->content['word_limit'] }} words</p>
                                @endif
                            @elseif($question->questionType->isLongAnswer())
                                <h6>Evaluation Criteria:</h6>
                                <p class="mb-0">{{ $question->content['evaluation_criteria'] ?? 'N/A' }}</p>
                                
                                @if(isset($question->content['word_limit']))
                                    <h6 class="mt-3">Word Limit:</h6>
                                    <p class="mb-0">{{ $question->content['word_limit'] }} words</p>
                                @endif
                            @elseif($question->questionType->isMatching())
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Column A:</h6>
                                        <ol class="mb-0">
                                            @foreach($question->content['column_a'] as $item)
                                                <li>{{ $item }}</li>
                                            @endforeach
                                        </ol>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Column B:</h6>
                                        <ol class="mb-0">
                                            @foreach($question->content['column_b'] as $item)
                                                <li>{{ $item }}</li>
                                            @endforeach
                                        </ol>
                                    </div>
                                </div>
                                
                                <h6 class="mt-3">Correct Matches:</h6>
                                <ul class="mb-0">
                                    @foreach($question->content['matches'] as $match)
                                        <li>{{ $match['a'] }} → {{ $match['b'] }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <pre>{{ json_encode($question->content, JSON_PRETTY_PRINT) }}</pre>
                            @endif
                        </div>
                    </div>
                @endif
                
                @if($question->solution)
                    <div class="mb-4">
                        <h5 class="fw-bold">Solution/Explanation:</h5>
                        <div class="p-3 bg-light rounded">
                            {!! nl2br(e($question->solution)) !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="card shadow fade-in">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Question Papers</h6>
            </div>
            <div class="card-body">
                @if($questionPapers->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Paper Title</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($questionPapers as $paper)
                                    <tr>
                                        <td>
                                            <a href="{{ route('question-bank.question-papers.show', $paper->question_paper_id) }}">
                                                {{ $paper->questionPaper->title }}
                                            </a>
                                        </td>
                                        <td>
                                            @if($paper->questionPaper->status === 'draft')
                                                <span class="badge bg-secondary">Draft</span>
                                            @elseif($paper->questionPaper->status === 'published')
                                                <span class="badge bg-success">Published</span>
                                            @elseif($paper->questionPaper->status === 'archived')
                                                <span class="badge bg-warning">Archived</span>
                                            @endif
                                        </td>
                                        <td>{{ $paper->questionPaper->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <a href="{{ route('question-bank.question-papers.show', $paper->question_paper_id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i> This question has not been used in any question papers yet.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

