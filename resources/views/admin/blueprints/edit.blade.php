@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Edit Blueprint</h1>
        <a href="{{ route('admin.blueprints.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Blueprints
        </a>
    </div>
    
    <form action="{{ route('admin.blueprints.update', $blueprint->id) }}" method="POST" id="blueprintForm">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Blueprint Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Blueprint Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $blueprint->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="subject_id" class="form-label">Subject <span class="text-danger">*</span></label>
                            <select class="form-select @error('subject_id') is-invalid @enderror" id="subject_id" name="subject_id" required>
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id', $blueprint->subject_id) == $subject->id ? 'selected' : '' }}>
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
                            <input type="number" class="form-control @error('total_marks') is-invalid @enderror" id="total_marks" name="total_marks" value="{{ old('total_marks', $blueprint->total_marks) }}" min="1" required>
                            @error('total_marks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="duration" class="form-label">Duration (minutes) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('duration') is-invalid @enderror" id="duration" name="duration" value="{{ old('duration', $blueprint->duration) }}" min="1" required>
                            @error('duration')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="instructions" class="form-label">General Instructions</label>
                            <textarea class="form-control @error('instructions') is-invalid @enderror" id="instructions" name="instructions" rows="4">{{ old('instructions', $blueprint->instructions) }}</textarea>
                            @error('instructions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Blueprint
                            </button>
                            <button type="button" class="btn btn-success" id="validateBtn">
                                <i class="fas fa-check-circle me-2"></i>Validate Blueprint
                            </button>
                            <a href="{{ route('admin.blueprints.show', $blueprint->id) }}" class="btn btn-light">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Blueprint Sections</h5>
                        <button type="button" class="btn btn-sm btn-primary" id="addSectionBtn">
                            <i class="fas fa-plus-circle me-1"></i>Add Section
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="sectionsContainer">
                            <!-- Sections will be added here dynamically -->
                            @if(count($sections) === 0)
                                <div class="alert alert-info" id="noSectionsMessage">
                                    <i class="fas fa-info-circle me-2"></i>
                                    No sections added yet. Click the "Add Section" button to create a section.
                                </div>
                            @else
                                @foreach($sections as $sectionIndex => $section)
                                    <div class="section-card mb-4" data-section-index="{{ $sectionIndex }}">
                                        <div class="card">
                                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                                <h6 class="card-title mb-0">Section {{ $sectionIndex + 1 }}</h6>
                                                <div>
                                                    <button type="button" class="btn btn-sm btn-danger remove-section-btn">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row mb-3">
                                                    <div class="col-md-8">
                                                        <label class="form-label">Section Title <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control section-title" name="sections[{{ $sectionIndex }}][title]" value="{{ $section->title }}" placeholder="e.g., Multiple Choice Questions" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Total Marks</label>
                                                        <input type="number" class="form-control section-marks" name="sections[{{ $sectionIndex }}][total_marks]" value="{{ $section->total_marks }}" readonly>
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Instructions</label>
                                                    <textarea class="form-control" name="sections[{{ $sectionIndex }}][instructions]" rows="2" placeholder="Instructions for this section">{{ $section->instructions }}</textarea>
                                                </div>
                                                
                                                <div class="rules-container">
                                                    @foreach($section->rules as $ruleIndex => $rule)
                                                        <div class="rule-card mb-3" data-rule-index="{{ $ruleIndex }}">
                                                            <div class="card">
                                                                <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                                                                    <h6 class="card-title mb-0">Rule {{ $ruleIndex + 1 }}</h6>
                                                                    <button type="button" class="btn btn-sm btn-danger remove-rule-btn">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </div>
                                                                <div class="card-body">
                                                                    <div class="row mb-3">
                                                                        <div class="col-md-6">
                                                                            <label class="form-label">Unit</label>
                                                                            <select class="form-select rule-unit" name="sections[{{ $sectionIndex }}][rules][{{ $ruleIndex }}][unit_id]">
                                                                                <option value="">Any Unit</option>
                                                                                @foreach($units->where('subject_id', $blueprint->subject_id) as $unit)
                                                                                    <option value="{{ $unit->id }}" {{ $rule->unit_id == $unit->id ? 'selected' : '' }}>
                                                                                        {{ $unit->unit_name }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <label class="form-label">Topic</label>
                                                                            <select class="form-select rule-topic" name="sections[{{ $sectionIndex }}][rules][{{ $ruleIndex }}][topic_id]">
                                                                                <option value="">Any Topic</option>
                                                                                @if($rule->unit_id)
                                                                                    @foreach($topics->where('unit_id', $rule->unit_id) as $topic)
                                                                                        <option value="{{ $topic->id }}" {{ $rule->topic_id == $topic->id ? 'selected' : '' }}>
                                                                                            {{ $topic->topic_name }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                @endif
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <div class="row mb-3">
                                                                        <div class="col-md-4">
                                                                            <label class="form-label">Question Type</label>
                                                                            <select class="form-select" name="sections[{{ $sectionIndex }}][rules][{{ $ruleIndex }}][question_type]">
                                                                                <option value="">Any Type</option>
                                                                                <option value="mcq" {{ $rule->question_type == 'mcq' ? 'selected' : '' }}>Multiple Choice</option>
                                                                                <option value="true_false" {{ $rule->question_type == 'true_false' ? 'selected' : '' }}>True/False</option>
                                                                                <option value="short_answer" {{ $rule->question_type == 'short_answer' ? 'selected' : '' }}>Short Answer</option>
                                                                                <option value="long_answer" {{ $rule->question_type == 'long_answer' ? 'selected' : '' }}>Long Answer</option>
                                                                                <option value="fill_in_the_blank" {{ $rule->question_type == 'fill_in_the_blank' ? 'selected' : '' }}>Fill in the Blank</option>
                                                                                <option value="matching" {{ $rule->question_type == 'matching' ? 'selected' : '' }}>Matching</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <label class="form-label">Difficulty Level</label>
                                                                            <select class="form-select" name="sections[{{ $sectionIndex }}][rules][{{ $ruleIndex }}][difficulty_level]">
                                                                                <option value="">Any Difficulty</option>
                                                                                <option value="easy" {{ $rule->difficulty_level == 'easy' ? 'selected' : '' }}>Easy</option>
                                                                                <option value="medium" {{ $rule->difficulty_level == 'medium' ? 'selected' : '' }}>Medium</option>
                                                                                <option value="hard" {{ $rule->difficulty_level == 'hard' ? 'selected' : '' }}>Hard</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <label class="form-label">Bloom's Level</label>
                                                                            <select class="form-select" name="sections[{{ $sectionIndex }}][rules][{{ $ruleIndex }}][blooms_taxonomy_id]">
                                                                                <option value="">Any Level</option>
                                                                                @foreach($bloomsTaxonomies as $taxonomy)
                                                                                    <option value="{{ $taxonomy->id }}" {{ $rule->blooms_taxonomy_id == $taxonomy->id ? 'selected' : '' }}>
                                                                                        {{ $taxonomy->level_name }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <div class="row">
                                                                        <div class="col-md-4">
                                                                            <label class="form-label">Marks per Question <span class="text-danger">*</span></label>
                                                                            <input type="number" class="form-control marks-per-question" name="sections[{{ $sectionIndex }}][rules][{{ $ruleIndex }}][marks_per_question]" value="{{ $rule->marks_per_question }}" min="1" required>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <label class="form-label">Number of Questions <span class="text-danger">*</span></label>
                                                                            <input type="number" class="form-control num-questions" name="sections[{{ $sectionIndex }}][rules][{{ $ruleIndex }}][num_questions]" value="{{ $rule->num_questions }}" min="1" required>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <label class="form-label">Total Marks</label>
                                                                            <input type="number" class="form-control rule-total-marks" value="{{ $rule->marks_per_question * $rule->num_questions }}" readonly>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                
                                                <div class="text-end mt-3">
                                                    <button type="button" class="btn btn-sm btn-primary add-rule-btn">
                                                        <i class="fas fa-plus-circle me-1"></i>Add Rule
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        
                        <div class="card mt-4">
                            <div class="card-body bg-light">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Total Questions: <span id="totalQuestions">{{ $totalQuestions }}</span></h6>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Total Marks: <span id="calculatedMarks">{{ $calculatedMarks }}</span> / <span id="requiredMarks">{{ $blueprint->total_marks }}</span></h6>
                                        <div class="progress mt-2">
                                            <div class="progress-bar" id="marksProgressBar" role="progressbar" style="width: {{ ($calculatedMarks / $blueprint->total_marks) * 100 }}%;" aria-valuenow="{{ ($calculatedMarks / $blueprint->total_marks) * 100 }}" aria-valuemin="0" aria-valuemax="100">{{ round(($calculatedMarks / $blueprint->total_marks) * 100) }}%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    
    <!-- Section Template (Hidden) -->
    <template id="sectionTemplate">
        <div class="section-card mb-4" data-section-index="{sectionIndex}">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">Section {sectionNumber}</h6>
                    <div>
                        <button type="button" class="btn btn-sm btn-danger remove-section-btn">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label">Section Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control section-title" name="sections[{sectionIndex}][title]" placeholder="e.g., Multiple Choice Questions" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Total Marks</label>
                            <input type="number" class="form-control section-marks" name="sections[{sectionIndex}][total_marks]" value="0" readonly>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Instructions</label>
                        <textarea class="form-control" name="sections[{sectionIndex}][instructions]" rows="2" placeholder="Instructions for this section"></textarea>
                    </div>
                    
                    <div class="rules-container">
                        <!-- Rules will be added here dynamically -->
                    </div>
                    
                    <div class="text-end mt-3">
                        <button type="button" class="btn btn-sm btn-primary add-rule-btn">
                            <i class="fas fa-plus-circle me-1"></i>Add Rule
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
    
    <!-- Rule Template (Hidden) -->
    <template id="ruleTemplate">
        <div class="rule-card mb-3" data-rule-index="{ruleIndex}">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                    <h6 class="card-title mb-0">Rule {ruleNumber}</h6>
                    <button type="button" class="btn btn-sm btn-danger remove-rule-btn">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Unit</label>
                            <select class="form-select rule-unit" name="sections[{sectionIndex}][rules][{ruleIndex}][unit_id]">
                                <option value="">Any Unit</option>
                                <!-- Units will be populated dynamically -->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Topic</label>
                            <select class="form-select rule-topic" name="sections[{sectionIndex}][rules][{ruleIndex}][topic_id]">
                                <option value="">Any Topic</option>
                                <!-- Topics will be populated dynamically -->
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Question Type</label>
                            <select class="form-select" name="sections[{sectionIndex}][rules][{ruleIndex}][question_type]">
                                <option value="">Any Type</option>
                                <option value="mcq">Multiple Choice</option>
                                <option value="true_false">True/False</option>
                                <option value="short_answer">Short Answer</option>
                                <option value="long_answer">Long Answer</option>
                                <option value="fill_in_the_blank">Fill in the Blank</option>
                                <option value="matching">Matching</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Difficulty Level</label>
                            <select class="form-select" name="sections[{sectionIndex}][rules][{ruleIndex}][difficulty_level]">
                                <option value="">Any Difficulty</option>
                                <option value="easy">Easy</option>
                                <option value="medium">Medium</option>
                                <option value="hard">Hard</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Bloom's Level</label>
                            <select class="form-select" name="sections[{sectionIndex}][rules][{ruleIndex}][blooms_taxonomy_id]">
                                <option value="">Any Level</option>
                                <!-- Bloom's levels will be populated dynamically -->
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">Marks per Question <span class="text-danger">*</span></label>
                            <input type="number" class="form-control marks-per-question" name="sections[{sectionIndex}][rules][{ruleIndex}][marks_per_question]" value="1" min="1" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Number of Questions <span class="text-danger">*</span></label>
                            <input type="number" class="form-control num-questions" name="sections[{sectionIndex}][rules][{ruleIndex}][num_questions]" value="1" min="1" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Total Marks</label>
                            <input type="number" class="form-control rule-total-marks" value="1" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const subjectSelect = document.getElementById('subject_id');
        const totalMarksInput = document.getElementById('total_marks');
        const sectionsContainer = document.getElementById('sectionsContainer');
        const noSectionsMessage = document.getElementById('noSectionsMessage');
        const addSectionBtn = document.getElementById('addSectionBtn');
        const validateBtn = document.getElementById('validateBtn');
        const sectionTemplate = document.getElementById('sectionTemplate').innerHTML;
        const ruleTemplate = document.getElementById('ruleTemplate').innerHTML;
        
        let sectionCount = {{ count($sections) }};
        let bloomsTaxonomies = @json($bloomsTaxonomies);
        let units = @json($units->where('subject_id', $blueprint->subject_id));
        let topics = @json($topics);
        
        // Initialize rich text editor for instructions
        if (typeof ClassicEditor !== 'undefined') {
            ClassicEditor.create(document.getElementById('instructions'))
                .catch(error => {
                    console.error(error);
                });
        }
        
        // Load units when subject changes
        subjectSelect.addEventListener('change', function() {
            const subjectId = this.value;
            
            if (subjectId) {
                // Fetch units for the selected subject
                fetch(`{{ url('admin/units/by-subject') }}/${subjectId}`)
                    .then(response => response.json())
                    .then(data => {
                        units = data;
                        updateUnitSelects();
                    });
            } else {
                units = [];
                updateUnitSelects();
            }
        });
        
        // Update total marks display when input changes
        totalMarksInput.addEventListener('change', function() {
            document.getElementById('requiredMarks').textContent = this.value;
            updateTotals();
        });
        
        // Add section button click handler
        addSectionBtn.addEventListener('click', function() {
            addSection();
        });
        
        // Validate blueprint button click handler
        validateBtn.addEventListener('click', function() {
            validateBlueprint();
        });
        
        // Add a new section
        function addSection() {
            const sectionIndex = sectionCount++;
            const sectionNumber = sectionIndex + 1;
            
            let sectionHtml = sectionTemplate
                .replace(/{sectionIndex}/g, sectionIndex)
                .replace(/{sectionNumber}/g, sectionNumber);
            
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = sectionHtml;
            const sectionElement = tempDiv.firstElementChild;
            
            sectionsContainer.appendChild(sectionElement);
            if (noSectionsMessage) {
                noSectionsMessage.style.display = 'none';
            }
            
            // Add event listeners for the new section
            const removeBtn = sectionElement.querySelector('.remove-section-btn');
            removeBtn.addEventListener('click', function() {
                removeSection(sectionElement);
            });
            
            const addRuleBtn = sectionElement.querySelector('.add-rule-btn');
            addRuleBtn.addEventListener('click', function() {
                addRule(sectionElement, sectionIndex);
            });
            
            // Add the first rule automatically
            addRule(sectionElement, sectionIndex);
        }
        
        // Remove a section
        function removeSection(sectionElement) {
            sectionsContainer.removeChild(sectionElement);
            
            const sections = sectionsContainer.querySelectorAll('.section-card');
            if (sections.length === 0 && noSectionsMessage) {
                noSectionsMessage.style.display = 'block';
            }
            
            updateTotals();
        }
        
        // Add a new rule to a section
        function addRule(sectionElement, sectionIndex) {
            const rulesContainer = sectionElement.querySelector('.rules-container');
            const ruleIndex = rulesContainer.querySelectorAll('.rule-card').length;
            const ruleNumber = ruleIndex + 1;
            
            let ruleHtml = ruleTemplate
                .replace(/{sectionIndex}/g, sectionIndex)
                .replace(/{ruleIndex}/g, ruleIndex)
                .replace(/{ruleNumber}/g, ruleNumber);
            
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = ruleHtml;
            const ruleElement = tempDiv.firstElementChild;
            
            rulesContainer.appendChild(ruleElement);
            
            // Add event listeners for the new rule
            const removeBtn = ruleElement.querySelector('.remove-rule-btn');
            removeBtn.addEventListener('click', function() {
                removeRule(ruleElement, sectionElement);
            });
            
            // Populate unit select
            const unitSelect = ruleElement.querySelector('.rule-unit');
            populateUnitSelect(unitSelect);
            
            // Populate Bloom's taxonomy select
            const bloomsSelect = ruleElement.querySelector('select[name$="[blooms_taxonomy_id]"]');
            populateBloomsSelect(bloomsSelect);
            
            // Add event listeners for unit change
            unitSelect.addEventListener('change', function() {
                const unitId = this.value;
                const topicSelect = ruleElement.querySelector('.rule-topic');
                
                if (unitId) {
                    // Fetch topics for the selected unit
                    fetch(`{{ url('admin/topics/by-unit') }}/${unitId}`)
                        .then(response => response.json())
                        .then(data => {
                            populateTopicSelect(topicSelect, data);
                        });
                } else {
                    populateTopicSelect(topicSelect, []);
                }
            });
            
            // Add event listeners for marks and questions inputs
            const marksPerQuestionInput = ruleElement.querySelector('.marks-per-question');
            const numQuestionsInput = ruleElement.querySelector('.num-questions');
            const ruleTotalMarksInput = ruleElement.querySelector('.rule-total-marks');
            
            function updateRuleTotalMarks() {
                const marksPerQuestion = parseInt(marksPerQuestionInput.value) || 0;
                const numQuestions = parseInt(numQuestionsInput.value) || 0;
                const totalMarks = marksPerQuestion * numQuestions;
                
                ruleTotalMarksInput.value = totalMarks;
                updateSectionTotalMarks(sectionElement);
                updateTotals();
            }
            
            marksPerQuestionInput.addEventListener('change', updateRuleTotalMarks);
            numQuestionsInput.addEventListener('change', updateRuleTotalMarks);
            
            // Initialize rule total marks
            updateRuleTotalMarks();
        }
        
        // Remove a rule
        function removeRule(ruleElement, sectionElement) {
            const rulesContainer = ruleElement.parentNode;
            rulesContainer.removeChild(ruleElement);
            
            // Renumber remaining rules
            const rules = rulesContainer.querySelectorAll('.rule-card');
            rules.forEach((rule, index) => {
                const ruleNumber = index + 1;
                rule.querySelector('.card-title').textContent = `Rule ${ruleNumber}`;
            });
            
            updateSectionTotalMarks(sectionElement);
            updateTotals();
        }
        
        // Update section total marks
        function updateSectionTotalMarks(sectionElement) {
            const rules = sectionElement.querySelectorAll('.rule-card');
            let sectionTotalMarks = 0;
            
            rules.forEach(rule => {
                const ruleTotalMarks = parseInt(rule.querySelector('.rule-total-marks').value) || 0;
                sectionTotalMarks += ruleTotalMarks;
            });
            
            sectionElement.querySelector('.section-marks').value = sectionTotalMarks;
        }
        
        // Update all unit selects
        function updateUnitSelects() {
            const unitSelects = document.querySelectorAll('.rule-unit');
            unitSelects.forEach(select => {
                populateUnitSelect(select);
            });
        }
        
        // Populate a unit select
        function populateUnitSelect(select) {
            const currentValue = select.value;
            
            // Clear select
            select.innerHTML = '<option value="">Any Unit</option>';
            
            // Add units
            units.forEach(unit => {
                const option = document.createElement('option');
                option.value = unit.id;
                option.textContent = unit.unit_name;
                select.appendChild(option);
            });
            
            // Restore selected value if it exists in the new options
            if (currentValue) {
                select.value = currentValue;
            }
        }
        
        // Populate a topic select
        function populateTopicSelect(select, topicsData) {
            const currentValue = select.value;
            
            // Clear select
            select.innerHTML = '<option value="">Any Topic</option>';
            
            // Add topics
            topicsData.forEach(topic => {
                const option = document.createElement('option');
                option.value = topic.id;
                option.textContent = topic.topic_name;
                select.appendChild(option);
            });
            
            // Restore selected value if it exists in the new options
            if (currentValue) {
                select.value = currentValue;
            }
        }
        
        // Populate a Bloom's taxonomy select
        function populateBloomsSelect(select) {
            const currentValue = select.value;
            
            // Clear select
            select.innerHTML = '<option value="">Any Level</option>';
            
            // Add Bloom's taxonomy levels
            bloomsTaxonomies.forEach(taxonomy => {
                const option = document.createElement('option');
                option.value = taxonomy.id;
                option.textContent = taxonomy.level_name;
                select.appendChild(option);
            });
            
            // Restore selected value if it exists in the new options
            if (currentValue) {
                select.value = currentValue;
            }
        }
        
        // Update totals (total questions, total marks, progress bar)
        function updateTotals() {
            let totalQuestions = 0;
            let totalMarks = 0;
            const requiredMarks = parseInt(totalMarksInput.value) || 0;
            
            // Calculate totals from all rules
            const rules = document.querySelectorAll('.rule-card');
            rules.forEach(rule => {
                const numQuestions = parseInt(rule.querySelector('.num-questions').value) || 0;
                const marksPerQuestion = parseInt(rule.querySelector('.marks-per-question').value) || 0;
                
                totalQuestions += numQuestions;
                totalMarks += (numQuestions * marksPerQuestion);
            });
            
            // Update display
            document.getElementById('totalQuestions').textContent = totalQuestions;
            document.getElementById('calculatedMarks').textContent = totalMarks;
            
            // Update progress bar
            const percentage = requiredMarks > 0 ? Math.min(100, Math.round((totalMarks / requiredMarks) * 100)) : 0;
            const progressBar = document.getElementById('marksProgressBar');
            progressBar.style.width = `${percentage}%`;
            progressBar.textContent = `${percentage}%`;
            progressBar.setAttribute('aria-valuenow', percentage);
            
            // Set progress bar color based on percentage
            if (percentage < 100) {
                progressBar.classList.remove('bg-success');
                progressBar.classList.add('bg-warning');
            } else {
                progressBar.classList.remove('bg-warning');
                progressBar.classList.add('bg-success');
            }
        }
        
        // Validate blueprint
        function validateBlueprint() {
            const requiredMarks = parseInt(totalMarksInput.value) || 0;
            let totalMarks = 0;
            
            // Calculate total marks from all rules
            const rules = document.querySelectorAll('.rule-card');
            rules.forEach(rule => {
                const numQuestions = parseInt(rule.querySelector('.num-questions').value) || 0;
                const marksPerQuestion = parseInt(rule.querySelector('.marks-per-question').value) || 0;
                
                totalMarks += (numQuestions * marksPerQuestion);
            });
            
            // Check if there are any sections
            if (document.querySelectorAll('.section-card').length === 0) {
                alert('Please add at least one section to the blueprint.');
                return;
            }
            
            // Check if total marks match required marks
            if (totalMarks !== requiredMarks) {
                alert(`Total marks (${totalMarks}) do not match the required marks (${requiredMarks}). Please adjust your rules.`);
                return;
            }
            
            // Check if all required fields are filled
            const requiredFields = document.querySelectorAll('#blueprintForm [required]');
            let allFieldsFilled = true;
            
            requiredFields.forEach(field => {
                if (!field.value) {
                    allFieldsFilled = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            if (!allFieldsFilled) {
                alert('Please fill in all required fields.');
                return;
            }
            
            alert('Blueprint validation successful! You can now update the blueprint.');
        }
        
        // Form submission
        document.getElementById('blueprintForm').addEventListener('submit', function(e) {
            const requiredMarks = parseInt(totalMarksInput.value) || 0;
            let totalMarks = 0;
            
            // Calculate total marks from all rules
            const rules = document.querySelectorAll('.rule-card');
            rules.forEach(rule => {
                const numQuestions = parseInt(rule.querySelector('.num-questions').value) || 0;
                const marksPerQuestion = parseInt(rule.querySelector('.marks-per-question').value) || 0;
                
                totalMarks += (numQuestions * marksPerQuestion);
            });
            
            // Check if there are any sections
            if (document.querySelectorAll('.section-card').length === 0) {
                e.preventDefault();
                alert('Please add at least one section to the blueprint.');
                return;
            }
            
            // Check if total marks match required marks
            if (totalMarks !== requiredMarks) {
                e.preventDefault();
                alert(`Total marks (${totalMarks}) do not match the required marks (${requiredMarks}). Please adjust your rules.`);
                return;
            }
        });
        
        // Initialize event listeners for existing sections and rules
        document.querySelectorAll('.section-card').forEach(sectionElement => {
            const sectionIndex = parseInt(sectionElement.dataset.sectionIndex);
            
            // Add event listener for remove section button
            const removeSectionBtn = sectionElement.querySelector('.remove-section-btn');
            removeSectionBtn.addEventListener('click', function() {
                removeSection(sectionElement);
            });
            
            // Add event listener for add rule button
            const addRuleBtn = sectionElement.querySelector('.add-rule-btn');
            addRuleBtn.addEventListener('click', function() {
                addRule(sectionElement, sectionIndex);
            });
            
            // Add event listeners for existing rules
            sectionElement.querySelectorAll('.rule-card').forEach(ruleElement => {
                // Add event listener for remove rule button
                const removeRuleBtn = ruleElement.querySelector('.remove-rule-btn');
                removeRuleBtn.addEventListener('click', function() {
                    removeRule(ruleElement, sectionElement);
                });
                
                // Add event listener for unit change
                const unitSelect = ruleElement.querySelector('.rule-unit');
                unitSelect.addEventListener('change', function() {
                    const unitId = this.value;
                    const topicSelect = ruleElement.querySelector('.rule-topic');
                    
                    if (unitId) {
                        // Fetch topics for the selected unit
                        fetch(`{{ url('admin/topics/by-unit') }}/${unitId}`)
                            .then(response => response.json())
                            .then(data => {
                                populateTopicSelect(topicSelect, data);
                            });
                    } else {
                        populateTopicSelect(topicSelect, []);
                    }
                });
                
                // Add event listeners for marks and questions inputs
                const marksPerQuestionInput = ruleElement.querySelector('.marks-per-question');
                const numQuestionsInput = ruleElement.querySelector('.num-questions');
                const ruleTotalMarksInput = ruleElement.querySelector('.rule-total-marks');
                
                function updateRuleTotalMarks() {
                    const marksPerQuestion = parseInt(marksPerQuestionInput.value) || 0;
                    const numQuestions = parseInt(numQuestionsInput.value) || 0;
                    const totalMarks = marksPerQuestion * numQuestions;
                    
                    ruleTotalMarksInput.value = totalMarks;
                    updateSectionTotalMarks(sectionElement);
                    updateTotals();
                }
                
                marksPerQuestionInput.addEventListener('change', updateRuleTotalMarks);
                numQuestionsInput.addEventListener('change', updateRuleTotalMarks);
            });
        });
        
        // Initialize totals
        updateTotals();
    });
</script>
@endpush
@endsection
