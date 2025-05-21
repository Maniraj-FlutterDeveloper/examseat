@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Edit Question</h1>
        <a href="{{ route('admin.questions.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Questions
        </a>
    </div>
    
    <form action="{{ route('admin.questions.update', $question->id) }}" method="POST" id="questionForm">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Question Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
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
                        
                        <div class="mb-3">
                            <label for="question_text" class="form-label">Question Text <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('question_text') is-invalid @enderror" id="question_text" name="question_text" rows="5" required>{{ old('question_text', $question->question_text) }}</textarea>
                            @error('question_text')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Enter the question text. You can use HTML formatting.</small>
                        </div>
                        
                        <!-- Multiple Choice Options -->
                        <div id="mcq_options" class="question-options" style="display: none;">
                            <h6 class="mt-4 mb-3">Options</h6>
                            <div class="options-container">
                                @php
                                    $options = old('options', $question->question_type == 'mcq' ? json_decode($question->options) : ['', '', '', '']);
                                    $correctAnswer = old('correct_answer', $question->question_type == 'mcq' ? $question->correct_answer : 0);
                                @endphp
                                
                                @foreach($options as $i => $option)
                                    <div class="mb-3 row align-items-center">
                                        <div class="col-auto">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="correct_answer" value="{{ $i }}" id="option_correct_{{ $i }}" {{ $correctAnswer == $i ? 'checked' : '' }}>
                                                <label class="form-check-label" for="option_correct_{{ $i }}">
                                                    Correct
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <input type="text" class="form-control" name="options[]" placeholder="Option {{ $i + 1 }}" value="{{ $option }}">
                                        </div>
                                        @if($i > 1)
                                            <div class="col-auto">
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-option">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary add-option">
                                <i class="fas fa-plus me-1"></i>Add Option
                            </button>
                        </div>
                        
                        <!-- True/False Options -->
                        <div id="true_false_options" class="question-options" style="display: none;">
                            <h6 class="mt-4 mb-3">Answer</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="true_false_answer" value="1" id="true_option" {{ old('true_false_answer', $question->question_type == 'true_false' && $question->correct_answer ? '1' : '') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="true_option">
                                    True
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="true_false_answer" value="0" id="false_option" {{ old('true_false_answer', $question->question_type == 'true_false' && !$question->correct_answer ? '0' : '') == '0' ? 'checked' : '' }}>
                                <label class="form-check-label" for="false_option">
                                    False
                                </label>
                            </div>
                        </div>
                        
                        <!-- Fill in the Blank Options -->
                        <div id="fill_in_the_blank_options" class="question-options" style="display: none;">
                            <h6 class="mt-4 mb-3">Correct Answers</h6>
                            <p class="text-muted">Enter all possible correct answers, one per line.</p>
                            <div class="blanks-container">
                                @php
                                    $blankAnswers = old('blank_answers', $question->question_type == 'fill_in_the_blank' ? json_decode($question->correct_answer) : ['', '', '']);
                                @endphp
                                
                                @foreach($blankAnswers as $i => $answer)
                                    <div class="mb-3 row align-items-center">
                                        <div class="col">
                                            <input type="text" class="form-control" name="blank_answers[]" placeholder="Correct Answer {{ $i + 1 }}" value="{{ $answer }}">
                                        </div>
                                        @if($i > 0)
                                            <div class="col-auto">
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-blank">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary add-blank">
                                <i class="fas fa-plus me-1"></i>Add Another Answer
                            </button>
                        </div>
                        
                        <!-- Matching Options -->
                        <div id="matching_options" class="question-options" style="display: none;">
                            <h6 class="mt-4 mb-3">Matching Pairs</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-muted">Column A</h6>
                                    <div class="column-a-container">
                                        @php
                                            $columnA = old('column_a', $question->question_type == 'matching' ? json_decode($question->options)->column_a : ['', '', '', '']);
                                        @endphp
                                        
                                        @foreach($columnA as $i => $item)
                                            <div class="mb-3 row align-items-center">
                                                <div class="col">
                                                    <input type="text" class="form-control" name="column_a[]" placeholder="Item {{ $i + 1 }}" value="{{ $item }}">
                                                </div>
                                                @if($i > 1)
                                                    <div class="col-auto">
                                                        <button type="button" class="btn btn-sm btn-outline-danger remove-pair">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted">Column B</h6>
                                    <div class="column-b-container">
                                        @php
                                            $columnB = old('column_b', $question->question_type == 'matching' ? json_decode($question->options)->column_b : ['', '', '', '']);
                                            $matchingAnswers = old('matching_answers', $question->question_type == 'matching' ? json_decode($question->correct_answer) : [0, 1, 2, 3]);
                                        @endphp
                                        
                                        @foreach($columnB as $i => $item)
                                            <div class="mb-3">
                                                <input type="text" class="form-control" name="column_b[]" placeholder="Match {{ $i + 1 }}" value="{{ $item }}">
                                                <input type="hidden" name="matching_answers[]" value="{{ $matchingAnswers[$i] ?? $i }}">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary add-pair">
                                <i class="fas fa-plus me-1"></i>Add Pair
                            </button>
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle me-2"></i>
                                The items in Column A will be matched with the corresponding items in Column B. You can drag and drop items in Column B to change the matching order.
                            </div>
                        </div>
                        
                        <!-- Short Answer / Long Answer Options -->
                        <div id="text_answer_options" class="question-options" style="display: none;">
                            <h6 class="mt-4 mb-3">Model Answer</h6>
                            <textarea class="form-control" id="model_answer" name="model_answer" rows="4">{{ old('model_answer', $question->question_type == 'short_answer' || $question->question_type == 'long_answer' ? $question->correct_answer : '') }}</textarea>
                            <small class="text-muted">Provide a model answer for reference. This will not be shown to students.</small>
                        </div>
                        
                        <div class="mb-3 mt-4">
                            <label for="explanation" class="form-label">Explanation</label>
                            <textarea class="form-control @error('explanation') is-invalid @enderror" id="explanation" name="explanation" rows="3">{{ old('explanation', $question->explanation) }}</textarea>
                            @error('explanation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Provide an explanation for the correct answer (optional).</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Question Classification</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-12 mb-3">
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
                            
                            <div class="col-12 mb-3">
                                <label for="unit_id" class="form-label">Unit <span class="text-danger">*</span></label>
                                <select class="form-select @error('unit_id') is-invalid @enderror" id="unit_id" name="unit_id" required>
                                    <option value="">Select Unit</option>
                                    @foreach($units->where('subject_id', $question->topic->unit->subject_id) as $unit)
                                        <option value="{{ $unit->id }}" {{ old('unit_id', $question->topic->unit_id) == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->unit_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('unit_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="topic_id" class="form-label">Topic <span class="text-danger">*</span></label>
                                <select class="form-select @error('topic_id') is-invalid @enderror" id="topic_id" name="topic_id" required>
                                    <option value="">Select Topic</option>
                                    @foreach($topics->where('unit_id', $question->topic->unit_id) as $topic)
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
                        
                        <div class="mb-3">
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
                        
                        <div class="mb-3">
                            <label for="blooms_taxonomy_id" class="form-label">Bloom's Taxonomy Level</label>
                            <select class="form-select @error('blooms_taxonomy_id') is-invalid @enderror" id="blooms_taxonomy_id" name="blooms_taxonomy_id">
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
                        
                        <div class="mb-3">
                            <label for="marks" class="form-label">Marks <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('marks') is-invalid @enderror" id="marks" name="marks" value="{{ old('marks', $question->marks) }}" min="1" max="100" required>
                            @error('marks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $question->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Question
                            </button>
                            <a href="{{ route('admin.questions.index') }}" class="btn btn-light">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const questionType = document.getElementById('question_type');
        const subjectSelect = document.getElementById('subject_id');
        const unitSelect = document.getElementById('unit_id');
        const topicSelect = document.getElementById('topic_id');
        
        // Initialize rich text editors
        if (typeof ClassicEditor !== 'undefined') {
            ClassicEditor.create(document.getElementById('question_text'))
                .catch(error => {
                    console.error(error);
                });
                
            ClassicEditor.create(document.getElementById('model_answer'))
                .catch(error => {
                    console.error(error);
                });
                
            ClassicEditor.create(document.getElementById('explanation'))
                .catch(error => {
                    console.error(error);
                });
        }
        
        // Show/hide question options based on question type
        questionType.addEventListener('change', function() {
            const type = this.value;
            document.querySelectorAll('.question-options').forEach(el => {
                el.style.display = 'none';
            });
            
            if (type === 'mcq') {
                document.getElementById('mcq_options').style.display = 'block';
            } else if (type === 'true_false') {
                document.getElementById('true_false_options').style.display = 'block';
            } else if (type === 'fill_in_the_blank') {
                document.getElementById('fill_in_the_blank_options').style.display = 'block';
            } else if (type === 'matching') {
                document.getElementById('matching_options').style.display = 'block';
            } else if (type === 'short_answer' || type === 'long_answer') {
                document.getElementById('text_answer_options').style.display = 'block';
            }
        });
        
        // Trigger change event to show the correct options on page load
        if (questionType.value) {
            questionType.dispatchEvent(new Event('change'));
        }
        
        // Subject change event
        subjectSelect.addEventListener('change', function() {
            const subjectId = this.value;
            
            // Clear unit and topic selects
            unitSelect.innerHTML = '<option value="">Select Unit</option>';
            topicSelect.innerHTML = '<option value="">Select Topic</option>';
            
            if (subjectId) {
                // Fetch units for the selected subject
                fetch(`{{ url('admin/units/by-subject') }}/${subjectId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(unit => {
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
                fetch(`{{ url('admin/topics/by-unit') }}/${unitId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(topic => {
                            const option = document.createElement('option');
                            option.value = topic.id;
                            option.textContent = topic.topic_name;
                            topicSelect.appendChild(option);
                        });
                    });
            }
        });
        
        // Add MCQ option
        document.querySelector('.add-option')?.addEventListener('click', function() {
            const optionsContainer = document.querySelector('.options-container');
            const optionCount = optionsContainer.children.length;
            
            const optionRow = document.createElement('div');
            optionRow.className = 'mb-3 row align-items-center';
            optionRow.innerHTML = `
                <div class="col-auto">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="correct_answer" value="${optionCount}" id="option_correct_${optionCount}">
                        <label class="form-check-label" for="option_correct_${optionCount}">
                            Correct
                        </label>
                    </div>
                </div>
                <div class="col">
                    <input type="text" class="form-control" name="options[]" placeholder="Option ${optionCount + 1}">
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-option">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            optionsContainer.appendChild(optionRow);
            
            // Add event listener to the new remove button
            optionRow.querySelector('.remove-option').addEventListener('click', function() {
                optionsContainer.removeChild(optionRow);
                updateOptionValues();
            });
        });
        
        // Remove MCQ option
        document.querySelectorAll('.remove-option').forEach(button => {
            button.addEventListener('click', function() {
                const optionRow = this.closest('.row');
                optionRow.parentNode.removeChild(optionRow);
                updateOptionValues();
            });
        });
        
        // Update option values after removal
        function updateOptionValues() {
            const optionsContainer = document.querySelector('.options-container');
            const options = optionsContainer.querySelectorAll('.row');
            
            options.forEach((option, index) => {
                const radio = option.querySelector('input[type="radio"]');
                const input = option.querySelector('input[type="text"]');
                
                radio.value = index;
                radio.id = `option_correct_${index}`;
                radio.nextElementSibling.setAttribute('for', `option_correct_${index}`);
                input.placeholder = `Option ${index + 1}`;
            });
        }
        
        // Add blank answer
        document.querySelector('.add-blank')?.addEventListener('click', function() {
            const blanksContainer = document.querySelector('.blanks-container');
            const blankCount = blanksContainer.children.length;
            
            const blankRow = document.createElement('div');
            blankRow.className = 'mb-3 row align-items-center';
            blankRow.innerHTML = `
                <div class="col">
                    <input type="text" class="form-control" name="blank_answers[]" placeholder="Correct Answer ${blankCount + 1}">
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-blank">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            blanksContainer.appendChild(blankRow);
            
            // Add event listener to the new remove button
            blankRow.querySelector('.remove-blank').addEventListener('click', function() {
                blanksContainer.removeChild(blankRow);
                updateBlankValues();
            });
        });
        
        // Remove blank answer
        document.querySelectorAll('.remove-blank').forEach(button => {
            button.addEventListener('click', function() {
                const blankRow = this.closest('.row');
                blankRow.parentNode.removeChild(blankRow);
                updateBlankValues();
            });
        });
        
        // Update blank values after removal
        function updateBlankValues() {
            const blanksContainer = document.querySelector('.blanks-container');
            const blanks = blanksContainer.querySelectorAll('.row');
            
            blanks.forEach((blank, index) => {
                const input = blank.querySelector('input[type="text"]');
                input.placeholder = `Correct Answer ${index + 1}`;
            });
        }
        
        // Add matching pair
        document.querySelector('.add-pair')?.addEventListener('click', function() {
            const columnAContainer = document.querySelector('.column-a-container');
            const columnBContainer = document.querySelector('.column-b-container');
            const pairCount = columnAContainer.children.length;
            
            const columnARow = document.createElement('div');
            columnARow.className = 'mb-3 row align-items-center';
            columnARow.innerHTML = `
                <div class="col">
                    <input type="text" class="form-control" name="column_a[]" placeholder="Item ${pairCount + 1}">
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-pair">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            const columnBRow = document.createElement('div');
            columnBRow.className = 'mb-3';
            columnBRow.innerHTML = `
                <input type="text" class="form-control" name="column_b[]" placeholder="Match ${pairCount + 1}">
                <input type="hidden" name="matching_answers[]" value="${pairCount}">
            `;
            
            columnAContainer.appendChild(columnARow);
            columnBContainer.appendChild(columnBRow);
            
            // Add event listener to the new remove button
            columnARow.querySelector('.remove-pair').addEventListener('click', function() {
                const index = Array.from(columnAContainer.children).indexOf(columnARow);
                columnAContainer.removeChild(columnARow);
                columnBContainer.removeChild(columnBContainer.children[index]);
                updatePairValues();
            });
        });
        
        // Remove matching pair
        document.querySelectorAll('.remove-pair').forEach(button => {
            button.addEventListener('click', function() {
                const columnARow = this.closest('.row');
                const columnAContainer = document.querySelector('.column-a-container');
                const columnBContainer = document.querySelector('.column-b-container');
                const index = Array.from(columnAContainer.children).indexOf(columnARow);
                
                columnAContainer.removeChild(columnARow);
                columnBContainer.removeChild(columnBContainer.children[index]);
                updatePairValues();
            });
        });
        
        // Update pair values after removal
        function updatePairValues() {
            const columnAContainer = document.querySelector('.column-a-container');
            const columnBContainer = document.querySelector('.column-b-container');
            
            const columnARows = columnAContainer.querySelectorAll('.row');
            const columnBRows = columnBContainer.querySelectorAll('.mb-3');
            
            columnARows.forEach((row, index) => {
                const input = row.querySelector('input[type="text"]');
                input.placeholder = `Item ${index + 1}`;
            });
            
            columnBRows.forEach((row, index) => {
                const input = row.querySelector('input[type="text"]');
                const hiddenInput = row.querySelector('input[type="hidden"]');
                input.placeholder = `Match ${index + 1}`;
                hiddenInput.value = index;
            });
        }
        
        // Form submission
        document.getElementById('questionForm').addEventListener('submit', function(e) {
            const questionType = document.getElementById('question_type').value;
            
            if (questionType === 'true_false') {
                const trueOption = document.getElementById('true_option');
                const falseOption = document.getElementById('false_option');
                
                if (!trueOption.checked && !falseOption.checked) {
                    e.preventDefault();
                    alert('Please select either True or False as the correct answer.');
                    return;
                }
                
                // Set the correct answer value
                const correctAnswerInput = document.createElement('input');
                correctAnswerInput.type = 'hidden';
                correctAnswerInput.name = 'correct_answer';
                correctAnswerInput.value = trueOption.checked ? '1' : '0';
                this.appendChild(correctAnswerInput);
            }
        });
    });
</script>
@endpush
@endsection

