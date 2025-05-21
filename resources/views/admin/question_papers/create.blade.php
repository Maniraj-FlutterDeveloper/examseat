@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Create Question Paper</h1>
        <a href="{{ route('admin.question_papers.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Question Papers
        </a>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="paperTypeTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="blueprint-tab" data-bs-toggle="tab" data-bs-target="#blueprint-content" type="button" role="tab" aria-controls="blueprint-content" aria-selected="true">
                                <i class="fas fa-drafting-compass me-2"></i>From Blueprint
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="random-tab" data-bs-toggle="tab" data-bs-target="#random-content" type="button" role="tab" aria-controls="random-content" aria-selected="false">
                                <i class="fas fa-random me-2"></i>Random Generation
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="paperTypeTabsContent">
                        <!-- Blueprint-based Question Paper -->
                        <div class="tab-pane fade show active" id="blueprint-content" role="tabpanel" aria-labelledby="blueprint-tab">
                            <form action="{{ route('admin.question_papers.store') }}" method="POST" id="blueprintForm">
                                @csrf
                                <input type="hidden" name="generation_method" value="blueprint">
                                
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="title" class="form-label">Question Paper Title <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                                            @error('title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="blueprint_id" class="form-label">Blueprint <span class="text-danger">*</span></label>
                                            <select class="form-select @error('blueprint_id') is-invalid @enderror" id="blueprint_id" name="blueprint_id" required>
                                                <option value="">Select Blueprint</option>
                                                @foreach($blueprints as $blueprint)
                                                    <option value="{{ $blueprint->id }}" {{ old('blueprint_id') == $blueprint->id ? 'selected' : '' }}>
                                                        {{ $blueprint->title }} ({{ $blueprint->subject->subject_name }}, {{ $blueprint->total_marks }} marks)
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('blueprint_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="exam_date" class="form-label">Exam Date</label>
                                            <input type="date" class="form-control @error('exam_date') is-invalid @enderror" id="exam_date" name="exam_date" value="{{ old('exam_date') }}">
                                            @error('exam_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="additional_instructions" class="form-label">Additional Instructions</label>
                                            <textarea class="form-control @error('additional_instructions') is-invalid @enderror" id="additional_instructions" name="additional_instructions" rows="3">{{ old('additional_instructions') }}</textarea>
                                            @error('additional_instructions')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">Blueprint Details</h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="blueprintDetails">
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Select a blueprint to view its details.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">Generation Options</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="shuffle_questions" name="shuffle_questions" value="1" {{ old('shuffle_questions') ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="shuffle_questions">
                                                            Shuffle Questions Within Sections
                                                        </label>
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="include_answer_key" name="include_answer_key" value="1" {{ old('include_answer_key') ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="include_answer_key">
                                                            Include Answer Key
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="include_marking_scheme" name="include_marking_scheme" value="1" {{ old('include_marking_scheme') ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="include_marking_scheme">
                                                            Include Marking Scheme
                                                        </label>
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="preview_before_save" name="preview_before_save" value="1" {{ old('preview_before_save', '1') ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="preview_before_save">
                                                            Preview Before Saving
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-file-alt me-2"></i>Generate Question Paper
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Random Question Paper -->
                        <div class="tab-pane fade" id="random-content" role="tabpanel" aria-labelledby="random-tab">
                            <form action="{{ route('admin.question_papers.store') }}" method="POST" id="randomForm">
                                @csrf
                                <input type="hidden" name="generation_method" value="random">
                                
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="random_title" class="form-label">Question Paper Title <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="random_title" name="title" value="{{ old('title') }}" required>
                                            @error('title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="subject_id" class="form-label">Subject <span class="text-danger">*</span></label>
                                            <select class="form-select @error('subject_id') is-invalid @enderror" id="subject_id" name="subject_id" required>
                                                <option value="">Select Subject</option>
                                                @foreach($subjects as $subject)
                                                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                                        {{ $subject->subject_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('subject_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="total_marks" class="form-label">Total Marks <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control @error('total_marks') is-invalid @enderror" id="total_marks" name="total_marks" value="{{ old('total_marks', 100) }}" min="1" required>
                                            @error('total_marks')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="random_exam_date" class="form-label">Exam Date</label>
                                            <input type="date" class="form-control @error('exam_date') is-invalid @enderror" id="random_exam_date" name="exam_date" value="{{ old('exam_date') }}">
                                            @error('exam_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="duration" class="form-label">Duration (minutes) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control @error('duration') is-invalid @enderror" id="duration" name="duration" value="{{ old('duration', 180) }}" min="1" required>
                                            @error('duration')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="random_additional_instructions" class="form-label">Additional Instructions</label>
                                            <textarea class="form-control @error('additional_instructions') is-invalid @enderror" id="random_additional_instructions" name="additional_instructions" rows="3">{{ old('additional_instructions') }}</textarea>
                                            @error('additional_instructions')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">Question Distribution</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-info mb-4">
                                            <i class="fas fa-info-circle me-2"></i>
                                            Configure how questions should be distributed across different types, difficulty levels, and Bloom's taxonomy levels.
                                        </div>
                                        
                                        <div class="row mb-4">
                                            <div class="col-md-4">
                                                <h6 class="mb-3">Question Types</h6>
                                                <div class="mb-2">
                                                    <div class="input-group">
                                                        <span class="input-group-text">MCQ</span>
                                                        <input type="number" class="form-control question-type-percent" name="question_type_distribution[mcq]" value="{{ old('question_type_distribution.mcq', 30) }}" min="0" max="100">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                                <div class="mb-2">
                                                    <div class="input-group">
                                                        <span class="input-group-text">True/False</span>
                                                        <input type="number" class="form-control question-type-percent" name="question_type_distribution[true_false]" value="{{ old('question_type_distribution.true_false', 10) }}" min="0" max="100">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                                <div class="mb-2">
                                                    <div class="input-group">
                                                        <span class="input-group-text">Short Answer</span>
                                                        <input type="number" class="form-control question-type-percent" name="question_type_distribution[short_answer]" value="{{ old('question_type_distribution.short_answer', 30) }}" min="0" max="100">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                                <div class="mb-2">
                                                    <div class="input-group">
                                                        <span class="input-group-text">Long Answer</span>
                                                        <input type="number" class="form-control question-type-percent" name="question_type_distribution[long_answer]" value="{{ old('question_type_distribution.long_answer', 20) }}" min="0" max="100">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                                <div class="mb-2">
                                                    <div class="input-group">
                                                        <span class="input-group-text">Fill in the Blank</span>
                                                        <input type="number" class="form-control question-type-percent" name="question_type_distribution[fill_in_the_blank]" value="{{ old('question_type_distribution.fill_in_the_blank', 10) }}" min="0" max="100">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                                <div class="progress mt-2">
                                                    <div class="progress-bar" id="questionTypeProgressBar" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">100%</div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-4">
                                                <h6 class="mb-3">Difficulty Levels</h6>
                                                <div class="mb-2">
                                                    <div class="input-group">
                                                        <span class="input-group-text">Easy</span>
                                                        <input type="number" class="form-control difficulty-percent" name="difficulty_distribution[easy]" value="{{ old('difficulty_distribution.easy', 30) }}" min="0" max="100">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                                <div class="mb-2">
                                                    <div class="input-group">
                                                        <span class="input-group-text">Medium</span>
                                                        <input type="number" class="form-control difficulty-percent" name="difficulty_distribution[medium]" value="{{ old('difficulty_distribution.medium', 50) }}" min="0" max="100">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                                <div class="mb-2">
                                                    <div class="input-group">
                                                        <span class="input-group-text">Hard</span>
                                                        <input type="number" class="form-control difficulty-percent" name="difficulty_distribution[hard]" value="{{ old('difficulty_distribution.hard', 20) }}" min="0" max="100">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                                <div class="progress mt-2">
                                                    <div class="progress-bar" id="difficultyProgressBar" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">100%</div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-4">
                                                <h6 class="mb-3">Bloom's Taxonomy Levels</h6>
                                                <div id="bloomsDistributionContainer">
                                                    @foreach($bloomsTaxonomies as $index => $taxonomy)
                                                        <div class="mb-2">
                                                            <div class="input-group">
                                                                <span class="input-group-text">{{ $taxonomy->level_name }}</span>
                                                                <input type="number" class="form-control blooms-percent" name="blooms_distribution[{{ $taxonomy->id }}]" value="{{ old('blooms_distribution.' . $taxonomy->id, $index == 0 ? 100 : 0) }}" min="0" max="100">
                                                                <span class="input-group-text">%</span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <div class="progress mt-2">
                                                    <div class="progress-bar" id="bloomsProgressBar" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">100%</div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="alert alert-warning" id="distributionWarning" style="display: none;">
                                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                                    <span id="distributionWarningMessage"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">Unit Coverage</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-info mb-4">
                                            <i class="fas fa-info-circle me-2"></i>
                                            Select a subject to configure unit coverage.
                                        </div>
                                        
                                        <div id="unitCoverageContainer">
                                            <!-- Units will be loaded dynamically based on selected subject -->
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">Generation Options</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="random_shuffle_questions" name="shuffle_questions" value="1" {{ old('shuffle_questions', '1') ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="random_shuffle_questions">
                                                            Shuffle Questions Within Sections
                                                        </label>
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="random_include_answer_key" name="include_answer_key" value="1" {{ old('include_answer_key') ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="random_include_answer_key">
                                                            Include Answer Key
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="random_include_marking_scheme" name="include_marking_scheme" value="1" {{ old('include_marking_scheme') ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="random_include_marking_scheme">
                                                            Include Marking Scheme
                                                        </label>
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="random_preview_before_save" name="preview_before_save" value="1" {{ old('preview_before_save', '1') ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="random_preview_before_save">
                                                            Preview Before Saving
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-file-alt me-2"></i>Generate Random Question Paper
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Blueprint form elements
        const blueprintSelect = document.getElementById('blueprint_id');
        const blueprintDetails = document.getElementById('blueprintDetails');
        
        // Random form elements
        const subjectSelect = document.getElementById('subject_id');
        const unitCoverageContainer = document.getElementById('unitCoverageContainer');
        const questionTypeInputs = document.querySelectorAll('.question-type-percent');
        const difficultyInputs = document.querySelectorAll('.difficulty-percent');
        const bloomsInputs = document.querySelectorAll('.blooms-percent');
        const questionTypeProgressBar = document.getElementById('questionTypeProgressBar');
        const difficultyProgressBar = document.getElementById('difficultyProgressBar');
        const bloomsProgressBar = document.getElementById('bloomsProgressBar');
        const distributionWarning = document.getElementById('distributionWarning');
        const distributionWarningMessage = document.getElementById('distributionWarningMessage');
        
        // Tab switching
        const paperTypeTabs = document.getElementById('paperTypeTabs');
        const blueprintTab = document.getElementById('blueprint-tab');
        const randomTab = document.getElementById('random-tab');
        const blueprintForm = document.getElementById('blueprintForm');
        const randomForm = document.getElementById('randomForm');
        const titleInput = document.getElementById('title');
        const randomTitleInput = document.getElementById('random_title');
        const examDateInput = document.getElementById('exam_date');
        const randomExamDateInput = document.getElementById('random_exam_date');
        const additionalInstructionsInput = document.getElementById('additional_instructions');
        const randomAdditionalInstructionsInput = document.getElementById('random_additional_instructions');
        
        // Initialize rich text editor for instructions if available
        if (typeof ClassicEditor !== 'undefined') {
            ClassicEditor.create(document.getElementById('additional_instructions'))
                .catch(error => {
                    console.error(error);
                });
                
            ClassicEditor.create(document.getElementById('random_additional_instructions'))
                .catch(error => {
                    console.error(error);
                });
        }
        
        // Load blueprint details when blueprint is selected
        blueprintSelect.addEventListener('change', function() {
            const blueprintId = this.value;
            
            if (blueprintId) {
                fetch(`{{ url('admin/blueprints') }}/${blueprintId}/details`)
                    .then(response => response.json())
                    .then(data => {
                        displayBlueprintDetails(data);
                    })
                    .catch(error => {
                        console.error('Error fetching blueprint details:', error);
                        blueprintDetails.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                Error loading blueprint details. Please try again.
                            </div>
                        `;
                    });
            } else {
                blueprintDetails.innerHTML = `
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Select a blueprint to view its details.
                    </div>
                `;
            }
        });
        
        // Display blueprint details
        function displayBlueprintDetails(blueprint) {
            let sectionsHtml = '';
            
            blueprint.sections.forEach(section => {
                let rulesHtml = '';
                
                section.rules.forEach(rule => {
                    rulesHtml += `
                        <div class="mb-2">
                            <div class="card bg-light">
                                <div class="card-body py-2">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <small>
                                                ${rule.unit_name ? rule.unit_name : 'Any Unit'} / 
                                                ${rule.topic_name ? rule.topic_name : 'Any Topic'} / 
                                                ${rule.question_type ? rule.question_type.toUpperCase() : 'Any Type'} / 
                                                ${rule.difficulty_level ? rule.difficulty_level.charAt(0).toUpperCase() + rule.difficulty_level.slice(1) : 'Any Difficulty'} / 
                                                ${rule.blooms_level ? rule.blooms_level : 'Any Bloom\'s Level'}
                                            </small>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <small>${rule.num_questions} questions × ${rule.marks_per_question} marks = ${rule.num_questions * rule.marks_per_question} marks</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                sectionsHtml += `
                    <div class="mb-4">
                        <h6>${section.title} (${section.total_marks} marks)</h6>
                        ${section.instructions ? `<p class="text-muted small">${section.instructions}</p>` : ''}
                        ${rulesHtml}
                    </div>
                `;
            });
            
            blueprintDetails.innerHTML = `
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p><strong>Subject:</strong> ${blueprint.subject_name}</p>
                        <p><strong>Total Marks:</strong> ${blueprint.total_marks}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Duration:</strong> ${blueprint.duration} minutes</p>
                        <p><strong>Total Questions:</strong> ${blueprint.total_questions}</p>
                    </div>
                </div>
                
                <h6 class="mb-3">Sections</h6>
                ${sectionsHtml}
            `;
        }
        
        // Load units when subject is selected
        subjectSelect.addEventListener('change', function() {
            const subjectId = this.value;
            
            if (subjectId) {
                fetch(`{{ url('admin/units/by-subject') }}/${subjectId}`)
                    .then(response => response.json())
                    .then(data => {
                        displayUnitCoverage(data);
                    })
                    .catch(error => {
                        console.error('Error fetching units:', error);
                        unitCoverageContainer.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                Error loading units. Please try again.
                            </div>
                        `;
                    });
            } else {
                unitCoverageContainer.innerHTML = `
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Select a subject to configure unit coverage.
                    </div>
                `;
            }
        });
        
        // Display unit coverage inputs
        function displayUnitCoverage(units) {
            if (units.length === 0) {
                unitCoverageContainer.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        No units found for the selected subject.
                    </div>
                `;
                return;
            }
            
            let unitsHtml = `
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="equal_unit_distribution" name="equal_unit_distribution" value="1" checked>
                            <label class="form-check-label" for="equal_unit_distribution">
                                Distribute questions equally across all units
                            </label>
                        </div>
                    </div>
                </div>
                
                <div id="unitDistributionInputs" style="display: none;">
                    <div class="row">
            `;
            
            units.forEach(unit => {
                unitsHtml += `
                    <div class="col-md-6 mb-2">
                        <div class="input-group">
                            <span class="input-group-text">${unit.unit_name}</span>
                            <input type="number" class="form-control unit-percent" name="unit_distribution[${unit.id}]" value="${Math.round(100 / units.length)}" min="0" max="100">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                `;
            });
            
            unitsHtml += `
                    </div>
                    <div class="progress mt-3">
                        <div class="progress-bar" id="unitProgressBar" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">100%</div>
                    </div>
                </div>
            `;
            
            unitCoverageContainer.innerHTML = unitsHtml;
            
            // Add event listener for equal distribution checkbox
            const equalDistributionCheckbox = document.getElementById('equal_unit_distribution');
            const unitDistributionInputs = document.getElementById('unitDistributionInputs');
            
            equalDistributionCheckbox.addEventListener('change', function() {
                unitDistributionInputs.style.display = this.checked ? 'none' : 'block';
            });
            
            // Add event listeners for unit distribution inputs
            const unitInputs = document.querySelectorAll('.unit-percent');
            const unitProgressBar = document.getElementById('unitProgressBar');
            
            unitInputs.forEach(input => {
                input.addEventListener('change', function() {
                    updateUnitDistributionProgress(unitInputs, unitProgressBar);
                });
            });
        }
        
        // Update progress bars for distributions
        function updateDistributionProgress(inputs, progressBar) {
            let total = 0;
            
            inputs.forEach(input => {
                total += parseInt(input.value) || 0;
            });
            
            const percentage = Math.min(100, total);
            progressBar.style.width = `${percentage}%`;
            progressBar.textContent = `${percentage}%`;
            progressBar.setAttribute('aria-valuenow', percentage);
            
            if (total !== 100) {
                progressBar.classList.remove('bg-success');
                progressBar.classList.add('bg-warning');
                return false;
            } else {
                progressBar.classList.remove('bg-warning');
                progressBar.classList.add('bg-success');
                return true;
            }
        }
        
        // Update question type distribution progress
        function updateQuestionTypeDistributionProgress() {
            return updateDistributionProgress(questionTypeInputs, questionTypeProgressBar);
        }
        
        // Update difficulty distribution progress
        function updateDifficultyDistributionProgress() {
            return updateDistributionProgress(difficultyInputs, difficultyProgressBar);
        }
        
        // Update Bloom's taxonomy distribution progress
        function updateBloomsDistributionProgress() {
            return updateDistributionProgress(bloomsInputs, bloomsProgressBar);
        }
        
        // Update unit distribution progress
        function updateUnitDistributionProgress(unitInputs, unitProgressBar) {
            return updateDistributionProgress(unitInputs, unitProgressBar);
        }
        
        // Add event listeners for distribution inputs
        questionTypeInputs.forEach(input => {
            input.addEventListener('change', function() {
                validateDistributions();
            });
        });
        
        difficultyInputs.forEach(input => {
            input.addEventListener('change', function() {
                validateDistributions();
            });
        });
        
        bloomsInputs.forEach(input => {
            input.addEventListener('change', function() {
                validateDistributions();
            });
        });
        
        // Validate all distributions
        function validateDistributions() {
            const questionTypeValid = updateQuestionTypeDistributionProgress();
            const difficultyValid = updateDifficultyDistributionProgress();
            const bloomsValid = updateBloomsDistributionProgress();
            
            if (!questionTypeValid || !difficultyValid || !bloomsValid) {
                distributionWarning.style.display = 'block';
                distributionWarningMessage.textContent = 'Each distribution should total exactly 100%. Please adjust your values.';
            } else {
                distributionWarning.style.display = 'none';
            }
        }
        
        // Sync form fields between tabs
        paperTypeTabs.addEventListener('shown.bs.tab', function(event) {
            if (event.target.id === 'blueprint-tab') {
                // Sync from random to blueprint
                titleInput.value = randomTitleInput.value;
                examDateInput.value = randomExamDateInput.value;
                // For rich text editors, we need to update the underlying textarea
                additionalInstructionsInput.value = randomAdditionalInstructionsInput.value;
            } else if (event.target.id === 'random-tab') {
                // Sync from blueprint to random
                randomTitleInput.value = titleInput.value;
                randomExamDateInput.value = examDateInput.value;
                // For rich text editors, we need to update the underlying textarea
                randomAdditionalInstructionsInput.value = additionalInstructionsInput.value;
            }
        });
        
        // Form submission validation
        blueprintForm.addEventListener('submit', function(event) {
            const blueprintId = blueprintSelect.value;
            
            if (!blueprintId) {
                event.preventDefault();
                alert('Please select a blueprint.');
                return;
            }
        });
        
        randomForm.addEventListener('submit', function(event) {
            const subjectId = subjectSelect.value;
            
            if (!subjectId) {
                event.preventDefault();
                alert('Please select a subject.');
                return;
            }
            
            const questionTypeValid = updateQuestionTypeDistributionProgress();
            const difficultyValid = updateDifficultyDistributionProgress();
            const bloomsValid = updateBloomsDistributionProgress();
            
            if (!questionTypeValid || !difficultyValid || !bloomsValid) {
                event.preventDefault();
                alert('Each distribution should total exactly 100%. Please adjust your values.');
                return;
            }
            
            const equalDistributionCheckbox = document.getElementById('equal_unit_distribution');
            
            if (!equalDistributionCheckbox.checked) {
                const unitInputs = document.querySelectorAll('.unit-percent');
                const unitProgressBar = document.getElementById('unitProgressBar');
                const unitValid = updateUnitDistributionProgress(unitInputs, unitProgressBar);
                
                if (!unitValid) {
                    event.preventDefault();
                    alert('Unit distribution should total exactly 100%. Please adjust your values.');
                    return;
                }
            }
        });
        
        // Initialize distributions validation
        validateDistributions();
    });
</script>
@endpush
@endsection
