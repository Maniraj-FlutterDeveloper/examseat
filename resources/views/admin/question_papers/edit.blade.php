@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Edit Question Paper</h1>
        <div>
            <a href="{{ route('admin.question_papers.show', $questionPaper->id) }}" class="btn btn-info me-2">
                <i class="fas fa-eye me-2"></i>View
            </a>
            <a href="{{ route('admin.question_papers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Question Papers
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <form action="{{ route('admin.question_papers.update', $questionPaper->id) }}" method="POST" id="editForm">
                @csrf
                @method('PUT')
                
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Question Paper Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Question Paper Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $questionPaper->title) }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="subject_id" class="form-label">Subject</label>
                                    <input type="text" class="form-control" value="{{ $questionPaper->subject->subject_name }}" readonly>
                                    <input type="hidden" name="subject_id" value="{{ $questionPaper->subject_id }}">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="total_marks" class="form-label">Total Marks</label>
                                    <input type="number" class="form-control" value="{{ $questionPaper->total_marks }}" readonly>
                                    <input type="hidden" name="total_marks" value="{{ $questionPaper->total_marks }}">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="exam_date" class="form-label">Exam Date</label>
                                    <input type="date" class="form-control @error('exam_date') is-invalid @enderror" id="exam_date" name="exam_date" value="{{ old('exam_date', $questionPaper->exam_date ? $questionPaper->exam_date->format('Y-m-d') : '') }}">
                                    @error('exam_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="duration" class="form-label">Duration (minutes)</label>
                                    <input type="number" class="form-control" value="{{ $questionPaper->duration }}" readonly>
                                    <input type="hidden" name="duration" value="{{ $questionPaper->duration }}">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="additional_instructions" class="form-label">Additional Instructions</label>
                                    <textarea class="form-control @error('additional_instructions') is-invalid @enderror" id="additional_instructions" name="additional_instructions" rows="3">{{ old('additional_instructions', $questionPaper->additional_instructions) }}</textarea>
                                    @error('additional_instructions')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Question Paper Options</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="shuffle_questions" name="shuffle_questions" value="1" {{ old('shuffle_questions', $questionPaper->shuffle_questions) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="shuffle_questions">
                                            Shuffle Questions Within Sections
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="include_answer_key" name="include_answer_key" value="1" {{ old('include_answer_key', $questionPaper->include_answer_key) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="include_answer_key">
                                            Include Answer Key
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="include_marking_scheme" name="include_marking_scheme" value="1" {{ old('include_marking_scheme', $questionPaper->include_marking_scheme) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="include_marking_scheme">
                                            Include Marking Scheme
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Questions</h5>
                        <div>
                            <button type="button" class="btn btn-sm btn-primary" id="regenerateBtn">
                                <i class="fas fa-sync-alt me-1"></i>Regenerate Questions
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-4">
                            <i class="fas fa-info-circle me-2"></i>
                            You can reorder questions by dragging them, or replace individual questions by clicking the "Replace" button.
                        </div>
                        
                        <div id="sectionsContainer">
                            @foreach($questionPaper->sections as $sectionIndex => $section)
                                <div class="section-card mb-4" data-section-id="{{ $section->id }}">
                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <h6 class="card-title mb-0">{{ $section->title }} ({{ $section->total_marks }} marks)</h6>
                                        </div>
                                        <div class="card-body">
                                            @if($section->instructions)
                                                <div class="mb-3">
                                                    <p class="text-muted small">{{ $section->instructions }}</p>
                                                </div>
                                            @endif
                                            
                                            <div class="questions-container" data-section-id="{{ $section->id }}">
                                                @foreach($section->questions as $questionIndex => $question)
                                                    <div class="question-card mb-3" data-question-id="{{ $question->id }}">
                                                        <div class="card">
                                                            <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                                                                <h6 class="card-title mb-0">Question {{ $questionIndex + 1 }} ({{ $question->marks }} marks)</h6>
                                                                <div>
                                                                    <button type="button" class="btn btn-sm btn-outline-primary replace-question-btn" data-question-id="{{ $question->id }}" data-section-id="{{ $section->id }}">
                                                                        <i class="fas fa-exchange-alt"></i> Replace
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="question-text mb-2">
                                                                            {!! $question->question_text !!}
                                                                        </div>
                                                                        
                                                                        @if($question->question_type == 'mcq')
                                                                            <div class="options-list">
                                                                                @foreach(json_decode($question->options) as $optionIndex => $option)
                                                                                    <div class="form-check">
                                                                                        <input class="form-check-input" type="radio" disabled>
                                                                                        <label class="form-check-label">
                                                                                            {{ $option }}
                                                                                        </label>
                                                                                    </div>
                                                                                @endforeach
                                                                            </div>
                                                                        @elseif($question->question_type == 'true_false')
                                                                            <div class="options-list">
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input" type="radio" disabled>
                                                                                    <label class="form-check-label">True</label>
                                                                                </div>
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input" type="radio" disabled>
                                                                                    <label class="form-check-label">False</label>
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                        
                                                                        <div class="question-meta mt-2">
                                                                            <small class="text-muted">
                                                                                <span class="badge bg-secondary">{{ ucfirst($question->question_type) }}</span>
                                                                                <span class="badge bg-secondary">{{ ucfirst($question->difficulty_level) }}</span>
                                                                                @if($question->blooms_taxonomy)
                                                                                    <span class="badge bg-secondary">{{ $question->blooms_taxonomy->level_name }}</span>
                                                                                @endif
                                                                                @if($question->topic)
                                                                                    <span class="badge bg-secondary">{{ $question->topic->topic_name }}</span>
                                                                                @endif
                                                                            </small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Question Paper
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Replace Question Modal -->
<div class="modal fade" id="replaceQuestionModal" tabindex="-1" aria-labelledby="replaceQuestionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="replaceQuestionModalLabel">Replace Question</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="replacementQuestionsContainer">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3">Loading alternative questions...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- Regenerate Questions Modal -->
<div class="modal fade" id="regenerateModal" tabindex="-1" aria-labelledby="regenerateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="regenerateModalLabel">Regenerate Questions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to regenerate all questions in this paper? This will replace all current questions with new ones based on the original blueprint or settings.</p>
                
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="keepStructure" checked>
                    <label class="form-check-label" for="keepStructure">
                        Keep the same section structure
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmRegenerateBtn">
                    <i class="fas fa-sync-alt me-2"></i>Regenerate
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize rich text editor for instructions if available
        if (typeof ClassicEditor !== 'undefined') {
            ClassicEditor.create(document.getElementById('additional_instructions'))
                .catch(error => {
                    console.error(error);
                });
        }
        
        // Initialize sortable for each questions container
        document.querySelectorAll('.questions-container').forEach(container => {
            new Sortable(container, {
                animation: 150,
                handle: '.card-header',
                ghostClass: 'bg-light',
                onEnd: function(evt) {
                    updateQuestionOrder(evt.from);
                }
            });
        });
        
        // Update question order after drag and drop
        function updateQuestionOrder(container) {
            const sectionId = container.dataset.sectionId;
            const questionIds = Array.from(container.querySelectorAll('.question-card')).map(card => card.dataset.questionId);
            
            // Send AJAX request to update question order
            fetch('{{ route("admin.question_papers.update_question_order") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    section_id: sectionId,
                    question_ids: questionIds
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update question numbers
                    container.querySelectorAll('.question-card').forEach((card, index) => {
                        card.querySelector('.card-title').textContent = `Question ${index + 1} (${card.querySelector('.card-title').textContent.split('(')[1]}`;
                    });
                } else {
                    console.error('Error updating question order:', data.message);
                }
            })
            .catch(error => {
                console.error('Error updating question order:', error);
            });
        }
        
        // Replace question button click handler
        const replaceQuestionModal = new bootstrap.Modal(document.getElementById('replaceQuestionModal'));
        const replacementQuestionsContainer = document.getElementById('replacementQuestionsContainer');
        
        document.querySelectorAll('.replace-question-btn').forEach(button => {
            button.addEventListener('click', function() {
                const questionId = this.dataset.questionId;
                const sectionId = this.dataset.sectionId;
                
                // Show modal
                replaceQuestionModal.show();
                
                // Fetch alternative questions
                fetch(`{{ url('admin/questions/alternatives') }}/${questionId}?section_id=${sectionId}`)
                    .then(response => response.json())
                    .then(data => {
                        displayAlternativeQuestions(data, questionId, sectionId);
                    })
                    .catch(error => {
                        console.error('Error fetching alternative questions:', error);
                        replacementQuestionsContainer.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                Error loading alternative questions. Please try again.
                            </div>
                        `;
                    });
            });
        });
        
        // Display alternative questions
        function displayAlternativeQuestions(questions, currentQuestionId, sectionId) {
            if (questions.length === 0) {
                replacementQuestionsContainer.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        No alternative questions found that match the criteria.
                    </div>
                `;
                return;
            }
            
            let questionsHtml = '<div class="list-group">';
            
            questions.forEach(question => {
                questionsHtml += `
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">${question.marks} marks</h6>
                            <button type="button" class="btn btn-sm btn-primary select-question-btn" data-question-id="${question.id}" data-current-question-id="${currentQuestionId}" data-section-id="${sectionId}">
                                Select
                            </button>
                        </div>
                        <div class="question-text mb-2">
                            ${question.question_text}
                        </div>
                `;
                
                if (question.question_type === 'mcq' && question.options) {
                    questionsHtml += '<div class="options-list">';
                    const options = JSON.parse(question.options);
                    options.forEach(option => {
                        questionsHtml += `
                            <div class="form-check">
                                <input class="form-check-input" type="radio" disabled>
                                <label class="form-check-label">
                                    ${option}
                                </label>
                            </div>
                        `;
                    });
                    questionsHtml += '</div>';
                } else if (question.question_type === 'true_false') {
                    questionsHtml += `
                        <div class="options-list">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" disabled>
                                <label class="form-check-label">True</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" disabled>
                                <label class="form-check-label">False</label>
                            </div>
                        </div>
                    `;
                }
                
                questionsHtml += `
                        <div class="question-meta mt-2">
                            <small class="text-muted">
                                <span class="badge bg-secondary">${question.question_type.charAt(0).toUpperCase() + question.question_type.slice(1)}</span>
                                <span class="badge bg-secondary">${question.difficulty_level.charAt(0).toUpperCase() + question.difficulty_level.slice(1)}</span>
                                ${question.blooms_taxonomy ? `<span class="badge bg-secondary">${question.blooms_taxonomy.level_name}</span>` : ''}
                                ${question.topic ? `<span class="badge bg-secondary">${question.topic.topic_name}</span>` : ''}
                            </small>
                        </div>
                    </div>
                `;
            });
            
            questionsHtml += '</div>';
            replacementQuestionsContainer.innerHTML = questionsHtml;
            
            // Add event listeners for select question buttons
            document.querySelectorAll('.select-question-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const newQuestionId = this.dataset.questionId;
                    const currentQuestionId = this.dataset.currentQuestionId;
                    const sectionId = this.dataset.sectionId;
                    
                    replaceQuestion(currentQuestionId, newQuestionId, sectionId);
                });
            });
        }
        
        // Replace question
        function replaceQuestion(currentQuestionId, newQuestionId, sectionId) {
            fetch('{{ route("admin.question_papers.replace_question") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    current_question_id: currentQuestionId,
                    new_question_id: newQuestionId,
                    section_id: sectionId,
                    question_paper_id: {{ $questionPaper->id }}
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close modal
                    replaceQuestionModal.hide();
                    
                    // Reload page to show updated question
                    window.location.reload();
                } else {
                    console.error('Error replacing question:', data.message);
                    alert('Error replacing question: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error replacing question:', error);
                alert('Error replacing question. Please try again.');
            });
        }
        
        // Regenerate questions button click handler
        const regenerateModal = new bootstrap.Modal(document.getElementById('regenerateModal'));
        const regenerateBtn = document.getElementById('regenerateBtn');
        const confirmRegenerateBtn = document.getElementById('confirmRegenerateBtn');
        
        regenerateBtn.addEventListener('click', function() {
            regenerateModal.show();
        });
        
        confirmRegenerateBtn.addEventListener('click', function() {
            const keepStructure = document.getElementById('keepStructure').checked;
            
            // Send AJAX request to regenerate questions
            fetch('{{ route("admin.question_papers.regenerate_questions", $questionPaper->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    keep_structure: keepStructure
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close modal
                    regenerateModal.hide();
                    
                    // Reload page to show updated questions
                    window.location.reload();
                } else {
                    console.error('Error regenerating questions:', data.message);
                    alert('Error regenerating questions: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error regenerating questions:', error);
                alert('Error regenerating questions. Please try again.');
            });
        });
    });
</script>
@endpush
@endsection
