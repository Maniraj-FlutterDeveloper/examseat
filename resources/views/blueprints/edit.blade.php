@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Edit Blueprint</h1>
        <a href="{{ route('blueprints.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Blueprints
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('blueprints.update', $blueprint->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="title" class="form-label">Blueprint Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $blueprint->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
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
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="total_marks" class="form-label">Total Marks <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('total_marks') is-invalid @enderror" id="total_marks" name="total_marks" value="{{ old('total_marks', $blueprint->total_marks) }}" min="1" required>
                        @error('total_marks')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="duration" class="form-label">Duration (minutes) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('duration') is-invalid @enderror" id="duration" name="duration" value="{{ old('duration', $blueprint->duration) }}" min="1" required>
                        @error('duration')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="passing_percentage" class="form-label">Passing Percentage <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('passing_percentage') is-invalid @enderror" id="passing_percentage" name="passing_percentage" value="{{ old('passing_percentage', $blueprint->passing_percentage) }}" min="1" max="100" required>
                        @error('passing_percentage')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $blueprint->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="instructions" class="form-label">Instructions</label>
                    <textarea class="form-control @error('instructions') is-invalid @enderror" id="instructions" name="instructions" rows="3">{{ old('instructions', $blueprint->instructions) }}</textarea>
                    @error('instructions')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <hr class="my-4">
                <h5 class="mb-3">Blueprint Sections</h5>
                
                <div id="sections_container">
                    @php
                        $sections = old('sections', $blueprint->sections);
                    @endphp
                    
                    @if($sections)
                        @foreach($sections as $index => $section)
                            <div class="card mb-3 section-card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Section {{ $index + 1 }}</h6>
                                    <button type="button" class="btn btn-sm btn-danger remove-section">
                                        <i class="fas fa-times"></i> Remove
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Section Title <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('sections.'.$index.'.title') is-invalid @enderror" name="sections[{{ $index }}][title]" value="{{ $section['title'] ?? '' }}" required>
                                            @error('sections.'.$index.'.title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Section Marks <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control section-marks @error('sections.'.$index.'.marks') is-invalid @enderror" name="sections[{{ $index }}][marks]" value="{{ $section['marks'] ?? '' }}" min="1" required>
                                            @error('sections.'.$index.'.marks')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label class="form-label">Section Instructions</label>
                                            <textarea class="form-control @error('sections.'.$index.'.instructions') is-invalid @enderror" name="sections[{{ $index }}][instructions]" rows="2">{{ $section['instructions'] ?? '' }}</textarea>
                                            @error('sections.'.$index.'.instructions')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <h6 class="mb-3">Question Distribution</h6>
                                    <div class="question-distribution">
                                        @if(isset($section['distribution']))
                                            @foreach($section['distribution'] as $distIndex => $dist)
                                                <div class="row mb-2 distribution-row">
                                                    <div class="col-md-3">
                                                        <select class="form-select @error('sections.'.$index.'.distribution.'.$distIndex.'.question_type') is-invalid @enderror" name="sections[{{ $index }}][distribution][{{ $distIndex }}][question_type]" required>
                                                            <option value="">Question Type</option>
                                                            <option value="mcq" {{ $dist['question_type'] == 'mcq' ? 'selected' : '' }}>Multiple Choice</option>
                                                            <option value="true_false" {{ $dist['question_type'] == 'true_false' ? 'selected' : '' }}>True/False</option>
                                                            <option value="short_answer" {{ $dist['question_type'] == 'short_answer' ? 'selected' : '' }}>Short Answer</option>
                                                            <option value="long_answer" {{ $dist['question_type'] == 'long_answer' ? 'selected' : '' }}>Long Answer</option>
                                                            <option value="fill_in_the_blank" {{ $dist['question_type'] == 'fill_in_the_blank' ? 'selected' : '' }}>Fill in the Blank</option>
                                                            <option value="matching" {{ $dist['question_type'] == 'matching' ? 'selected' : '' }}>Matching</option>
                                                        </select>
                                                        @error('sections.'.$index.'.distribution.'.$distIndex.'.question_type')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="number" class="form-control @error('sections.'.$index.'.distribution.'.$distIndex.'.count') is-invalid @enderror" name="sections[{{ $index }}][distribution][{{ $distIndex }}][count]" placeholder="Count" value="{{ $dist['count'] ?? '' }}" min="1" required>
                                                        @error('sections.'.$index.'.distribution.'.$distIndex.'.count')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="number" class="form-control @error('sections.'.$index.'.distribution.'.$distIndex.'.marks_per_question') is-invalid @enderror" name="sections[{{ $index }}][distribution][{{ $distIndex }}][marks_per_question]" placeholder="Marks each" value="{{ $dist['marks_per_question'] ?? '' }}" min="1" required>
                                                        @error('sections.'.$index.'.distribution.'.$distIndex.'.marks_per_question')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-3">
                                                        <select class="form-select @error('sections.'.$index.'.distribution.'.$distIndex.'.difficulty_level') is-invalid @enderror" name="sections[{{ $index }}][distribution][{{ $distIndex }}][difficulty_level]" required>
                                                            <option value="">Difficulty</option>
                                                            <option value="easy" {{ $dist['difficulty_level'] == 'easy' ? 'selected' : '' }}>Easy</option>
                                                            <option value="medium" {{ $dist['difficulty_level'] == 'medium' ? 'selected' : '' }}>Medium</option>
                                                            <option value="hard" {{ $dist['difficulty_level'] == 'hard' ? 'selected' : '' }}>Hard</option>
                                                            <option value="mixed" {{ $dist['difficulty_level'] == 'mixed' ? 'selected' : '' }}>Mixed</option>
                                                        </select>
                                                        @error('sections.'.$index.'.distribution.'.$distIndex.'.difficulty_level')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-danger remove-distribution">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="row mb-2 distribution-row">
                                                <div class="col-md-3">
                                                    <select class="form-select" name="sections[{{ $index }}][distribution][0][question_type]" required>
                                                        <option value="">Question Type</option>
                                                        <option value="mcq">Multiple Choice</option>
                                                        <option value="true_false">True/False</option>
                                                        <option value="short_answer">Short Answer</option>
                                                        <option value="long_answer">Long Answer</option>
                                                        <option value="fill_in_the_blank">Fill in the Blank</option>
                                                        <option value="matching">Matching</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number" class="form-control" name="sections[{{ $index }}][distribution][0][count]" placeholder="Count" min="1" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number" class="form-control" name="sections[{{ $index }}][distribution][0][marks_per_question]" placeholder="Marks each" min="1" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <select class="form-select" name="sections[{{ $index }}][distribution][0][difficulty_level]" required>
                                                        <option value="">Difficulty</option>
                                                        <option value="easy">Easy</option>
                                                        <option value="medium">Medium</option>
                                                        <option value="hard">Hard</option>
                                                        <option value="mixed">Mixed</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" class="btn btn-danger remove-distribution">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <button type="button" class="btn btn-sm btn-secondary add-distribution mt-2">
                                        <i class="fas fa-plus me-1"></i>Add Question Type
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="card mb-3 section-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Section 1</h6>
                                <button type="button" class="btn btn-sm btn-danger remove-section">
                                    <i class="fas fa-times"></i> Remove
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Section Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="sections[0][title]" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Section Marks <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control section-marks" name="sections[0][marks]" min="1" required>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label">Section Instructions</label>
                                        <textarea class="form-control" name="sections[0][instructions]" rows="2"></textarea>
                                    </div>
                                </div>
                                
                                <h6 class="mb-3">Question Distribution</h6>
                                <div class="question-distribution">
                                    <div class="row mb-2 distribution-row">
                                        <div class="col-md-3">
                                            <select class="form-select" name="sections[0][distribution][0][question_type]" required>
                                                <option value="">Question Type</option>
                                                <option value="mcq">Multiple Choice</option>
                                                <option value="true_false">True/False</option>
                                                <option value="short_answer">Short Answer</option>
                                                <option value="long_answer">Long Answer</option>
                                                <option value="fill_in_the_blank">Fill in the Blank</option>
                                                <option value="matching">Matching</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" class="form-control" name="sections[0][distribution][0][count]" placeholder="Count" min="1" required>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" class="form-control" name="sections[0][distribution][0][marks_per_question]" placeholder="Marks each" min="1" required>
                                        </div>
                                        <div class="col-md-3">
                                            <select class="form-select" name="sections[0][distribution][0][difficulty_level]" required>
                                                <option value="">Difficulty</option>
                                                <option value="easy">Easy</option>
                                                <option value="medium">Medium</option>
                                                <option value="hard">Hard</option>
                                                <option value="mixed">Mixed</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger remove-distribution">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="button" class="btn btn-sm btn-secondary add-distribution mt-2">
                                    <i class="fas fa-plus me-1"></i>Add Question Type
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
                
                <button type="button" id="add_section" class="btn btn-secondary mb-4">
                    <i class="fas fa-plus-circle me-2"></i>Add Section
                </button>
                
                <div class="alert alert-info">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle fa-2x me-3"></i>
                        <div>
                            <strong>Total Marks: <span id="total_marks_sum">0</span></strong>
                            <div id="marks_warning" class="text-danger" style="display: none;">
                                Total section marks do not match the blueprint total marks.
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="reset" class="btn btn-secondary me-md-2">Reset</button>
                    <button type="submit" class="btn btn-primary">Update Blueprint</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sectionsContainer = document.getElementById('sections_container');
        const addSectionBtn = document.getElementById('add_section');
        const totalMarksInput = document.getElementById('total_marks');
        const totalMarksSum = document.getElementById('total_marks_sum');
        const marksWarning = document.getElementById('marks_warning');
        
        // Add section
        addSectionBtn.addEventListener('click', function() {
            const sectionCount = document.querySelectorAll('.section-card').length;
            const newSectionIndex = sectionCount;
            
            const sectionCard = document.createElement('div');
            sectionCard.className = 'card mb-3 section-card';
            
            sectionCard.innerHTML = `
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Section ${newSectionIndex + 1}</h6>
                    <button type="button" class="btn btn-sm btn-danger remove-section">
                        <i class="fas fa-times"></i> Remove
                    </button>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Section Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="sections[${newSectionIndex}][title]" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Section Marks <span class="text-danger">*</span></label>
                            <input type="number" class="form-control section-marks" name="sections[${newSectionIndex}][marks]" min="1" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Section Instructions</label>
                            <textarea class="form-control" name="sections[${newSectionIndex}][instructions]" rows="2"></textarea>
                        </div>
                    </div>
                    
                    <h6 class="mb-3">Question Distribution</h6>
                    <div class="question-distribution">
                        <div class="row mb-2 distribution-row">
                            <div class="col-md-3">
                                <select class="form-select" name="sections[${newSectionIndex}][distribution][0][question_type]" required>
                                    <option value="">Question Type</option>
                                    <option value="mcq">Multiple Choice</option>
                                    <option value="true_false">True/False</option>
                                    <option value="short_answer">Short Answer</option>
                                    <option value="long_answer">Long Answer</option>
                                    <option value="fill_in_the_blank">Fill in the Blank</option>
                                    <option value="matching">Matching</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="number" class="form-control" name="sections[${newSectionIndex}][distribution][0][count]" placeholder="Count" min="1" required>
                            </div>
                            <div class="col-md-2">
                                <input type="number" class="form-control" name="sections[${newSectionIndex}][distribution][0][marks_per_question]" placeholder="Marks each" min="1" required>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" name="sections[${newSectionIndex}][distribution][0][difficulty_level]" required>
                                    <option value="">Difficulty</option>
                                    <option value="easy">Easy</option>
                                    <option value="medium">Medium</option>
                                    <option value="hard">Hard</option>
                                    <option value="mixed">Mixed</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger remove-distribution">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" class="btn btn-sm btn-secondary add-distribution mt-2">
                        <i class="fas fa-plus me-1"></i>Add Question Type
                    </button>
                </div>
            `;
            
            sectionsContainer.appendChild(sectionCard);
            updateSectionNumbers();
            calculateTotalMarks();
        });
        
        // Remove section
        sectionsContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-section') || e.target.parentElement.classList.contains('remove-section')) {
                const button = e.target.closest('.remove-section');
                const sectionCard = button.closest('.section-card');
                
                if (document.querySelectorAll('.section-card').length > 1) {
                    sectionCard.remove();
                    updateSectionNumbers();
                    calculateTotalMarks();
                } else {
                    alert('You must have at least one section.');
                }
            }
        });
        
        // Add distribution row
        sectionsContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('add-distribution') || e.target.parentElement.classList.contains('add-distribution')) {
                const button = e.target.closest('.add-distribution');
                const distributionContainer = button.previousElementSibling;
                const sectionIndex = button.closest('.section-card').getAttribute('data-section-index') || 
                                    Array.from(sectionsContainer.children).indexOf(button.closest('.section-card'));
                const distributionCount = distributionContainer.querySelectorAll('.distribution-row').length;
                
                const distributionRow = document.createElement('div');
                distributionRow.className = 'row mb-2 distribution-row';
                
                distributionRow.innerHTML = `
                    <div class="col-md-3">
                        <select class="form-select" name="sections[${sectionIndex}][distribution][${distributionCount}][question_type]" required>
                            <option value="">Question Type</option>
                            <option value="mcq">Multiple Choice</option>
                            <option value="true_false">True/False</option>
                            <option value="short_answer">Short Answer</option>
                            <option value="long_answer">Long Answer</option>
                            <option value="fill_in_the_blank">Fill in the Blank</option>
                            <option value="matching">Matching</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control" name="sections[${sectionIndex}][distribution][${distributionCount}][count]" placeholder="Count" min="1" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control" name="sections[${sectionIndex}][distribution][${distributionCount}][marks_per_question]" placeholder="Marks each" min="1" required>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="sections[${sectionIndex}][distribution][${distributionCount}][difficulty_level]" required>
                            <option value="">Difficulty</option>
                            <option value="easy">Easy</option>
                            <option value="medium">Medium</option>
                            <option value="hard">Hard</option>
                            <option value="mixed">Mixed</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger remove-distribution">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                
                distributionContainer.appendChild(distributionRow);
            }
        });
        
        // Remove distribution row
        sectionsContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-distribution') || e.target.parentElement.classList.contains('remove-distribution')) {
                const button = e.target.closest('.remove-distribution');
                const distributionRow = button.closest('.distribution-row');
                const distributionContainer = distributionRow.parentElement;
                
                if (distributionContainer.querySelectorAll('.distribution-row').length > 1) {
                    distributionRow.remove();
                    updateDistributionIndices(distributionContainer);
                } else {
                    alert('You must have at least one question type per section.');
                }
            }
        });
        
        // Update section numbers
        function updateSectionNumbers() {
            const sectionCards = document.querySelectorAll('.section-card');
            sectionCards.forEach((card, index) => {
                card.querySelector('.card-header h6').textContent = `Section ${index + 1}`;
                
                // Update section index in all input names
                const inputs = card.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    const name = input.getAttribute('name');
                    if (name) {
                        const newName = name.replace(/sections\[\d+\]/, `sections[${index}]`);
                        input.setAttribute('name', newName);
                    }
                });
                
                // Update distribution indices
                const distributionContainer = card.querySelector('.question-distribution');
                updateDistributionIndices(distributionContainer);
            });
        }
        
        // Update distribution indices
        function updateDistributionIndices(container) {
            const distributionRows = container.querySelectorAll('.distribution-row');
            distributionRows.forEach((row, index) => {
                const inputs = row.querySelectorAll('input, select');
                inputs.forEach(input => {
                    const name = input.getAttribute('name');
                    if (name) {
                        const newName = name.replace(/\[distribution\]\[\d+\]/, `[distribution][${index}]`);
                        input.setAttribute('name', newName);
                    }
                });
            });
        }
        
        // Calculate total marks
        function calculateTotalMarks() {
            const sectionMarksInputs = document.querySelectorAll('.section-marks');
            let sum = 0;
            
            sectionMarksInputs.forEach(input => {
                const value = parseInt(input.value) || 0;
                sum += value;
            });
            
            totalMarksSum.textContent = sum;
            
            // Check if sum matches total marks
            const totalMarksValue = parseInt(totalMarksInput.value) || 0;
            if (sum !== totalMarksValue && sum > 0) {
                marksWarning.style.display = 'block';
            } else {
                marksWarning.style.display = 'none';
            }
        }
        
        // Listen for changes in section marks
        sectionsContainer.addEventListener('input', function(e) {
            if (e.target.classList.contains('section-marks')) {
                calculateTotalMarks();
            }
        });
        
        // Listen for changes in total marks
        totalMarksInput.addEventListener('input', calculateTotalMarks);
        
        // Initial calculation
        calculateTotalMarks();
    });
</script>
@endpush
@endsection
                const distributionContainer = distributionRow.parentElement;
                
                if (distributionContainer.querySelectorAll('.distribution-row').length > 1) {
                    distributionRow.remove();
                    updateDistributionIndices(distributionContainer);
                } else {
                    alert('You must have at least one question type per section.');
                }
            }
        });
        
        // Update section numbers
        function updateSectionNumbers() {
            const sectionCards = document.querySelectorAll('.section-card');
            sectionCards.forEach((card, index) => {
                card.querySelector('.card-header h6').textContent = `Section ${index + 1}`;
                
                // Update section index in all input names
                const inputs = card.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    const name = input.getAttribute('name');
                    if (name) {
                        const newName = name.replace(/sections\[\d+\]/, `sections[${index}]`);
                        input.setAttribute('name', newName);
                    }
                });
                
                // Update distribution indices
                const distributionContainer = card.querySelector('.question-distribution');
                updateDistributionIndices(distributionContainer);
            });
        }
        
        // Update distribution indices
        function updateDistributionIndices(container) {
            const distributionRows = container.querySelectorAll('.distribution-row');
            distributionRows.forEach((row, index) => {
                const inputs = row.querySelectorAll('input, select');
                inputs.forEach(input => {
                    const name = input.getAttribute('name');
                    if (name) {
                        const newName = name.replace(/\[distribution\]\[\d+\]/, `[distribution][${index}]`);
                        input.setAttribute('name', newName);
                    }
                });
            });
        }
        
        // Calculate total marks
        function calculateTotalMarks() {
            const sectionMarksInputs = document.querySelectorAll('.section-marks');
            let sum = 0;
            
            sectionMarksInputs.forEach(input => {
                const value = parseInt(input.value) || 0;
                sum += value;
            });
            
            totalMarksSum.textContent = sum;
            
            // Check if sum matches total marks
            const totalMarksValue = parseInt(totalMarksInput.value) || 0;
            if (sum !== totalMarksValue && sum > 0) {
                marksWarning.style.display = 'block';
            } else {
                marksWarning.style.display = 'none';
            }
        }
        
        // Listen for changes in section marks
        sectionsContainer.addEventListener('input', function(e) {
            if (e.target.classList.contains('section-marks')) {
                calculateTotalMarks();
            }
        });
        
        // Listen for changes in total marks
        totalMarksInput.addEventListener('input', calculateTotalMarks);
        
        // Initial calculation
        calculateTotalMarks();
    });
</script>
@endpush
@endsection
