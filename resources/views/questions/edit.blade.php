@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Edit Question</h1>
        <a href="{{ route('questions.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Questions
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('questions.update', $question->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="subject_id" class="form-label">Subject <span class="text-danger">*</span></label>
                        <select class="form-select @error('subject_id') is-invalid @enderror" id="subject_id" name="subject_id" required>
                            <option value="">Select Subject</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id', $question->topic->unit->subject_id) == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->subject_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('subject_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="unit_id" class="form-label">Unit <span class="text-danger">*</span></label>
                        <select class="form-select @error('unit_id') is-invalid @enderror" id="unit_id" name="unit_id" required>
                            <option value="">Select Unit</option>
                            @foreach($units->where('subject_id', old('subject_id', $question->topic->unit->subject_id)) as $unit)
                                <option value="{{ $unit->id }}" {{ old('unit_id', $question->topic->unit_id) == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->unit_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('unit_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="topic_id" class="form-label">Topic <span class="text-danger">*</span></label>
                        <select class="form-select @error('topic_id') is-invalid @enderror" id="topic_id" name="topic_id" required>
                            <option value="">Select Topic</option>
                            @foreach($topics->where('unit_id', old('unit_id', $question->topic->unit_id)) as $topic)
                                <option value="{{ $topic->id }}" {{ old('topic_id', $question->topic_id) == $topic->id ? 'selected' : '' }}>
                                    {{ $topic->topic_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('topic_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="question_type" class="form-label">Question Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('question_type') is-invalid @enderror" id="question_type" name="question_type" required>
                            <option value="">Select Question Type</option>
                            <option value="mcq" {{ old('question_type', $question->question_type) == 'mcq' ? 'selected' : '' }}>Multiple Choice</option>
                            <option value="true_false" {{ old('question_type', $question->question_type) == 'true_false' ? 'selected' : '' }}>True/False</option>
                            <option value="short_answer" {{ old('question_type', $question->question_type) == 'short_answer' ? 'selected' : '' }}>Short Answer</option>
                            <option value="long_answer" {{ old('question_type', $question->question_type) == 'long_answer' ? 'selected' : '' }}>Long Answer</option>
                            <option value="fill_in_the_blank" {{ old('question_type', $question->question_type) == 'fill_in_the_blank' ? 'selected' : '' }}>Fill in the Blank</option>
                            <option value="matching" {{ old('question_type', $question->question_type) == 'matching' ? 'selected' : '' }}>Matching</option>
                        </select>
                        @error('question_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="difficulty_level" class="form-label">Difficulty Level <span class="text-danger">*</span></label>
                        <select class="form-select @error('difficulty_level') is-invalid @enderror" id="difficulty_level" name="difficulty_level" required>
                            <option value="">Select Difficulty</option>
                            <option value="easy" {{ old('difficulty_level', $question->difficulty_level) == 'easy' ? 'selected' : '' }}>Easy</option>
                            <option value="medium" {{ old('difficulty_level', $question->difficulty_level) == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="hard" {{ old('difficulty_level', $question->difficulty_level) == 'hard' ? 'selected' : '' }}>Hard</option>
                        </select>
                        @error('difficulty_level')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="blooms_taxonomy_id" class="form-label">Bloom's Taxonomy Level <span class="text-danger">*</span></label>
                        <select class="form-select @error('blooms_taxonomy_id') is-invalid @enderror" id="blooms_taxonomy_id" name="blooms_taxonomy_id" required>
                            <option value="">Select Bloom's Level</option>
                            @foreach($bloomsTaxonomies as $taxonomy)
                                <option value="{{ $taxonomy->id }}" {{ old('blooms_taxonomy_id', $question->blooms_taxonomy_id) == $taxonomy->id ? 'selected' : '' }}>
                                    {{ $taxonomy->level_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('blooms_taxonomy_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="question_text" class="form-label">Question Text <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('question_text') is-invalid @enderror" id="question_text" name="question_text" rows="4" required>{{ old('question_text', $question->question_text) }}</textarea>
                    @error('question_text')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="marks" class="form-label">Marks <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('marks') is-invalid @enderror" id="marks" name="marks" value="{{ old('marks', $question->marks) }}" min="1" required>
                    @error('marks')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Dynamic fields based on question type -->
                <div id="mcq_options" class="question-type-fields" style="{{ old('question_type', $question->question_type) == 'mcq' ? '' : 'display: none;' }}">
                    <h5 class="mt-4 mb-3">Multiple Choice Options</h5>
                    <div class="mb-3">
                        <label class="form-label">Options <span class="text-danger">*</span></label>
                        <div id="options_container">
                            @if(old('options'))
                                @foreach(old('options') as $index => $option)
                                    <div class="input-group mb-2">
                                        <div class="input-group-text">
                                            <input class="form-check-input mt-0" type="radio" name="correct_option" value="{{ $index }}" {{ old('correct_option') == $index ? 'checked' : '' }}>
                                        </div>
                                        <input type="text" class="form-control @error('options.'.$index) is-invalid @enderror" name="options[]" value="{{ $option }}" placeholder="Option {{ $index + 1 }}">
                                        <button type="button" class="btn btn-danger remove-option">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        @error('options.'.$index)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endforeach
                            @elseif($question->question_type == 'mcq' && $question->options)
                                @foreach(json_decode($question->options) as $index => $option)
                                    <div class="input-group mb-2">
                                        <div class="input-group-text">
                                            <input class="form-check-input mt-0" type="radio" name="correct_option" value="{{ $index }}" {{ $question->correct_answer == $index ? 'checked' : '' }}>
                                        </div>
                                        <input type="text" class="form-control" name="options[]" value="{{ $option }}" placeholder="Option {{ $index + 1 }}">
                                        <button type="button" class="btn btn-danger remove-option">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endforeach
                            @else
                                @for($i = 0; $i < 4; $i++)
                                    <div class="input-group mb-2">
                                        <div class="input-group-text">
                                            <input class="form-check-input mt-0" type="radio" name="correct_option" value="{{ $i }}">
                                        </div>
                                        <input type="text" class="form-control" name="options[]" placeholder="Option {{ $i + 1 }}">
                                        <button type="button" class="btn btn-danger remove-option">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endfor
                            @endif
                        </div>
                        <button type="button" id="add_option" class="btn btn-sm btn-secondary mt-2">
                            <i class="fas fa-plus me-1"></i>Add Option
                        </button>
                        @error('options')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                        @error('correct_option')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div id="true_false_options" class="question-type-fields" style="{{ old('question_type', $question->question_type) == 'true_false' ? '' : 'display: none;' }}">
                    <h5 class="mt-4 mb-3">True/False Answer</h5>
                    <div class="mb-3">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="true_false_answer" id="true_answer" value="true" 
                                {{ old('true_false_answer', $question->question_type == 'true_false' ? $question->correct_answer : '') == 'true' ? 'checked' : '' }}>
                            <label class="form-check-label" for="true_answer">True</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="true_false_answer" id="false_answer" value="false" 
                                {{ old('true_false_answer', $question->question_type == 'true_false' ? $question->correct_answer : '') == 'false' ? 'checked' : '' }}>
                            <label class="form-check-label" for="false_answer">False</label>
                        </div>
                        @error('true_false_answer')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div id="short_answer_options" class="question-type-fields" style="{{ old('question_type', $question->question_type) == 'short_answer' ? '' : 'display: none;' }}">
                    <h5 class="mt-4 mb-3">Short Answer</h5>
                    <div class="mb-3">
                        <label for="short_answer" class="form-label">Model Answer <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('short_answer') is-invalid @enderror" id="short_answer" name="short_answer" rows="2">{{ old('short_answer', $question->question_type == 'short_answer' ? $question->correct_answer : '') }}</textarea>
                        @error('short_answer')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div id="long_answer_options" class="question-type-fields" style="{{ old('question_type', $question->question_type) == 'long_answer' ? '' : 'display: none;' }}">
                    <h5 class="mt-4 mb-3">Long Answer</h5>
                    <div class="mb-3">
                        <label for="long_answer" class="form-label">Model Answer</label>
                        <textarea class="form-control @error('long_answer') is-invalid @enderror" id="long_answer" name="long_answer" rows="4">{{ old('long_answer', $question->question_type == 'long_answer' ? $question->correct_answer : '') }}</textarea>
                        @error('long_answer')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="marking_scheme" class="form-label">Marking Scheme</label>
                        <textarea class="form-control @error('marking_scheme') is-invalid @enderror" id="marking_scheme" name="marking_scheme" rows="3" placeholder="Describe how marks should be allocated">{{ old('marking_scheme', $question->marking_scheme) }}</textarea>
                        @error('marking_scheme')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div id="fill_in_the_blank_options" class="question-type-fields" style="{{ old('question_type', $question->question_type) == 'fill_in_the_blank' ? '' : 'display: none;' }}">
                    <h5 class="mt-4 mb-3">Fill in the Blank</h5>
                    <div class="mb-3">
                        <label for="blank_answer" class="form-label">Correct Answer <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('blank_answer') is-invalid @enderror" id="blank_answer" name="blank_answer" value="{{ old('blank_answer', $question->question_type == 'fill_in_the_blank' ? $question->correct_answer : '') }}">
                        <div class="form-text">Enter the word or phrase that should fill the blank.</div>
                        @error('blank_answer')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div id="matching_options" class="question-type-fields" style="{{ old('question_type', $question->question_type) == 'matching' ? '' : 'display: none;' }}">
                    <h5 class="mt-4 mb-3">Matching Items</h5>
                    <div class="mb-3">
                        <div id="matching_container">
                            @if(old('matching_left') && old('matching_right'))
                                @foreach(old('matching_left') as $index => $left)
                                    <div class="row mb-2 matching-pair">
                                        <div class="col-md-5">
                                            <input type="text" class="form-control @error('matching_left.'.$index) is-invalid @enderror" name="matching_left[]" value="{{ $left }}" placeholder="Left item">
                                            @error('matching_left.'.$index)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-5">
                                            <input type="text" class="form-control @error('matching_right.'.$index) is-invalid @enderror" name="matching_right[]" value="{{ old('matching_right')[$index] ?? '' }}" placeholder="Right item">
                                            @error('matching_right.'.$index)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger remove-matching">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            @elseif($question->question_type == 'matching' && $question->options)
                                @php
                                    $matchingItems = json_decode($question->options, true);
                                    $matchingLeft = $matchingItems['left'] ?? [];
                                    $matchingRight = $matchingItems['right'] ?? [];
                                @endphp
                                @foreach($matchingLeft as $index => $left)
                                    <div class="row mb-2 matching-pair">
                                        <div class="col-md-5">
                                            <input type="text" class="form-control" name="matching_left[]" value="{{ $left }}" placeholder="Left item">
                                        </div>
                                        <div class="col-md-5">
                                            <input type="text" class="form-control" name="matching_right[]" value="{{ $matchingRight[$index] ?? '' }}" placeholder="Right item">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger remove-matching">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                @for($i = 0; $i < 4; $i++)
                                    <div class="row mb-2 matching-pair">
                                        <div class="col-md-5">
                                            <input type="text" class="form-control" name="matching_left[]" placeholder="Left item">
                                        </div>
                                        <div class="col-md-5">
                                            <input type="text" class="form-control" name="matching_right[]" placeholder="Right item">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger remove-matching">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endfor
                            @endif
                        </div>
                        <button type="button" id="add_matching" class="btn btn-sm btn-secondary mt-2">
                            <i class="fas fa-plus me-1"></i>Add Matching Pair
                        </button>
                        @error('matching_left')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                        @error('matching_right')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="explanation" class="form-label">Explanation</label>
                    <textarea class="form-control @error('explanation') is-invalid @enderror" id="explanation" name="explanation" rows="3" placeholder="Explain the answer or provide additional context">{{ old('explanation', $question->explanation) }}</textarea>
                    @error('explanation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="reset" class="btn btn-secondary me-md-2">Reset</button>
                    <button type="submit" class="btn btn-primary">Update Question</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Subject, Unit, Topic dropdowns
        const subjectSelect = document.getElementById('subject_id');
        const unitSelect = document.getElementById('unit_id');
        const topicSelect = document.getElementById('topic_id');
        
        // Question type fields
        const questionTypeSelect = document.getElementById('question_type');
        const questionTypeFields = document.querySelectorAll('.question-type-fields');
        
        // Subject change event
        subjectSelect.addEventListener('change', function() {
            const subjectId = this.value;
            
            // Clear unit and topic selects
            unitSelect.innerHTML = '<option value="">Select Unit</option>';
            topicSelect.innerHTML = '<option value="">Select Topic</option>';
            
            if (subjectId) {
                // Fetch units for the selected subject
                fetch(`/api/subjects/${subjectId}/units`)
                    .then(response => response.json())
                    .then(units => {
                        units.forEach(unit => {
                            const option = document.createElement('option');
                            option.value = unit.id;
                            option.textContent = unit.unit_name;
                            unitSelect.appendChild(option);
                        });
                    });
            }
        });
        
        // Unit change event
        unitSelect.addEventListener('change', function() {
            const unitId = this.value;
            
            // Clear topic select
            topicSelect.innerHTML = '<option value="">Select Topic</option>';
            
            if (unitId) {
                // Fetch topics for the selected unit
                fetch(`/api/units/${unitId}/topics`)
                    .then(response => response.json())
                    .then(topics => {
                        topics.forEach(topic => {
                            const option = document.createElement('option');
                            option.value = topic.id;
                            option.textContent = topic.topic_name;
                            topicSelect.appendChild(option);
                        });
                    });
            }
        });
        
        // Question type change event
        questionTypeSelect.addEventListener('change', function() {
            const questionType = this.value;
            
            // Hide all question type fields
            questionTypeFields.forEach(field => {
                field.style.display = 'none';
            });
            
            // Show the selected question type fields
            if (questionType) {
                document.getElementById(`${questionType}_options`).style.display = 'block';
            }
        });
        
        // MCQ options
        const addOptionBtn = document.getElementById('add_option');
        const optionsContainer = document.getElementById('options_container');
        
        addOptionBtn.addEventListener('click', function() {
            const optionCount = optionsContainer.children.length;
            
            const optionGroup = document.createElement('div');
            optionGroup.className = 'input-group mb-2';
            
            optionGroup.innerHTML = `
                <div class="input-group-text">
                    <input class="form-check-input mt-0" type="radio" name="correct_option" value="${optionCount}">
                </div>
                <input type="text" class="form-control" name="options[]" placeholder="Option ${optionCount + 1}">
                <button type="button" class="btn btn-danger remove-option">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            optionsContainer.appendChild(optionGroup);
        });
        
        // Remove MCQ option
        optionsContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-option') || e.target.parentElement.classList.contains('remove-option')) {
                const button = e.target.closest('.remove-option');
                const optionGroup = button.closest('.input-group');
                
                if (optionsContainer.children.length > 2) {
                    optionGroup.remove();
                    
                    // Update the value attributes of the radio buttons
                    const radioButtons = optionsContainer.querySelectorAll('input[type="radio"]');
                    radioButtons.forEach((radio, index) => {
                        radio.value = index;
                    });
                } else {
                    alert('You must have at least 2 options.');
                }
            }
        });
        
        // Matching items
        const addMatchingBtn = document.getElementById('add_matching');
        const matchingContainer = document.getElementById('matching_container');
        
        addMatchingBtn.addEventListener('click', function() {
            const matchingPair = document.createElement('div');
            matchingPair.className = 'row mb-2 matching-pair';
            
            matchingPair.innerHTML = `
                <div class="col-md-5">
                    <input type="text" class="form-control" name="matching_left[]" placeholder="Left item">
                </div>
                <div class="col-md-5">
                    <input type="text" class="form-control" name="matching_right[]" placeholder="Right item">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger remove-matching">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            matchingContainer.appendChild(matchingPair);
        });
        
        // Remove matching pair
        matchingContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-matching') || e.target.parentElement.classList.contains('remove-matching')) {
                const button = e.target.closest('.remove-matching');
                const matchingPair = button.closest('.matching-pair');
                
                if (matchingContainer.children.length > 2) {
                    matchingPair.remove();
                } else {
                    alert('You must have at least 2 matching pairs.');
                }
            }
        });
    });
</script>
@endpush
@endsection

