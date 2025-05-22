@extends('layouts.question-bank')

@section('title', 'Create Blueprint')

@section('content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('question-bank.blueprints.index') }}">Blueprints</a></li>
        <li class="breadcrumb-item active" aria-current="page">Create</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Create Blueprint</h1>
</div>

<form action="{{ route('question-bank.blueprints.store') }}" method="POST" id="blueprint-form">
    @csrf
    
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4 fade-in">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold">Blueprint Information</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                        <small class="form-text text-muted">A descriptive name for this blueprint (e.g., "Midterm Exam - Mathematics 101")</small>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        <small class="form-text text-muted">A brief description of this blueprint's purpose and structure.</small>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="subject_id" class="form-label">Subject</label>
                            <select class="form-select @error('subject_id') is-invalid @enderror" id="subject_id" name="subject_id">
                                <option value="">Select Subject (Optional)</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Leave blank for multi-subject blueprints.</small>
                            @error('subject_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="total_marks" class="form-label">Total Marks <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('total_marks') is-invalid @enderror" id="total_marks" name="total_marks" value="{{ old('total_marks') }}" min="1" required>
                            <small class="form-text text-muted">The total marks for question papers generated from this blueprint.</small>
                            @error('total_marks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card shadow fade-in">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">Blueprint Conditions</h6>
                    <button type="button" class="btn btn-sm btn-primary" id="add-condition-btn">
                        <i class="fas fa-plus"></i> Add Condition
                    </button>
                </div>
                <div class="card-body">
                    <div id="conditions-container">
                        <!-- Conditions will be added here dynamically -->
                        @if(old('conditions'))
                            @foreach(old('conditions') as $index => $condition)
                                <div class="condition-card card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="condition-title mb-0">Condition #{{ $index + 1 }}</h6>
                                            <button type="button" class="btn btn-sm btn-danger remove-condition-btn">
                                                <i class="fas fa-times"></i> Remove
                                            </button>
                                        </div>
                                        
                                        <input type="hidden" name="conditions[{{ $index }}][id]" value="{{ $condition['id'] ?? '' }}">
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Filter Type <span class="text-danger">*</span></label>
                                                <select class="form-select filter-type" name="conditions[{{ $index }}][filter_type]" required>
                                                    <option value="">Select Filter Type</option>
                                                    <option value="subject" {{ $condition['filter_type'] === 'subject' ? 'selected' : '' }}>Subject</option>
                                                    <option value="unit" {{ $condition['filter_type'] === 'unit' ? 'selected' : '' }}>Unit</option>
                                                    <option value="topic" {{ $condition['filter_type'] === 'topic' ? 'selected' : '' }}>Topic</option>
                                                    <option value="question_type" {{ $condition['filter_type'] === 'question_type' ? 'selected' : '' }}>Question Type</option>
                                                    <option value="blooms_taxonomy" {{ $condition['filter_type'] === 'blooms_taxonomy' ? 'selected' : '' }}>Bloom's Taxonomy Level</option>
                                                    <option value="difficulty_level" {{ $condition['filter_type'] === 'difficulty_level' ? 'selected' : '' }}>Difficulty Level</option>
                                                </select>
                                            </div>
                                            
                                            <div class="col-md-6 mb-3 filter-value-container">
                                                <!-- Filter value field will be dynamically populated based on filter type -->
                                                @if($condition['filter_type'] === 'subject')
                                                    <label class="form-label">Subject <span class="text-danger">*</span></label>
                                                    <select class="form-select" name="conditions[{{ $index }}][filter_value]" required>
                                                        <option value="">Select Subject</option>
                                                        @foreach($subjects as $subject)
                                                            <option value="{{ $subject->id }}" {{ $condition['filter_value'] == $subject->id ? 'selected' : '' }}>
                                                                {{ $subject->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @elseif($condition['filter_type'] === 'question_type')
                                                    <label class="form-label">Question Type <span class="text-danger">*</span></label>
                                                    <select class="form-select" name="conditions[{{ $index }}][filter_value]" required>
                                                        <option value="">Select Question Type</option>
                                                        @foreach($questionTypes as $type)
                                                            <option value="{{ $type->id }}" {{ $condition['filter_value'] == $type->id ? 'selected' : '' }}>
                                                                {{ $type->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @elseif($condition['filter_type'] === 'blooms_taxonomy')
                                                    <label class="form-label">Bloom's Taxonomy Level <span class="text-danger">*</span></label>
                                                    <select class="form-select" name="conditions[{{ $index }}][filter_value]" required>
                                                        <option value="">Select Level</option>
                                                        @foreach($bloomsLevels as $level)
                                                            <option value="{{ $level->id }}" {{ $condition['filter_value'] == $level->id ? 'selected' : '' }}>
                                                                {{ $level->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @elseif($condition['filter_type'] === 'difficulty_level')
                                                    <label class="form-label">Difficulty Level <span class="text-danger">*</span></label>
                                                    <select class="form-select" name="conditions[{{ $index }}][filter_value]" required>
                                                        <option value="">Select Difficulty</option>
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <option value="{{ $i }}" {{ $condition['filter_value'] == $i ? 'selected' : '' }}>
                                                                {{ $i }} - {{ ['Very Easy', 'Easy', 'Medium', 'Hard', 'Very Hard'][$i-1] }}
                                                            </option>
                                                        @endfor
                                                    </select>
                                                @else
                                                    <label class="form-label">Filter Value <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-select" name="conditions[{{ $index }}][filter_value]" value="{{ $condition['filter_value'] }}" required>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Number of Questions <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control question-count" name="conditions[{{ $index }}][question_count]" value="{{ $condition['question_count'] }}" min="1" required>
                                            </div>
                                            
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Marks per Question <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control marks-per-question" name="conditions[{{ $index }}][marks_per_question]" value="{{ $condition['marks_per_question'] }}" min="1" required>
                                            </div>
                                            
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Total Marks</label>
                                                <input type="text" class="form-control total-condition-marks" value="{{ $condition['question_count'] * $condition['marks_per_question'] }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    
                    @if(!old('conditions'))
                        <div class="alert alert-info mb-0" id="no-conditions-message">
                            <i class="fas fa-info-circle me-2"></i> No conditions added yet. Click the "Add Condition" button to define criteria for your blueprint.
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow mb-4 fade-in">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold">Summary</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="fw-bold">Total Conditions:</label>
                        <p id="total-conditions">{{ old('conditions') ? count(old('conditions')) : 0 }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Total Questions:</label>
                        <p id="total-questions">0</p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Total Marks:</label>
                        <p id="summary-total-marks">0</p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Target Marks:</label>
                        <p id="target-marks">{{ old('total_marks') ?: 0 }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Status:</label>
                        <p id="marks-status">
                            <span class="badge bg-warning">No conditions added</span>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="card shadow mb-4 fade-in">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold">Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('question-bank.blueprints.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Blueprint
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Condition Template (Hidden) -->
<template id="condition-template">
    <div class="condition-card card mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="condition-title mb-0">Condition #1</h6>
                <button type="button" class="btn btn-sm btn-danger remove-condition-btn">
                    <i class="fas fa-times"></i> Remove
                </button>
            </div>
            
            <input type="hidden" name="conditions[0][id]" value="">
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Filter Type <span class="text-danger">*</span></label>
                    <select class="form-select filter-type" name="conditions[0][filter_type]" required>
                        <option value="">Select Filter Type</option>
                        <option value="subject">Subject</option>
                        <option value="unit">Unit</option>
                        <option value="topic">Topic</option>
                        <option value="question_type">Question Type</option>
                        <option value="blooms_taxonomy">Bloom's Taxonomy Level</option>
                        <option value="difficulty_level">Difficulty Level</option>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3 filter-value-container">
                    <!-- Filter value field will be dynamically populated based on filter type -->
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Number of Questions <span class="text-danger">*</span></label>
                    <input type="number" class="form-control question-count" name="conditions[0][question_count]" value="1" min="1" required>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Marks per Question <span class="text-danger">*</span></label>
                    <input type="number" class="form-control marks-per-question" name="conditions[0][marks_per_question]" value="1" min="1" required>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Total Marks</label>
                    <input type="text" class="form-control total-condition-marks" value="1" readonly>
                </div>
            </div>
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        let conditionCount = {{ old('conditions') ? count(old('conditions')) : 0 }};
        
        // Add condition button click handler
        $('#add-condition-btn').on('click', function() {
            addCondition();
        });
        
        // Remove condition button click handler (using event delegation)
        $(document).on('click', '.remove-condition-btn', function() {
            $(this).closest('.condition-card').remove();
            conditionCount--;
            
            // Update condition numbers
            updateConditionNumbers();
            
            // Show "no conditions" message if no conditions are present
            if (conditionCount === 0) {
                $('#no-conditions-message').show();
            }
            
            // Update summary
            updateSummary();
        });
        
        // Filter type change handler (using event delegation)
        $(document).on('change', '.filter-type', function() {
            const filterType = $(this).val();
            const container = $(this).closest('.row').find('.filter-value-container');
            const conditionIndex = $(this).closest('.condition-card').index();
            
            // Clear the container
            container.empty();
            
            // Generate the appropriate filter value field based on the selected filter type
            let filterValueField = '';
            
            switch (filterType) {
                case 'subject':
                    filterValueField = `
                        <label class="form-label">Subject <span class="text-danger">*</span></label>
                        <select class="form-select" name="conditions[${conditionIndex}][filter_value]" required>
                            <option value="">Select Subject</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    `;
                    break;
                    
                case 'unit':
                    filterValueField = `
                        <label class="form-label">Unit <span class="text-danger">*</span></label>
                        <select class="form-select" name="conditions[${conditionIndex}][filter_value]" required>
                            <option value="">Select Unit</option>
                            <!-- Units will be populated dynamically based on selected subject -->
                        </select>
                    `;
                    break;
                    
                case 'topic':
                    filterValueField = `
                        <label class="form-label">Topic <span class="text-danger">*</span></label>
                        <select class="form-select" name="conditions[${conditionIndex}][filter_value]" required>
                            <option value="">Select Topic</option>
                            <!-- Topics will be populated dynamically based on selected unit -->
                        </select>
                    `;
                    break;
                    
                case 'question_type':
                    filterValueField = `
                        <label class="form-label">Question Type <span class="text-danger">*</span></label>
                        <select class="form-select" name="conditions[${conditionIndex}][filter_value]" required>
                            <option value="">Select Question Type</option>
                            @foreach($questionTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    `;
                    break;
                    
                case 'blooms_taxonomy':
                    filterValueField = `
                        <label class="form-label">Bloom's Taxonomy Level <span class="text-danger">*</span></label>
                        <select class="form-select" name="conditions[${conditionIndex}][filter_value]" required>
                            <option value="">Select Level</option>
                            @foreach($bloomsLevels as $level)
                                <option value="{{ $level->id }}">{{ $level->name }}</option>
                            @endforeach
                        </select>
                    `;
                    break;
                    
                case 'difficulty_level':
                    filterValueField = `
                        <label class="form-label">Difficulty Level <span class="text-danger">*</span></label>
                        <select class="form-select" name="conditions[${conditionIndex}][filter_value]" required>
                            <option value="">Select Difficulty</option>
                            @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}">{{ $i }} - {{ ['Very Easy', 'Easy', 'Medium', 'Hard', 'Very Hard'][$i-1] }}</option>
                            @endfor
                        </select>
                    `;
                    break;
                    
                default:
                    filterValueField = `
                        <label class="form-label">Filter Value <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="conditions[${conditionIndex}][filter_value]" required>
                    `;
            }
            
            container.html(filterValueField);
        });
        
        // Update total marks when question count or marks per question changes
        $(document).on('input', '.question-count, .marks-per-question', function() {
            const card = $(this).closest('.condition-card');
            const questionCount = parseInt(card.find('.question-count').val()) || 0;
            const marksPerQuestion = parseInt(card.find('.marks-per-question').val()) || 0;
            const totalMarks = questionCount * marksPerQuestion;
            
            card.find('.total-condition-marks').val(totalMarks);
            
            // Update summary
            updateSummary();
        });
        
        // Update total marks field when the target marks field changes
        $('#total_marks').on('input', function() {
            $('#target-marks').text($(this).val() || 0);
            updateSummary();
        });
        
        // Function to add a new condition
        function addCondition() {
            // Clone the template
            const template = document.getElementById('condition-template');
            const clone = document.importNode(template.content, true);
            
            // Update the condition index
            const conditionElements = clone.querySelectorAll('[name^="conditions[0]"]');
            conditionElements.forEach(element => {
                element.name = element.name.replace('conditions[0]', `conditions[${conditionCount}]`);
            });
            
            // Update the condition title
            clone.querySelector('.condition-title').textContent = `Condition #${conditionCount + 1}`;
            
            // Append the clone to the container
            document.getElementById('conditions-container').appendChild(clone);
            
            // Hide "no conditions" message
            $('#no-conditions-message').hide();
            
            // Increment condition count
            conditionCount++;
            
            // Update summary
            updateSummary();
        }
        
        // Function to update condition numbers
        function updateConditionNumbers() {
            $('.condition-card').each(function(index) {
                $(this).find('.condition-title').text(`Condition #${index + 1}`);
                
                // Update the condition index in field names
                const conditionElements = $(this).find('[name^="conditions["]');
                conditionElements.each(function() {
                    const name = $(this).attr('name');
                    const newName = name.replace(/conditions\[\d+\]/, `conditions[${index}]`);
                    $(this).attr('name', newName);
                });
            });
        }
        
        // Function to update the summary
        function updateSummary() {
            let totalQuestions = 0;
            let totalMarks = 0;
            
            $('.condition-card').each(function() {
                const questionCount = parseInt($(this).find('.question-count').val()) || 0;
                const marksPerQuestion = parseInt($(this).find('.marks-per-question').val()) || 0;
                
                totalQuestions += questionCount;
                totalMarks += questionCount * marksPerQuestion;
            });
            
            $('#total-conditions').text(conditionCount);
            $('#total-questions').text(totalQuestions);
            $('#summary-total-marks').text(totalMarks);
            
            const targetMarks = parseInt($('#total_marks').val()) || 0;
            
            // Update status
            let statusBadge = '';
            if (conditionCount === 0) {
                statusBadge = '<span class="badge bg-warning">No conditions added</span>';
            } else if (totalMarks < targetMarks) {
                statusBadge = `<span class="badge bg-danger">Insufficient marks (${totalMarks}/${targetMarks})</span>`;
            } else if (totalMarks > targetMarks) {
                statusBadge = `<span class="badge bg-danger">Excess marks (${totalMarks}/${targetMarks})</span>`;
            } else {
                statusBadge = `<span class="badge bg-success">Marks match (${totalMarks}/${targetMarks})</span>`;
            }
            
            $('#marks-status').html(statusBadge);
        }
        
        // Initialize summary
        updateSummary();
    });
</script>
@endpush

