@extends('layouts.question-bank')

@section('title', 'Edit Question')

@section('content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('question-bank.questions.index') }}">Questions</a></li>
        <li class="breadcrumb-item"><a href="{{ route('question-bank.questions.show', $question) }}">View Question</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Edit Question</h1>
</div>

<div class="card shadow fade-in">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold">Question Information</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('question-bank.questions.update', $question) }}" method="POST" id="question-form">
            @csrf
            @method('PATCH')
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="subject_id" class="form-label">Subject <span class="text-danger">*</span></label>
                    <select class="form-select @error('subject_id') is-invalid @enderror" id="subject_id" name="subject_id" required>
                        <option value="">Select Subject</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('subject_id', $question->topic->unit->subject_id) == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('subject_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="unit_id" class="form-label">Unit <span class="text-danger">*</span></label>
                    <select class="form-select @error('unit_id') is-invalid @enderror" id="unit_id" name="unit_id" required>
                        <option value="">Select Unit</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}" {{ old('unit_id', $question->topic->unit_id) == $unit->id ? 'selected' : '' }}>
                                {{ $unit->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('unit_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="topic_id" class="form-label">Topic <span class="text-danger">*</span></label>
                    <select class="form-select @error('topic_id') is-invalid @enderror" id="topic_id" name="topic_id" required>
                        <option value="">Select Topic</option>
                        @foreach($topics as $topic)
                            <option value="{{ $topic->id }}" {{ old('topic_id', $question->topic_id) == $topic->id ? 'selected' : '' }}>
                                {{ $topic->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('topic_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="question_type_id" class="form-label">Question Type <span class="text-danger">*</span></label>
                    <select class="form-select @error('question_type_id') is-invalid @enderror" id="question_type_id" name="question_type_id" required>
                        <option value="">Select Question Type</option>
                        @foreach($questionTypes as $type)
                            <option value="{{ $type->id }}" data-structure="{{ json_encode($type->structure) }}" {{ old('question_type_id', $question->question_type_id) == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('question_type_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="blooms_taxonomy_id" class="form-label">Bloom's Taxonomy Level</label>
                    <select class="form-select @error('blooms_taxonomy_id') is-invalid @enderror" id="blooms_taxonomy_id" name="blooms_taxonomy_id">
                        <option value="">Select Bloom's Level</option>
                        @foreach($bloomsLevels as $level)
                            <option value="{{ $level->id }}" {{ old('blooms_taxonomy_id', $question->blooms_taxonomy_id) == $level->id ? 'selected' : '' }}>
                                {{ $level->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('blooms_taxonomy_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-2 mb-3">
                    <label for="difficulty_level" class="form-label">Difficulty Level <span class="text-danger">*</span></label>
                    <select class="form-select @error('difficulty_level') is-invalid @enderror" id="difficulty_level" name="difficulty_level" required>
                        <option value="">Select Difficulty</option>
                        @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ old('difficulty_level', $question->difficulty_level) == $i ? 'selected' : '' }}>
                                {{ $i }} - {{ ['Very Easy', 'Easy', 'Medium', 'Hard', 'Very Hard'][$i-1] }}
                            </option>
                        @endfor
                    </select>
                    @error('difficulty_level')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-2 mb-3">
                    <label for="marks" class="form-label">Marks <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('marks') is-invalid @enderror" id="marks" name="marks" value="{{ old('marks', $question->marks) }}" min="1" required>
                    @error('marks')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <label for="question_text" class="form-label">Question Text <span class="text-danger">*</span></label>
                <textarea class="form-control @error('question_text') is-invalid @enderror" id="question_text" name="question_text" rows="3" required>{{ old('question_text', $question->question_text) }}</textarea>
                @error('question_text')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div id="dynamic-fields-container">
                <!-- Dynamic fields will be inserted here based on the selected question type -->
            </div>
            
            <div class="mb-3">
                <label for="solution" class="form-label">Solution/Explanation</label>
                <textarea class="form-control @error('solution') is-invalid @enderror" id="solution" name="solution" rows="3">{{ old('solution', $question->solution) }}</textarea>
                <small class="form-text text-muted">Provide a detailed solution or explanation for this question.</small>
                @error('solution')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $question->is_active) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Active</label>
                <small class="form-text text-muted d-block">Inactive questions will not be available for selection in question papers.</small>
            </div>
            
            <div class="d-flex justify-content-end">
                <a href="{{ route('question-bank.questions.show', $question) }}" class="btn btn-secondary me-2">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Question
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Store the question content for reference
    const questionContent = @json($question->content);
    
    // Dynamic dropdowns for subject -> unit -> topic
    $('#subject_id').on('change', function() {
        const subjectId = $(this).val();
        const unitDropdown = $('#unit_id');
        const topicDropdown = $('#topic_id');
        
        // Reset unit and topic dropdowns
        unitDropdown.html('<option value="">Select Unit</option>');
        topicDropdown.html('<option value="">Select Topic</option>');
        
        if (subjectId) {
            // Fetch units for the selected subject
            $.ajax({
                url: `/api/subjects/${subjectId}/units`,
                type: 'GET',
                success: function(data) {
                    if (data.length > 0) {
                        data.forEach(function(unit) {
                            unitDropdown.append(`<option value="${unit.id}">${unit.name}</option>`);
                        });
                    }
                }
            });
        }
    });
    
    $('#unit_id').on('change', function() {
        const unitId = $(this).val();
        const topicDropdown = $('#topic_id');
        
        // Reset topic dropdown
        topicDropdown.html('<option value="">Select Topic</option>');
        
        if (unitId) {
            // Fetch topics for the selected unit
            $.ajax({
                url: `/api/units/${unitId}/topics`,
                type: 'GET',
                success: function(data) {
                    if (data.length > 0) {
                        data.forEach(function(topic) {
                            topicDropdown.append(`<option value="${topic.id}">${topic.name}</option>`);
                        });
                    }
                }
            });
        }
    });
    
    // Dynamic fields based on question type
    $('#question_type_id').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const structure = selectedOption.data('structure');
        const container = $('#dynamic-fields-container');
        
        // Clear previous fields
        container.empty();
        
        if (!structure) {
            return;
        }
        
        // Generate fields based on structure
        Object.entries(structure).forEach(([key, field]) => {
            let fieldHtml = '';
            let fieldValue = questionContent && questionContent[key] ? questionContent[key] : '';
            
            switch (field.type) {
                case 'text':
                    fieldHtml = `
                        <div class="mb-3">
                            <label for="${key}" class="form-label">${field.label} ${field.required ? '<span class="text-danger">*</span>' : ''}</label>
                            <input type="text" class="form-control @error('${key}') is-invalid @enderror" id="${key}" name="${key}" value="${fieldValue}" ${field.required ? 'required' : ''}>
                            ${field.description ? `<small class="form-text text-muted">${field.description}</small>` : ''}
                            @error('${key}')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    `;
                    break;
                    
                case 'textarea':
                    fieldHtml = `
                        <div class="mb-3">
                            <label for="${key}" class="form-label">${field.label} ${field.required ? '<span class="text-danger">*</span>' : ''}</label>
                            <textarea class="form-control @error('${key}') is-invalid @enderror" id="${key}" name="${key}" rows="3" ${field.required ? 'required' : ''}>${fieldValue}</textarea>
                            ${field.description ? `<small class="form-text text-muted">${field.description}</small>` : ''}
                            @error('${key}')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    `;
                    break;
                    
                case 'options':
                    fieldHtml = `
                        <div class="mb-3">
                            <label class="form-label">${field.label} ${field.required ? '<span class="text-danger">*</span>' : ''}</label>
                            <div id="${key}-container">`;
                    
                    if (questionContent && questionContent.options && questionContent.options.length > 0) {
                        questionContent.options.forEach((option, index) => {
                            fieldHtml += `
                                <div class="option-row mb-2 d-flex align-items-center">
                                    <input type="text" class="form-control me-2" name="${key}[]" value="${option}" placeholder="Option text" required>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="correct_option" value="${index}" ${questionContent.correct_option === index ? 'checked' : ''} required>
                                        <label class="form-check-label">Correct</label>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-danger ms-2 remove-option" ${questionContent.options.length <= 1 ? 'disabled' : ''}>
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            `;
                        });
                    } else {
                        fieldHtml += `
                            <div class="option-row mb-2 d-flex align-items-center">
                                <input type="text" class="form-control me-2" name="${key}[]" placeholder="Option text" required>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="correct_option" value="0" required>
                                    <label class="form-check-label">Correct</label>
                                </div>
                                <button type="button" class="btn btn-sm btn-danger ms-2 remove-option" disabled>
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        `;
                    }
                    
                    fieldHtml += `
                            </div>
                            <button type="button" class="btn btn-sm btn-success mt-2 add-option" data-target="${key}-container">
                                <i class="fas fa-plus"></i> Add Option
                            </button>
                            ${field.description ? `<small class="form-text text-muted d-block mt-2">${field.description}</small>` : ''}
                        </div>
                    `;
                    break;
                    
                case 'checkbox':
                    fieldHtml = `
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="${key}" name="${key}" value="1" ${fieldValue ? 'checked' : ''}>
                            <label class="form-check-label" for="${key}">${field.label}</label>
                            ${field.description ? `<small class="form-text text-muted d-block">${field.description}</small>` : ''}
                        </div>
                    `;
                    break;
                    
                case 'number':
                    fieldHtml = `
                        <div class="mb-3">
                            <label for="${key}" class="form-label">${field.label} ${field.required ? '<span class="text-danger">*</span>' : ''}</label>
                            <input type="number" class="form-control @error('${key}') is-invalid @enderror" id="${key}" name="${key}" value="${fieldValue}" ${field.min ? `min="${field.min}"` : ''} ${field.max ? `max="${field.max}"` : ''} ${field.required ? 'required' : ''}>
                            ${field.description ? `<small class="form-text text-muted">${field.description}</small>` : ''}
                            @error('${key}')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    `;
                    break;
                    
                case 'select':
                    let options = '';
                    if (field.options) {
                        field.options.forEach((option) => {
                            options += `<option value="${option.value}" ${fieldValue === option.value ? 'selected' : ''}>${option.label}</option>`;
                        });
                    }
                    
                    fieldHtml = `
                        <div class="mb-3">
                            <label for="${key}" class="form-label">${field.label} ${field.required ? '<span class="text-danger">*</span>' : ''}</label>
                            <select class="form-select @error('${key}') is-invalid @enderror" id="${key}" name="${key}" ${field.required ? 'required' : ''}>
                                <option value="">Select ${field.label}</option>
                                ${options}
                            </select>
                            ${field.description ? `<small class="form-text text-muted">${field.description}</small>` : ''}
                            @error('${key}')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    `;
                    break;
            }
            
            container.append(fieldHtml);
        });
        
        // Initialize option buttons
        initOptionButtons();
    });
    
    // Handle adding and removing options
    function initOptionButtons() {
        $('.add-option').on('click', function() {
            const targetContainer = $(this).data('target');
            const optionCount = $(`#${targetContainer} .option-row`).length;
            
            const newOption = `
                <div class="option-row mb-2 d-flex align-items-center">
                    <input type="text" class="form-control me-2" name="${targetContainer.replace('-container', '')}[]" placeholder="Option text" required>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="correct_option" value="${optionCount}" required>
                        <label class="form-check-label">Correct</label>
                    </div>
                    <button type="button" class="btn btn-sm btn-danger ms-2 remove-option">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            $(`#${targetContainer}`).append(newOption);
            
            // Enable all remove buttons if there are more than one option
            if (optionCount + 1 > 1) {
                $('.remove-option').prop('disabled', false);
            }
        });
        
        // Use event delegation for dynamically added elements
        $(document).on('click', '.remove-option', function() {
            const container = $(this).closest('[id$="-container"]');
            $(this).closest('.option-row').remove();
            
            // Disable remove buttons if only one option remains
            if (container.find('.option-row').length <= 1) {
                container.find('.remove-option').prop('disabled', true);
            }
            
            // Reindex correct_option values
            container.find('.option-row').each(function(index) {
                $(this).find('input[name="correct_option"]').val(index);
            });
        });
    }
    
    // Initialize the form
    $(document).ready(function() {
        // Trigger change event to populate fields
        $('#question_type_id').trigger('change');
    });
</script>
@endpush

