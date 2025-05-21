@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Generate Question Paper</h1>
        <a href="{{ route('question-papers.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Question Papers
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('question-papers.store') }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="title" class="form-label">Question Paper Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="generation_method" class="form-label">Generation Method <span class="text-danger">*</span></label>
                        <select class="form-select @error('generation_method') is-invalid @enderror" id="generation_method" name="generation_method" required>
                            <option value="">Select Method</option>
                            <option value="blueprint" {{ old('generation_method') == 'blueprint' ? 'selected' : '' }}>From Blueprint</option>
                            <option value="custom" {{ old('generation_method') == 'custom' ? 'selected' : '' }}>Custom Selection</option>
                            <option value="random" {{ old('generation_method') == 'random' ? 'selected' : '' }}>Random Generation</option>
                        </select>
                        @error('generation_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Blueprint Selection (shown when generation_method is 'blueprint') -->
                <div id="blueprint_section" class="mb-4" style="display: none;">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Blueprint Selection</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="subject_id" class="form-label">Subject <span class="text-danger">*</span></label>
                                    <select class="form-select @error('subject_id') is-invalid @enderror" id="subject_id" name="subject_id">
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
                                
                                <div class="col-md-6">
                                    <label for="blueprint_id" class="form-label">Blueprint <span class="text-danger">*</span></label>
                                    <select class="form-select @error('blueprint_id') is-invalid @enderror" id="blueprint_id" name="blueprint_id">
                                        <option value="">Select Blueprint</option>
                                        @if(old('subject_id'))
                                            @foreach($blueprints->where('subject_id', old('subject_id')) as $blueprint)
                                                <option value="{{ $blueprint->id }}" {{ old('blueprint_id') == $blueprint->id ? 'selected' : '' }}>
                                                    {{ $blueprint->title }} ({{ $blueprint->total_marks }} marks)
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('blueprint_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div id="blueprint_details" class="mt-3" style="display: none;">
                                <div class="alert alert-info">
                                    <div class="d-flex">
                                        <i class="fas fa-info-circle fa-2x me-3"></i>
                                        <div>
                                            <h5 class="alert-heading">Blueprint Information</h5>
                                            <p class="mb-0">Total Marks: <span id="blueprint_marks">0</span></p>
                                            <p class="mb-0">Duration: <span id="blueprint_duration">0</span> minutes</p>
                                            <p class="mb-0">Sections: <span id="blueprint_sections">0</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Custom Selection (shown when generation_method is 'custom') -->
                <div id="custom_section" class="mb-4" style="display: none;">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Custom Question Selection</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="custom_subject_id" class="form-label">Subject <span class="text-danger">*</span></label>
                                    <select class="form-select @error('custom_subject_id') is-invalid @enderror" id="custom_subject_id" name="custom_subject_id">
                                        <option value="">Select Subject</option>
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}" {{ old('custom_subject_id') == $subject->id ? 'selected' : '' }}>
                                                {{ $subject->subject_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('custom_subject_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="total_marks" class="form-label">Total Marks <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('total_marks') is-invalid @enderror" id="total_marks" name="total_marks" value="{{ old('total_marks') }}" min="1">
                                    @error('total_marks')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="duration" class="form-label">Duration (minutes) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('duration') is-invalid @enderror" id="duration" name="duration" value="{{ old('duration') }}" min="1">
                                    @error('duration')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="passing_percentage" class="form-label">Passing Percentage <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('passing_percentage') is-invalid @enderror" id="passing_percentage" name="passing_percentage" value="{{ old('passing_percentage', 40) }}" min="1" max="100">
                                    @error('passing_percentage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div id="units_container" class="mt-4">
                                <!-- Units will be loaded here dynamically -->
                            </div>
                            
                            <div id="selected_questions_container" class="mt-4">
                                <h6>Selected Questions: <span id="selected_count">0</span></h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="selected_questions_table">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Question</th>
                                                <th>Type</th>
                                                <th>Marks</th>
                                                <th>Difficulty</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Selected questions will be added here -->
                                        </tbody>
                                        <tfoot>
                                            <tr class="table-secondary">
                                                <td colspan="2" class="text-end fw-bold">Total Marks:</td>
                                                <td id="selected_total_marks" class="fw-bold">0</td>
                                                <td colspan="2"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Random Generation (shown when generation_method is 'random') -->
                <div id="random_section" class="mb-4" style="display: none;">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Random Question Generation</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="random_subject_id" class="form-label">Subject <span class="text-danger">*</span></label>
                                    <select class="form-select @error('random_subject_id') is-invalid @enderror" id="random_subject_id" name="random_subject_id">
                                        <option value="">Select Subject</option>
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}" {{ old('random_subject_id') == $subject->id ? 'selected' : '' }}>
                                                {{ $subject->subject_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('random_subject_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="random_total_marks" class="form-label">Total Marks <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('random_total_marks') is-invalid @enderror" id="random_total_marks" name="random_total_marks" value="{{ old('random_total_marks') }}" min="1">
                                    @error('random_total_marks')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="random_duration" class="form-label">Duration (minutes) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('random_duration') is-invalid @enderror" id="random_duration" name="random_duration" value="{{ old('random_duration') }}" min="1">
                                    @error('random_duration')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="random_passing_percentage" class="form-label">Passing Percentage <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('random_passing_percentage') is-invalid @enderror" id="random_passing_percentage" name="random_passing_percentage" value="{{ old('random_passing_percentage', 40) }}" min="1" max="100">
                                    @error('random_passing_percentage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <hr>
                            <h6 class="mb-3">Question Distribution</h6>
                            
                            <div id="question_distribution">
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Question Type</label>
                                        <select class="form-select" name="distribution[0][question_type]">
                                            <option value="mcq">Multiple Choice</option>
                                            <option value="true_false">True/False</option>
                                            <option value="short_answer">Short Answer</option>
                                            <option value="long_answer">Long Answer</option>
                                            <option value="fill_in_the_blank">Fill in the Blank</option>
                                            <option value="matching">Matching</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Count</label>
                                        <input type="number" class="form-control" name="distribution[0][count]" min="1" value="5">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Marks Each</label>
                                        <input type="number" class="form-control" name="distribution[0][marks_per_question]" min="1" value="1">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Difficulty</label>
                                        <select class="form-select" name="distribution[0][difficulty_level]">
                                            <option value="easy">Easy</option>
                                            <option value="medium" selected>Medium</option>
                                            <option value="hard">Hard</option>
                                            <option value="mixed">Mixed</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="button" class="btn btn-danger remove-distribution" style="display: none;">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="button" id="add_distribution" class="btn btn-sm btn-secondary mt-2">
                                <i class="fas fa-plus me-1"></i>Add Question Type
                            </button>
                            
                            <div class="alert alert-info mt-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-info-circle fa-2x me-3"></i>
                                    <div>
                                        <strong>Total Marks: <span id="distribution_total_marks">0</span></strong>
                                        <div id="distribution_warning" class="text-danger" style="display: none;">
                                            Total distribution marks do not match the specified total marks.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="instructions" class="form-label">Instructions</label>
                    <textarea class="form-control @error('instructions') is-invalid @enderror" id="instructions" name="instructions" rows="3">{{ old('instructions') }}</textarea>
                    @error('instructions')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="reset" class="btn btn-secondary me-md-2">Reset</button>
                    <button type="submit" class="btn btn-primary">Generate Question Paper</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const generationMethodSelect = document.getElementById('generation_method');
        const blueprintSection = document.getElementById('blueprint_section');
        const customSection = document.getElementById('custom_section');
        const randomSection = document.getElementById('random_section');
        
        // Subject and blueprint selects for blueprint method
        const subjectSelect = document.getElementById('subject_id');
        const blueprintSelect = document.getElementById('blueprint_id');
        const blueprintDetails = document.getElementById('blueprint_details');
        
        // Custom method elements
        const customSubjectSelect = document.getElementById('custom_subject_id');
        const unitsContainer = document.getElementById('units_container');
        
        // Random method elements
        const randomSubjectSelect = document.getElementById('random_subject_id');
        const randomTotalMarksInput = document.getElementById('random_total_marks');
        const distributionTotalMarks = document.getElementById('distribution_total_marks');
        const distributionWarning = document.getElementById('distribution_warning');
        const questionDistribution = document.getElementById('question_distribution');
        const addDistributionBtn = document.getElementById('add_distribution');
        
        // Show/hide sections based on generation method
        generationMethodSelect.addEventListener('change', function() {
            const method = this.value;
            
            blueprintSection.style.display = method === 'blueprint' ? 'block' : 'none';
            customSection.style.display = method === 'custom' ? 'block' : 'none';
            randomSection.style.display = method === 'random' ? 'block' : 'none';
        });
        
        // Initialize based on selected method
        if (generationMethodSelect.value) {
            const method = generationMethodSelect.value;
            blueprintSection.style.display = method === 'blueprint' ? 'block' : 'none';
            customSection.style.display = method === 'custom' ? 'block' : 'none';
            randomSection.style.display = method === 'random' ? 'block' : 'none';
        }
        
        // Blueprint method - load blueprints when subject changes
        subjectSelect.addEventListener('change', function() {
            const subjectId = this.value;
            
            // Clear blueprint select
            blueprintSelect.innerHTML = '<option value="">Select Blueprint</option>';
            blueprintDetails.style.display = 'none';
            
            if (subjectId) {
                // Fetch blueprints for the selected subject
                fetch(`/api/subjects/${subjectId}/blueprints`)
                    .then(response => response.json())
                    .then(blueprints => {
                        blueprints.forEach(blueprint => {
                            const option = document.createElement('option');
                            option.value = blueprint.id;
                            option.textContent = `${blueprint.title} (${blueprint.total_marks} marks)`;
                            blueprintSelect.appendChild(option);
                        });
                    });
            }
        });
        
        // Load blueprint details when blueprint changes
        blueprintSelect.addEventListener('change', function() {
            const blueprintId = this.value;
            
            blueprintDetails.style.display = 'none';
            
            if (blueprintId) {
                // Fetch blueprint details
                fetch(`/api/blueprints/${blueprintId}`)
                    .then(response => response.json())
                    .then(blueprint => {
                        document.getElementById('blueprint_marks').textContent = blueprint.total_marks;
                        document.getElementById('blueprint_duration').textContent = blueprint.duration;
                        document.getElementById('blueprint_sections').textContent = blueprint.sections.length;
                        
                        blueprintDetails.style.display = 'block';
                    });
            }
        });
        
        // Custom method - load units when subject changes
        customSubjectSelect.addEventListener('change', function() {
            const subjectId = this.value;
            
            // Clear units container
            unitsContainer.innerHTML = '';
            
            if (subjectId) {
                // Fetch units for the selected subject
                fetch(`/api/subjects/${subjectId}/units`)
                    .then(response => response.json())
                    .then(units => {
                        if (units.length > 0) {
                            const unitsHtml = `
                                <div class="accordion" id="unitsAccordion">
                                    ${units.map((unit, index) => `
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="heading${unit.id}">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${unit.id}" aria-expanded="false" aria-controls="collapse${unit.id}">
                                                    ${unit.unit_name}
                                                </button>
                                            </h2>
                                            <div id="collapse${unit.id}" class="accordion-collapse collapse" aria-labelledby="heading${unit.id}" data-bs-parent="#unitsAccordion">
                                                <div class="accordion-body">
                                                    <div class="topics-container" data-unit-id="${unit.id}">
                                                        <div class="text-center py-2">
                                                            <div class="spinner-border text-primary" role="status">
                                                                <span class="visually-hidden">Loading...</span>
                                                            </div>
                                                            <p>Loading topics...</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                            `;
                            
                            unitsContainer.innerHTML = unitsHtml;
                            
                            // Add event listeners to load topics when unit is expanded
                            document.querySelectorAll('.accordion-button').forEach(button => {
                                button.addEventListener('click', function() {
                                    const unitId = this.getAttribute('data-bs-target').replace('#collapse', '');
                                    const topicsContainer = document.querySelector(`.topics-container[data-unit-id="${unitId}"]`);
                                    
                                    if (topicsContainer.querySelector('.spinner-border')) {
                                        // Fetch topics for the unit
                                        fetch(`/api/units/${unitId}/topics`)
                                            .then(response => response.json())
                                            .then(topics => {
                                                if (topics.length > 0) {
                                                    const topicsHtml = `
                                                        <div class="list-group">
                                                            ${topics.map(topic => `
                                                                <a href="#" class="list-group-item list-group-item-action topic-item" data-topic-id="${topic.id}">
                                                                    ${topic.topic_name}
                                                                </a>
                                                            `).join('')}
                                                        </div>
                                                    `;
                                                    
                                                    topicsContainer.innerHTML = topicsHtml;
                                                    
                                                    // Add event listeners to load questions when topic is clicked
                                                    topicsContainer.querySelectorAll('.topic-item').forEach(item => {
                                                        item.addEventListener('click', function(e) {
                                                            e.preventDefault();
                                                            const topicId = this.getAttribute('data-topic-id');
                                                            
                                                            // Fetch questions for the topic
                                                            fetch(`/api/topics/${topicId}/questions`)
                                                                .then(response => response.json())
                                                                .then(questions => {
                                                                    if (questions.length > 0) {
                                                                        // Show questions in a modal
                                                                        const modal = new bootstrap.Modal(document.getElementById('questionsModal'));
                                                                        const modalBody = document.querySelector('#questionsModal .modal-body');
                                                                        
                                                                        modalBody.innerHTML = `
                                                                            <div class="list-group">
                                                                                ${questions.map(question => `
                                                                                    <div class="list-group-item">
                                                                                        <div class="d-flex justify-content-between align-items-center">
                                                                                            <div>
                                                                                                <h6>${question.question_text}</h6>
                                                                                                <div class="text-muted small">
                                                                                                    Type: ${getQuestionTypeLabel(question.question_type)} | 
                                                                                                    Marks: ${question.marks} | 
                                                                                                    Difficulty: ${getDifficultyLabel(question.difficulty_level)}
                                                                                                </div>
                                                                                            </div>
                                                                                            <button type="button" class="btn btn-sm btn-primary add-question" data-question-id="${question.id}" data-question-text="${question.question_text}" data-question-type="${question.question_type}" data-question-marks="${question.marks}" data-question-difficulty="${question.difficulty_level}">
                                                                                                <i class="fas fa-plus"></i>
                                                                                            </button>
                                                                                        </div>
                                                                                    </div>
                                                                                `).join('')}
                                                                            </div>
                                                                        `;
                                                                        
                                                                        // Add event listeners to add questions
                                                                        modalBody.querySelectorAll('.add-question').forEach(button => {
                                                                            button.addEventListener('click', function() {
                                                                                const questionId = this.getAttribute('data-question-id');
                                                                                const questionText = this.getAttribute('data-question-text');
                                                                                const questionType = this.getAttribute('data-question-type');
                                                                                const questionMarks = this.getAttribute('data-question-marks');
                                                                                const questionDifficulty = this.getAttribute('data-question-difficulty');
                                                                                
                                                                                addQuestionToSelection(questionId, questionText, questionType, questionMarks, questionDifficulty);
                                                                                
                                                                                // Disable the button
                                                                                this.disabled = true;
                                                                                this.innerHTML = '<i class="fas fa-check"></i>';
                                                                            });
                                                                        });
                                                                        
                                                                        modal.show();
                                                                    } else {
                                                                        alert('No questions found for this topic.');
                                                                    }
                                                                });
                                                        });
                                                    });
                                                } else {
                                                    topicsContainer.innerHTML = '<p class="text-muted">No topics found for this unit.</p>';
                                                }
                                            });
                                    }
                                });
                            });
                        } else {
                            unitsContainer.innerHTML = '<div class="alert alert-warning">No units found for this subject.</div>';
                        }
                    });
            }
        });
        
        // Function to add a question to the selection
        function addQuestionToSelection(id, text, type, marks, difficulty) {
            const selectedQuestionsTable = document.getElementById('selected_questions_table').querySelector('tbody');
            const selectedCount = document.getElementById('selected_count');
            const selectedTotalMarks = document.getElementById('selected_total_marks');
            
            // Check if question is already selected
            if (selectedQuestionsTable.querySelector(`[data-question-id="${id}"]`)) {
                return;
            }
            
            const row = document.createElement('tr');
            row.setAttribute('data-question-id', id);
            row.innerHTML = `
                <td>${text}</td>
                <td>${getQuestionTypeLabel(type)}</td>
                <td>${marks}</td>
                <td>${getDifficultyLabel(difficulty)}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger remove-question">
                        <i class="fas fa-times"></i>
                    </button>
                    <input type="hidden" name="selected_questions[]" value="${id}">
                </td>
            `;
            
            selectedQuestionsTable.appendChild(row);
            
            // Update count and total marks
            selectedCount.textContent = selectedQuestionsTable.querySelectorAll('tr').length;
            
            let totalMarks = 0;
            selectedQuestionsTable.querySelectorAll('tr').forEach(row => {
                totalMarks += parseInt(row.querySelector('td:nth-child(3)').textContent);
            });
            selectedTotalMarks.textContent = totalMarks;
            
            // Add event listener to remove button
            row.querySelector('.remove-question').addEventListener('click', function() {
                row.remove();
                
                // Update count and total marks
                selectedCount.textContent = selectedQuestionsTable.querySelectorAll('tr').length;
                
                let totalMarks = 0;
                selectedQuestionsTable.querySelectorAll('tr').forEach(row => {
                    totalMarks += parseInt(row.querySelector('td:nth-child(3)').textContent);
                });
                selectedTotalMarks.textContent = totalMarks;
            });
        }
        
        // Random method - add distribution row
        addDistributionBtn.addEventListener('click', function() {
            const distributionRows = questionDistribution.querySelectorAll('.row');
            const newIndex = distributionRows.length;
            
            const newRow = document.createElement('div');
            newRow.className = 'row mb-3';
            newRow.innerHTML = `
                <div class="col-md-3">
                    <label class="form-label">Question Type</label>
                    <select class="form-select" name="distribution[${newIndex}][question_type]">
                        <option value="mcq">Multiple Choice</option>
                        <option value="true_false">True/False</option>
                        <option value="short_answer">Short Answer</option>
                        <option value="long_answer">Long Answer</option>
                        <option value="fill_in_the_blank">Fill in the Blank</option>
                        <option value="matching">Matching</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Count</label>
                    <input type="number" class="form-control distribution-count" name="distribution[${newIndex}][count]" min="1" value="5">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Marks Each</label>
                    <input type="number" class="form-control distribution-marks" name="distribution[${newIndex}][marks_per_question]" min="1" value="1">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Difficulty</label>
                    <select class="form-select" name="distribution[${newIndex}][difficulty_level]">
                        <option value="easy">Easy</option>
                        <option value="medium" selected>Medium</option>
                        <option value="hard">Hard</option>
                        <option value="mixed">Mixed</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger remove-distribution">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            questionDistribution.appendChild(newRow);
            
            // Show remove button for first row
            if (newIndex === 1) {
                questionDistribution.querySelector('.remove-distribution').style.display = 'block';
            }
            
            // Add event listener to remove button
            newRow.querySelector('.remove-distribution').addEventListener('click', function() {
                newRow.remove();
                updateDistributionIndices();
                calculateDistributionTotal();
                
                // Hide remove button for first row if only one row remains
                if (questionDistribution.querySelectorAll('.row').length === 1) {
                    questionDistribution.querySelector('.remove-distribution').style.display = 'none';
                }
            });
            
            // Add event listeners to count and marks inputs
            newRow.querySelectorAll('.distribution-count, .distribution-marks').forEach(input => {
                input.addEventListener('input', calculateDistributionTotal);
            });
            
            calculateDistributionTotal();
        });
        
        // Remove distribution row
        questionDistribution.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-distribution') || e.target.parentElement.classList.contains('remove-distribution')) {
                const button = e.target.closest('.remove-distribution');
                const row = button.closest('.row');
                
                if (questionDistribution.querySelectorAll('.row').length > 1) {
                    row.remove();
                    updateDistributionIndices();
                    calculateDistributionTotal();
                    
                    // Hide remove button for first row if only one row remains
                    if (questionDistribution.querySelectorAll('.row').length === 1) {
                        questionDistribution.querySelector('.remove-distribution').style.display = 'none';
                    }
                }
            }
        });
        
        // Update distribution indices
        function updateDistributionIndices() {
            const rows = questionDistribution.querySelectorAll('.row');
            rows.forEach((row, index) => {
                const selects = row.querySelectorAll('select');
                const inputs = row.querySelectorAll('input');
                
                selects.forEach(select => {
                    const name = select.getAttribute('name');
                    if (name) {
                        const newName = name.replace(/distribution\[\d+\]/, `distribution[${index}]`);
                        select.setAttribute('name', newName);
                    }
                });
                
                inputs.forEach(input => {
                    const name = input.getAttribute('name');
                    if (name) {
                        const newName = name.replace(/distribution\[\d+\]/, `distribution[${index}]`);
                        input.setAttribute('name', newName);
                    }
                });
            });
        }
        
        // Calculate distribution total
        function calculateDistributionTotal() {
            const rows = questionDistribution.querySelectorAll('.row');
            let total = 0;
            
            rows.forEach(row => {
                const count = parseInt(row.querySelector('.distribution-count').value) || 0;
                const marks = parseInt(row.querySelector('.distribution-marks').value) || 0;
                total += count * marks;
            });
            
            distributionTotalMarks.textContent = total;
            
            // Check if total matches random total marks
            const randomTotal = parseInt(randomTotalMarksInput.value) || 0;
            if (randomTotal > 0 && total !== randomTotal) {
                distributionWarning.style.display = 'block';
            } else {
                distributionWarning.style.display = 'none';
            }
        }
        
        // Listen for changes in random total marks
        randomTotalMarksInput.addEventListener('input', calculateDistributionTotal);
        
        // Listen for changes in distribution count and marks
        questionDistribution.addEventListener('input', function(e) {
            if (e.target.classList.contains('distribution-count') || e.target.classList.contains('distribution-marks')) {
                calculateDistributionTotal();
            }
        });
        
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
        
        function getDifficultyLabel(difficulty) {
            switch (difficulty) {
                case 'easy': return '<span class="badge bg-success">Easy</span>';
                case 'medium': return '<span class="badge bg-warning">Medium</span>';
                case 'hard': return '<span class="badge bg-danger">Hard</span>';
                case 'mixed': return '<span class="badge bg-secondary">Mixed</span>';
                default: return difficulty;
            }
        }
        
        // Initial calculations
        calculateDistributionTotal();
    });
</script>

<!-- Questions Modal -->
<div class="modal fade" id="questionsModal" tabindex="-1" aria-labelledby="questionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="questionsModalLabel">Select Questions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Questions will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endpush
@endsection
