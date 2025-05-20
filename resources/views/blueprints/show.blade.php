@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Blueprint Details</h1>
        <div>
            <a href="{{ route('question-papers.create', ['blueprint_id' => $blueprint->id]) }}" class="btn btn-success me-2">
                <i class="fas fa-file-alt me-2"></i>Generate Question Paper
            </a>
            <a href="{{ route('blueprints.edit', $blueprint->id) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('blueprints.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Blueprints
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Blueprint Information</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th style="width: 40%">ID:</th>
                            <td>{{ $blueprint->id }}</td>
                        </tr>
                        <tr>
                            <th>Title:</th>
                            <td>{{ $blueprint->title }}</td>
                        </tr>
                        <tr>
                            <th>Subject:</th>
                            <td>
                                <a href="{{ route('subjects.show', $blueprint->subject_id) }}">
                                    {{ $blueprint->subject->subject_name }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Total Marks:</th>
                            <td>{{ $blueprint->total_marks }}</td>
                        </tr>
                        <tr>
                            <th>Duration:</th>
                            <td>{{ $blueprint->duration }} minutes</td>
                        </tr>
                        <tr>
                            <th>Passing Percentage:</th>
                            <td>{{ $blueprint->passing_percentage }}%</td>
                        </tr>
                        <tr>
                            <th>Question Papers:</th>
                            <td>{{ $blueprint->questionPapers->count() }}</td>
                        </tr>
                        <tr>
                            <th>Created At:</th>
                            <td>{{ $blueprint->created_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At:</th>
                            <td>{{ $blueprint->updated_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Description</h5>
                </div>
                <div class="card-body">
                    @if($blueprint->description)
                        <p>{{ $blueprint->description }}</p>
                    @else
                        <p class="text-muted">No description provided.</p>
                    @endif
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Instructions</h5>
                </div>
                <div class="card-body">
                    @if($blueprint->instructions)
                        <p>{{ $blueprint->instructions }}</p>
                    @else
                        <p class="text-muted">No instructions provided.</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Blueprint Sections</h5>
                </div>
                <div class="card-body">
                    @if($blueprint->sections && count($blueprint->sections) > 0)
                        <div class="accordion" id="sectionsAccordion">
                            @foreach($blueprint->sections as $index => $section)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{ $index }}">
                                        <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $index }}">
                                            <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                                <span>Section {{ $index + 1 }}: {{ $section['title'] }}</span>
                                                <span class="badge bg-primary">{{ $section['marks'] }} Marks</span>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" aria-labelledby="heading{{ $index }}" data-bs-parent="#sectionsAccordion">
                                        <div class="accordion-body">
                                            @if(isset($section['instructions']) && $section['instructions'])
                                                <div class="mb-3">
                                                    <h6 class="fw-bold">Instructions:</h6>
                                                    <p>{{ $section['instructions'] }}</p>
                                                </div>
                                            @endif
                                            
                                            <h6 class="fw-bold">Question Distribution:</h6>
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Question Type</th>
                                                            <th>Count</th>
                                                            <th>Marks Each</th>
                                                            <th>Total Marks</th>
                                                            <th>Difficulty</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $sectionTotal = 0;
                                                        @endphp
                                                        @foreach($section['distribution'] as $dist)
                                                            @php
                                                                $totalDistMarks = $dist['count'] * $dist['marks_per_question'];
                                                                $sectionTotal += $totalDistMarks;
                                                            @endphp
                                                            <tr>
                                                                <td>
                                                                    @if($dist['question_type'] == 'mcq')
                                                                        Multiple Choice
                                                                    @elseif($dist['question_type'] == 'true_false')
                                                                        True/False
                                                                    @elseif($dist['question_type'] == 'short_answer')
                                                                        Short Answer
                                                                    @elseif($dist['question_type'] == 'long_answer')
                                                                        Long Answer
                                                                    @elseif($dist['question_type'] == 'fill_in_the_blank')
                                                                        Fill in the Blank
                                                                    @elseif($dist['question_type'] == 'matching')
                                                                        Matching
                                                                    @endif
                                                                </td>
                                                                <td>{{ $dist['count'] }}</td>
                                                                <td>{{ $dist['marks_per_question'] }}</td>
                                                                <td>{{ $totalDistMarks }}</td>
                                                                <td>
                                                                    @if($dist['difficulty_level'] == 'easy')
                                                                        <span class="badge bg-success">Easy</span>
                                                                    @elseif($dist['difficulty_level'] == 'medium')
                                                                        <span class="badge bg-warning">Medium</span>
                                                                    @elseif($dist['difficulty_level'] == 'hard')
                                                                        <span class="badge bg-danger">Hard</span>
                                                                    @else
                                                                        <span class="badge bg-secondary">Mixed</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                        <tr class="table-secondary">
                                                            <td colspan="3" class="text-end fw-bold">Section Total:</td>
                                                            <td class="fw-bold">{{ $sectionTotal }}</td>
                                                            <td></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-exclamation-circle fa-3x text-muted mb-3"></i>
                            <p>No sections defined for this blueprint.</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Question Papers</h5>
                    <a href="{{ route('question-papers.create', ['blueprint_id' => $blueprint->id]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus-circle me-1"></i>Generate New
                    </a>
                </div>
                <div class="card-body">
                    @if($blueprint->questionPapers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Total Marks</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($blueprint->questionPapers as $paper)
                                        <tr>
                                            <td>{{ $paper->id }}</td>
                                            <td>{{ $paper->title }}</td>
                                            <td>{{ $paper->total_marks }}</td>
                                            <td>{{ $paper->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('question-papers.show', $paper->id) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('question-papers.edit', $paper->id) }}" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('question-papers.download', $paper->id) }}" class="btn btn-sm btn-success">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <p>No question papers have been generated from this blueprint yet.</p>
                            <a href="{{ route('question-papers.create', ['blueprint_id' => $blueprint->id]) }}" class="btn btn-primary">Generate Question Paper</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
