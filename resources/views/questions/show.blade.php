@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Question Details</h1>
        <div>
            <a href="{{ route('questions.edit', $question->id) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('questions.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Questions
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Question</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h5 class="fw-bold">{{ $question->question_text }}</h5>
                        <div class="d-flex flex-wrap mt-3">
                            <span class="badge bg-primary me-2 mb-2">{{ ucfirst(str_replace('_', ' ', $question->question_type)) }}</span>
                            
                            @if($question->difficulty_level == 'easy')
                                <span class="badge bg-success me-2 mb-2">Easy</span>
                            @elseif($question->difficulty_level == 'medium')
                                <span class="badge bg-warning me-2 mb-2">Medium</span>
                            @else
                                <span class="badge bg-danger me-2 mb-2">Hard</span>
                            @endif
                            
                            <span class="badge bg-info me-2 mb-2">{{ $question->bloomsTaxonomy->level_name }}</span>
                            <span class="badge bg-secondary me-2 mb-2">{{ $question->marks }} {{ Str::plural('Mark', $question->marks) }}</span>
                        </div>
                    </div>
                    
                    @if($question->question_type == 'mcq')
                        <div class="mb-4">
                            <h6 class="fw-bold">Options:</h6>
                            <div class="list-group">
                                @foreach(json_decode($question->options) as $index => $option)
                                    <div class="list-group-item list-group-item-action {{ $question->correct_answer == $index ? 'list-group-item-success' : '' }}">
                                        @if($question->correct_answer == $index)
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                        @else
                                            <i class="fas fa-circle text-secondary me-2"></i>
                                        @endif
                                        {{ $option }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @elseif($question->question_type == 'true_false')
                        <div class="mb-4">
                            <h6 class="fw-bold">Answer:</h6>
                            <div class="list-group">
                                <div class="list-group-item list-group-item-action list-group-item-success">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    {{ ucfirst($question->correct_answer) }}
                                </div>
                            </div>
                        </div>
                    @elseif($question->question_type == 'short_answer' || $question->question_type == 'long_answer' || $question->question_type == 'fill_in_the_blank')
                        <div class="mb-4">
                            <h6 class="fw-bold">Model Answer:</h6>
                            <div class="card">
                                <div class="card-body bg-light">
                                    {{ $question->correct_answer }}
                                </div>
                            </div>
                        </div>
                        
                        @if($question->question_type == 'long_answer' && $question->marking_scheme)
                            <div class="mb-4">
                                <h6 class="fw-bold">Marking Scheme:</h6>
                                <div class="card">
                                    <div class="card-body bg-light">
                                        {{ $question->marking_scheme }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    @elseif($question->question_type == 'matching')
                        <div class="mb-4">
                            <h6 class="fw-bold">Matching Items:</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50%">Left</th>
                                            <th style="width: 50%">Right</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $matchingItems = json_decode($question->options, true);
                                            $matchingLeft = $matchingItems['left'] ?? [];
                                            $matchingRight = $matchingItems['right'] ?? [];
                                        @endphp
                                        @foreach($matchingLeft as $index => $left)
                                            <tr>
                                                <td>{{ $left }}</td>
                                                <td>{{ $matchingRight[$index] ?? '' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                    
                    @if($question->explanation)
                        <div class="mb-4">
                            <h6 class="fw-bold">Explanation:</h6>
                            <div class="card">
                                <div class="card-body bg-light">
                                    {{ $question->explanation }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Question Information</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th style="width: 40%">ID:</th>
                            <td>{{ $question->id }}</td>
                        </tr>
                        <tr>
                            <th>Subject:</th>
                            <td>
                                <a href="{{ route('subjects.show', $question->topic->unit->subject_id) }}">
                                    {{ $question->topic->unit->subject->subject_name }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Unit:</th>
                            <td>
                                <a href="{{ route('units.show', $question->topic->unit_id) }}">
                                    {{ $question->topic->unit->unit_name }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Topic:</th>
                            <td>
                                <a href="{{ route('topics.show', $question->topic_id) }}">
                                    {{ $question->topic->topic_name }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Question Type:</th>
                            <td>{{ ucfirst(str_replace('_', ' ', $question->question_type)) }}</td>
                        </tr>
                        <tr>
                            <th>Difficulty:</th>
                            <td>
                                @if($question->difficulty_level == 'easy')
                                    <span class="badge bg-success">Easy</span>
                                @elseif($question->difficulty_level == 'medium')
                                    <span class="badge bg-warning">Medium</span>
                                @else
                                    <span class="badge bg-danger">Hard</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Bloom's Level:</th>
                            <td>
                                <a href="{{ route('blooms-taxonomy.show', $question->blooms_taxonomy_id) }}">
                                    {{ $question->bloomsTaxonomy->level_name }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Marks:</th>
                            <td>{{ $question->marks }}</td>
                        </tr>
                        <tr>
                            <th>Created At:</th>
                            <td>{{ $question->created_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At:</th>
                            <td>{{ $question->updated_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Question Papers</h5>
                </div>
                <div class="card-body">
                    @if($question->questionPapers->count() > 0)
                        <div class="list-group">
                            @foreach($question->questionPapers as $paper)
                                <a href="{{ route('question-papers.show', $paper->id) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $paper->title }}</h6>
                                        <small>{{ $paper->created_at->format('M d, Y') }}</small>
                                    </div>
                                    <p class="mb-1">{{ Str::limit($paper->description, 50) }}</p>
                                    <small>Total Marks: {{ $paper->total_marks }}</small>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <p>This question is not used in any question papers yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

