@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Edit Question Paper</h1>
        <a href="{{ route('question-papers.show', $questionPaper->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Question Paper
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('question-papers.update', $questionPaper->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="title" class="form-label">Question Paper Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $questionPaper->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="subject_id" class="form-label">Subject <span class="text-danger">*</span></label>
                        <select class="form-select @error('subject_id') is-invalid @enderror" id="subject_id" name="subject_id" required>
                            <option value="">Select Subject</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id', $questionPaper->subject_id) == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->subject_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('subject_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="total_marks" class="form-label">Total Marks <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('total_marks') is-invalid @enderror" id="total_marks" name="total_marks" value="{{ old('total_marks', $questionPaper->total_marks) }}" min="1" required>
                        @error('total_marks')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="duration" class="form-label">Duration (minutes) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('duration') is-invalid @enderror" id="duration" name="duration" value="{{ old('duration', $questionPaper->duration) }}" min="1" required>
                        @error('duration')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="passing_percentage" class="form-label">Passing Percentage <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('passing_percentage') is-invalid @enderror" id="passing_percentage" name="passing_percentage" value="{{ old('passing_percentage', $questionPaper->passing_percentage) }}" min="1" max="100" required>
                        @error('passing_percentage')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="instructions" class="form-label">Instructions</label>
                    <textarea class="form-control @error('instructions') is-invalid @enderror" id="instructions" name="instructions" rows="3">{{ old('instructions', $questionPaper->instructions) }}</textarea>
                    @error('instructions')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <hr class="my-4">
                <h5 class="mb-3">Questions</h5>
                
                <div class="alert alert-info mb-4">
                    <div class="d-flex">
                        <i class="fas fa-info-circle fa-2x me-3"></i>
                        <div>
                            <h5 class="alert-heading">Question Management</h5>
                            <p class="mb-0">You can reorder questions by dragging them, change section names, or remove questions from the paper.</p>
                            <p class="mb-0">Current Questions: <strong><span id="question_count">{{ $questionPaper->questions->count() }}</span></strong></p>
                            <p class="mb-0">Current Total Marks: <strong><span id="current_total_marks">{{ $questionPaper->questions->sum('marks') }}</span></strong></p>
                            <div id="marks_warning" class="text-danger mt-2" style="{{ $questionPaper->questions->sum('marks') != $questionPaper->total_marks ? '' : 'display: none;' }}">
                                Total question marks do not match the question paper total marks.
                            </div>
                        </div>
                    </div>
                </div>
                
                <div id="sections_container">
                    @php
                        $sections = $questionPaper->questions->groupBy('section');
                    @endphp
                    
                    @foreach($sections as $sectionName => $sectionQuestions)
                        <div class="card mb-3 section-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-grip-vertical me-2 text-muted handle"></i>
                                    <input type="text" class="form-control form-control-sm section-name" name="sections[{{ $loop->index }}][name]" value="{{ $sectionName ?: 'Section ' . $loop->iteration }}" style="width: 200px;">
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary add-question-btn" data-section-index="{{ $loop->index }}">
                                    <i class="fas fa-plus me-1"></i>Add Question
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="questions-container" data-section-index="{{ $loop->index }}">
                                    @foreach($sectionQuestions as $question)
                                        <div class="card mb-2 question-card" data-question-id="{{ $question->id }}">
                                            <div class="card-body">
                                                <div class="d-flex">
                                                    <i class="fas fa-grip-vertical me-2 text-muted handle"></i>
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex justify-content-between">
                                                            <h6 class="mb-1">{{ $question->question_text }}</h6>
                                                            <div>
                                                                <span class="badge bg-primary">{{ $question->marks }} marks</span>
                                                                <button type="button" class="btn btn-sm btn-danger remove-question-btn ms-2">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <span class="badge bg-secondary">
                                                                {{ ucfirst($question->question_type) }}
                                                            </span>
                                                            
                                                            @if($question->difficulty_level == 'easy')
                                                                <span class="badge bg-success">Easy</span>
                                                            @elseif($question->difficulty_level == 'medium')
                                                                <span class="badge bg-warning">Medium</span>
                                                            @elseif($question->difficulty_level == 'hard')
                                                                <span class="badge bg-danger">Hard</span>
                                                            @endif
                                                            
                                                            <span class="badge bg-info">
                                                                {{ $question->topic->topic_name }}
                                                            </span>
                                                        </div>
                                                        <input type="hidden" name="sections[{{ $loop->parent->index }}][questions][]" value="{{ $question->id }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                @if($sectionQuestions->isEmpty())
                                    <div class="text-center py-3 empty-section-message">
                                        <p class="text-muted">No questions in this section. Click "Add Question" to add questions.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <button type="button" id="add_section" class="btn btn-secondary mb-4">
                    <i class="fas fa-plus-circle me-2"></i>Add Section
                </button>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="reset" class="btn btn-secondary me-md-2">Reset</button>
                    <button type="submit" class="btn btn-primary">Update Question Paper</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Question Modal -->
<div class="modal fade" id="addQuestionModal" tabindex="-1" aria-labelledby="addQuestionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addQuestionModalLabel">Add Questions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="modal_unit_id" class="form-label">Select Unit</label>
                    <select class="form-select" id="modal_unit_id">
                        <option value="">Select Unit</option>
                        <!-- Units will be loaded dynamically -->
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="modal_topic_id" class="form-label">Select Topic</label>
                    <select class="form-select" id="modal_topic_id" disabled>
                        <option value="">Select Topic</option>
                        <!-- Topics will be loaded dynamically -->
                    </select>
                </div>
                
                <div id="questions_container" class="mt-4">
                    <div class="text-center py-3">
                        <p class="text-muted">Select a unit and topic to view available questions.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sectionsContainer = document.getElementById('sections_container');
        const addSectionBtn = document.getElementById('add_section');
        const totalMarksInput = document.getElementById('total_marks');
        const currentTotalMarks = document.getElementById('current_total_marks');
        const questionCount = document.getElementById('question_count');
        const marksWarning = document.getElementById('marks_warning');
        
        let currentSectionIndex = {{ $sections->count() }};
        let currentSection = null;
        
        // Initialize sortable for sections
        new Sortable(sectionsContainer, {
            handle: '.handle',
            animation: 150
        });
        
        // Initialize sortable for questions in each section
        document.querySelectorAll('.questions-container').forEach(container => {
            new Sortable(container, {
                handle: '.handle',
                animation: 150,
                group: 'questions',
                onAdd: function() {
                    updateSectionIndices();
                    updateEmptySectionMessages();
                },
                onRemove: function() {
                    updateEmptySectionMessages();
                }
            });
        });
        
        // Add section
        addSectionBtn.addEventListener('click', function() {
            const sectionCard = document.createElement('div');
            sectionCard.className = 'card mb-3 section-card';
            
            sectionCard.innerHTML = `
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-grip-vertical me-2 text-muted handle"></i>
                        <input type="text" class="form-control form-control-sm section-name" name="sections[${currentSectionIndex}][name]" value="Section ${currentSectionIndex + 1}" style="width: 200px;">
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary add-question-btn" data-section-index="${currentSectionIndex}">
                        <i class="fas fa-plus me-1"></i>Add Question
                    </button>
                </div>
                <div class="card-body">
                    <div class="questions-container" data-section-index="${currentSectionIndex}">
                    </div>
                    <div class="text-center py-3 empty-section-message">
                        <p class="text-muted">No questions in this section. Click "Add Question" to add questions.</p>
                    </div>
                </div>
            `;
            
            sectionsContainer.appendChild(sectionCard);
            
            // Initialize sortable for the new section's questions
            new Sortable(sectionCard.querySelector('.questions-container'), {
                handle: '.handle',
                animation: 150,
                group: 'questions',
                onAdd: function() {
                    updateSectionIndices();
                    updateEmptySectionMessages();
                },
                onRemove: function() {
                    updateEmptySectionMessages();
                }
            });
            
            currentSectionIndex++;
        });
        
        // Remove question
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-question-btn') || e.target.parentElement.classList.contains('remove-question-btn')) {
                const button = e.target.closest('.remove-question-btn');
                const questionCard = button.closest('.question-card');
                
                questionCard.remove();
                updateQuestionCount();
                updateTotalMarks();
                updateEmptySectionMessages();
            }
        });
        
        // Add question button click
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('add-question-btn') || e.target.parentElement.classList.contains('add-question-btn')) {
                const button = e.target.closest('.add-question-btn');
                const sectionIndex = button.getAttribute('data-section-index');
                currentSection = document.querySelector(`.questions-container[data-section-index="${sectionIndex}"]`);
                
                // Load units for the current subject
                const subjectId = document.getElementById('subject_id').value;
                if (subjectId) {
                    fetch(`/api/subjects/${subjectId}/units`)
                        .then(response => response.json())
                        .then(units => {
                            const unitSelect = document.getElementById('modal_unit_id');
                            unitSelect.innerHTML = '<option value="">Select Unit</option>';
                            
                            units.forEach(unit => {
                                const option = document.createElement('option');
                                option.value = unit.id;
                                option.textContent = unit.unit_name;
                                unitSelect.appendChild(option);
                            });
                            
                            // Show the modal
                            const modal = new bootstrap.Modal(document.getElementById('addQuestionModal'));
                            modal.show();
                        });
                } else {
                    alert('Please select a subject first.');
                }
            }
        });
        
        // Unit select change
        document.getElementById('modal_unit_id').addEventListener('change', function() {
            const unitId = this.value;
            const topicSelect = document.getElementById('modal_topic_id');
            
            topicSelect.innerHTML = '<option value="">Select Topic</option>';
            topicSelect.disabled = !unitId;
            
            document.getElementById('questions_container').innerHTML = `
                <div class="text-center py-3">
                    <p class="text-muted">Select a topic to view available questions.</p>
                </div>
            `;
            
            if (unitId) {
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
        
        // Topic select change
        document.getElementById('modal_topic_id').addEventListener('change', function() {
            const topicId = this.value;
            const questionsContainer = document.getElementById('questions_container');
            
            if (topicId) {
                questionsContainer.innerHTML = `
                    <div class="text-center py-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p>Loading questions...</p>
                    </div>
                `;
                
                fetch(`/api/topics/${topicId}/questions`)
                    .then(response => response.json())
                    .then(questions => {
                        if (questions.length > 0) {
                            questionsContainer.innerHTML = `
                                <div class="list-group">
                                    ${questions.map(question => `
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6>${question.question_text}</h6>
                                                    <div>
                                                        <span class="badge bg-secondary">
                                                            ${getQuestionTypeLabel(question.question_type)}
                                                        </span>
                                                        <span class="badge ${getDifficultyBadgeClass(question.difficulty_level)}">
                                                            ${question.difficulty_level.charAt(0).toUpperCase() + question.difficulty_level.slice(1)}
                                                        </span>
                                                        <span class="badge bg-primary">${question.marks} marks</span>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-primary add-question-to-section" data-question-id="${question.id}" data-question-text="${question.question_text}" data-question-type="${question.question_type}" data-question-marks="${question.marks}" data-question-difficulty="${question.difficulty_level}" data-topic-id="${question.topic_id}" data-topic-name="${question.topic.topic_name}">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                            `;
                            
                            // Add event listeners to add question buttons
                            questionsContainer.querySelectorAll('.add-question-to-section').forEach(button => {
                                button.addEventListener('click', function() {
                                    const questionId = this.getAttribute('data-question-id');
                                    const questionText = this.getAttribute('data-question-text');
                                    const questionType = this.getAttribute('data-question-type');
                                    const questionMarks = this.getAttribute('data-question-marks');
                                    const questionDifficulty = this.getAttribute('data-question-difficulty');
                                    const topicId = this.getAttribute('data-topic-id');
                                    const topicName = this.getAttribute('data-topic-name');
                                    
                                    addQuestionToSection(questionId, questionText, questionType, questionMarks, questionDifficulty, topicId, topicName);
                                    
                                    // Disable the button
                                    this.disabled = true;
                                    this.innerHTML = '<i class="fas fa-check"></i>';
                                });
                            });
                        } else {
                            questionsContainer.innerHTML = `
                                <div class="alert alert-warning">
                                    No questions found for this topic.
                                </div>
                            `;
                        }
                    });
            }
        });
        
        // Add question to section
        function addQuestionToSection(id, text, type, marks, difficulty, topicId, topicName) {
            // Check if question is already in the paper
            const existingQuestion = document.querySelector(`[data-question-id="${id}"]`);
            if (existingQuestion) {
                alert('This question is already in the paper.');
                return;
            }
            
            const questionCard = document.createElement('div');
            questionCard.className = 'card mb-2 question-card';
            questionCard.setAttribute('data-question-id', id);
            
            questionCard.innerHTML = `
                <div class="card-body">
                    <div class="d-flex">
                        <i class="fas fa-grip-vertical me-2 text-muted handle"></i>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-1">${text}</h6>
                                <div>
                                    <span class="badge bg-primary">${marks} marks</span>
                                    <button type="button" class="btn btn-sm btn-danger remove-question-btn ms-2">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <span class="badge bg-secondary">
                                    ${getQuestionTypeLabel(type)}
                                </span>
                                <span class="badge ${getDifficultyBadgeClass(difficulty)}">
                                    ${difficulty.charAt(0).toUpperCase() + difficulty.slice(1)}
                                </span>
                                <span class="badge bg-info">
                                    ${topicName}
                                </span>
                            </div>
                            <input type="hidden" name="sections[${currentSection.getAttribute('data-section-index')}][questions][]" value="${id}">
                        </div>
                    </div>
                </div>
            `;
            
            currentSection.appendChild(questionCard);
            updateQuestionCount();
            updateTotalMarks();
            updateEmptySectionMessages();
        }
        
        // Update section indices
        function updateSectionIndices() {
            const sections = document.querySelectorAll('.section-card');
            sections.forEach((section, index) => {
                const sectionNameInput = section.querySelector('.section-name');
                sectionNameInput.name = `sections[${index}][name]`;
                
                const questionsContainer = section.querySelector('.questions-container');
                questionsContainer.setAttribute('data-section-index', index);
                
                const questionInputs = questionsContainer.querySelectorAll('input[type="hidden"]');
                questionInputs.forEach(input => {
                    input.name = `sections[${index}][questions][]`;
                });
                
                const addQuestionBtn = section.querySelector('.add-question-btn');
                addQuestionBtn.setAttribute('data-section-index', index);
            });
        }
        
        // Update empty section messages
        function updateEmptySectionMessages() {
            document.querySelectorAll('.questions-container').forEach(container => {
                const emptyMessage = container.parentElement.querySelector('.empty-section-message');
                if (emptyMessage) {
                    if (container.children.length > 0) {
                        emptyMessage.style.display = 'none';
                    } else {
                        emptyMessage.style.display = 'block';
                    }
                }
            });
        }
        
        // Update question count
        function updateQuestionCount() {
            const count = document.querySelectorAll('.question-card').length;
            questionCount.textContent = count;
        }
        
        // Update total marks
        function updateTotalMarks() {
            let total = 0;
            document.querySelectorAll('.question-card').forEach(card => {
                const marksText = card.querySelector('.badge.bg-primary').textContent;
                const marks = parseInt(marksText);
                if (!isNaN(marks)) {
                    total += marks;
                }
            });
            
            currentTotalMarks.textContent = total;
            
            // Check if total matches question paper total marks
            const paperTotal = parseInt(totalMarksInput.value) || 0;
            if (total !== paperTotal && total > 0) {
                marksWarning.style.display = 'block';
            } else {
                marksWarning.style.display = 'none';
            }
        }
        
        // Listen for changes in total marks
        totalMarksInput.addEventListener('input', updateTotalMarks);
        
        // Helper functions
        function getQuestionTypeLabel(type) {
            switch (type) {
                case 'mcq': return 'Multiple Choice';
                case 'true_false': return 'True/False';
                case 'short_answer': return 'Short Answer';
                case 'long_answer': return 'Long Answer';
                case 'fill_in_the_blank': return 'Fill in the Blank';
                case 'matching': return 'Matching';
                default: return type;
            }
        }
        
        function getDifficultyBadgeClass(difficulty) {
            switch (difficulty) {
                case 'easy': return 'bg-success';
                case 'medium': return 'bg-warning';
                case 'hard': return 'bg-danger';
                default: return 'bg-secondary';
            }
        }
        
        // Initial setup
        updateEmptySectionMessages();
    });
</script>
@endpush
@endsection
                const addQuestionBtn = section.querySelector('.add-question-btn');
                addQuestionBtn.setAttribute('data-section-index', index);
            });
        }
        
        // Update empty section messages
        function updateEmptySectionMessages() {
            document.querySelectorAll('.questions-container').forEach(container => {
                const emptyMessage = container.parentElement.querySelector('.empty-section-message');
                if (emptyMessage) {
                    if (container.children.length > 0) {
                        emptyMessage.style.display = 'none';
                    } else {
                        emptyMessage.style.display = 'block';
                    }
                }
            });
        }
        
        // Update question count
        function updateQuestionCount() {
            const count = document.querySelectorAll('.question-card').length;
            questionCount.textContent = count;
        }
        
        // Update total marks
        function updateTotalMarks() {
            let total = 0;
            document.querySelectorAll('.question-card').forEach(card => {
                const marksText = card.querySelector('.badge.bg-primary').textContent;
                const marks = parseInt(marksText);
                if (!isNaN(marks)) {
                    total += marks;
                }
            });
            
            currentTotalMarks.textContent = total;
            
            // Check if total matches question paper total marks
            const paperTotal = parseInt(totalMarksInput.value) || 0;
            if (total !== paperTotal && total > 0) {
                marksWarning.style.display = 'block';
            } else {
                marksWarning.style.display = 'none';
            }
        }
        
        // Listen for changes in total marks
        totalMarksInput.addEventListener('input', updateTotalMarks);
        
        // Helper functions
        function getQuestionTypeLabel(type) {
            switch (type) {
                case 'mcq': return 'Multiple Choice';
                case 'true_false': return 'True/False';
                case 'short_answer': return 'Short Answer';
                case 'long_answer': return 'Long Answer';
                case 'fill_in_the_blank': return 'Fill in the Blank';
                case 'matching': return 'Matching';
                default: return type;
            }
        }
        
        function getDifficultyBadgeClass(difficulty) {
            switch (difficulty) {
                case 'easy': return 'bg-success';
                case 'medium': return 'bg-warning';
                case 'hard': return 'bg-danger';
                default: return 'bg-secondary';
            }
        }
        
        // Initial setup
        updateEmptySectionMessages();
    });
</script>
@endpush
@endsection
