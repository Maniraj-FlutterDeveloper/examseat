@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Blueprint Details</h1>
        <div>
            <a href="{{ route('admin.blueprints.generate', $blueprint->id) }}" class="btn btn-success me-2">
                <i class="fas fa-file-alt me-2"></i>Generate Question Paper
            </a>
            <a href="{{ route('admin.blueprints.edit', $blueprint->id) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-2"></i>Edit Blueprint
            </a>
            <a href="{{ route('admin.blueprints.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Blueprints
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Blueprint Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 40%">ID</th>
                            <td>{{ $blueprint->id }}</td>
                        </tr>
                        <tr>
                            <th>Title</th>
                            <td>{{ $blueprint->title }}</td>
                        </tr>
                        <tr>
                            <th>Subject</th>
                            <td>
                                <a href="{{ route('admin.subjects.show', $blueprint->subject_id) }}">
                                    {{ $blueprint->subject->subject_name }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Total Marks</th>
                            <td>{{ $blueprint->total_marks }}</td>
                        </tr>
                        <tr>
                            <th>Duration</th>
                            <td>{{ $blueprint->duration }} minutes</td>
                        </tr>
                        <tr>
                            <th>Instructions</th>
                            <td>{{ $blueprint->instructions ?: 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $blueprint->created_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <td>{{ $blueprint->updated_at->format('M d, Y H:i:s') }}</td>
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
                        <a href="{{ route('admin.blueprints.generate', $blueprint->id) }}" class="btn btn-success">
                            <i class="fas fa-file-alt me-2"></i>Generate Question Paper
                        </a>
                        <a href="{{ route('admin.blueprints.edit', $blueprint->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Edit Blueprint
                        </a>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash me-2"></i>Delete Blueprint
                        </button>
                        <a href="{{ route('admin.blueprints.create', ['duplicate' => $blueprint->id]) }}" class="btn btn-info">
                            <i class="fas fa-copy me-2"></i>Duplicate Blueprint
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
                                    <p>Are you sure you want to delete this blueprint?</p>
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        This action cannot be undone. The blueprint will be permanently removed from the database.
                                    </div>
                                    @if($questionPaperCount > 0)
                                        <div class="alert alert-danger">
                                            <i class="fas fa-exclamation-circle me-2"></i>
                                            This blueprint is used in {{ $questionPaperCount }} question papers. Deleting it may affect existing question papers.
                                        </div>
                                    @endif
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <form action="{{ route('admin.blueprints.destroy', $blueprint->id) }}" method="POST" class="d-inline">
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
                    <h5 class="card-title mb-0">Blueprint Structure</h5>
                </div>
                <div class="card-body">
                    @if(count($sections) > 0)
                        <div class="accordion" id="blueprintAccordion">
                            @foreach($sections as $sectionIndex => $section)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{ $sectionIndex }}">
                                        <button class="accordion-button {{ $sectionIndex > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $sectionIndex }}" aria-expanded="{{ $sectionIndex === 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $sectionIndex }}">
                                            <strong>Section {{ $sectionIndex + 1 }}: {{ $section->title }}</strong>
                                            <span class="ms-auto badge bg-primary">{{ $section->total_marks }} Marks</span>
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $sectionIndex }}" class="accordion-collapse collapse {{ $sectionIndex === 0 ? 'show' : '' }}" aria-labelledby="heading{{ $sectionIndex }}" data-bs-parent="#blueprintAccordion">
                                        <div class="accordion-body">
                                            <div class="mb-3">
                                                <strong>Instructions:</strong> {{ $section->instructions ?: 'N/A' }}
                                            </div>
                                            
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Unit/Topic</th>
                                                            <th>Question Type</th>
                                                            <th>Difficulty</th>
                                                            <th>Bloom's Level</th>
                                                            <th>Marks per Question</th>
                                                            <th>Number of Questions</th>
                                                            <th>Total Marks</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($section->rules as $rule)
                                                            <tr>
                                                                <td>
                                                                    @if($rule->unit_id && $rule->topic_id)
                                                                        {{ $rule->unit->unit_name }} / {{ $rule->topic->topic_name }}
                                                                    @elseif($rule->unit_id)
                                                                        {{ $rule->unit->unit_name }} / Any Topic
                                                                    @else
                                                                        Any Unit / Any Topic
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($rule->question_type)
                                                                        @if($rule->question_type == 'mcq')
                                                                            <span class="badge bg-primary">Multiple Choice</span>
                                                                        @elseif($rule->question_type == 'true_false')
                                                                            <span class="badge bg-info">True/False</span>
                                                                        @elseif($rule->question_type == 'short_answer')
                                                                            <span class="badge bg-success">Short Answer</span>
                                                                        @elseif($rule->question_type == 'long_answer')
                                                                            <span class="badge bg-warning text-dark">Long Answer</span>
                                                                        @elseif($rule->question_type == 'fill_in_the_blank')
                                                                            <span class="badge bg-secondary">Fill in the Blank</span>
                                                                        @elseif($rule->question_type == 'matching')
                                                                            <span class="badge bg-dark">Matching</span>
                                                                        @else
                                                                            <span class="badge bg-light text-dark">{{ ucfirst(str_replace('_', ' ', $rule->question_type)) }}</span>
                                                                        @endif
                                                                    @else
                                                                        <span class="text-muted">Any</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($rule->difficulty_level)
                                                                        @if($rule->difficulty_level == 'easy')
                                                                            <span class="badge bg-success">Easy</span>
                                                                        @elseif($rule->difficulty_level == 'medium')
                                                                            <span class="badge bg-warning text-dark">Medium</span>
                                                                        @else
                                                                            <span class="badge bg-danger">Hard</span>
                                                                        @endif
                                                                    @else
                                                                        <span class="text-muted">Any</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($rule->blooms_taxonomy_id)
                                                                        {{ $rule->bloomsTaxonomy->level_name }}
                                                                    @else
                                                                        <span class="text-muted">Any</span>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $rule->marks_per_question }}</td>
                                                                <td>{{ $rule->num_questions }}</td>
                                                                <td>{{ $rule->marks_per_question * $rule->num_questions }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                        <tr class="table-primary">
                                                            <th colspan="5">Section Total</th>
                                                            <th>{{ $section->rules->sum('num_questions') }}</th>
                                                            <th>{{ $section->total_marks }}</th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="card mt-4">
                            <div class="card-body bg-light">
                                <div class="row">
                                    <div class="col-md-4">
                                        <h6>Total Questions: {{ $totalQuestions }}</h6>
                                    </div>
                                    <div class="col-md-4">
                                        <h6>Total Marks: {{ $blueprint->total_marks }}</h6>
                                    </div>
                                    <div class="col-md-4">
                                        <h6>Duration: {{ $blueprint->duration }} minutes</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            This blueprint doesn't have any sections defined yet. Edit the blueprint to add sections and rules.
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Generated Question Papers</h5>
                    <a href="{{ route('admin.blueprints.generate', $blueprint->id) }}" class="btn btn-sm btn-success">
                        <i class="fas fa-file-alt me-1"></i>Generate New
                    </a>
                </div>
                <div class="card-body">
                    @if($questionPapers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($questionPapers as $paper)
                                        <tr>
                                            <td>{{ $paper->id }}</td>
                                            <td>{{ $paper->title }}</td>
                                            <td>{{ $paper->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.question-papers.show', $paper->id) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.question-papers.export-pdf', $paper->id) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-file-pdf"></i>
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
                            <a href="{{ route('admin.blueprints.generate', $blueprint->id) }}" class="btn btn-success">Generate Question Paper</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

