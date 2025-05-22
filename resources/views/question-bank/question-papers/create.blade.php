@extends('layouts.question-bank')

@section('title', 'Create Question Paper')

@section('content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('question-bank.question-papers.index') }}">Question Papers</a></li>
        <li class="breadcrumb-item active" aria-current="page">Create from Blueprint</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Create Question Paper from Blueprint</h1>
</div>

<form action="{{ route('question-bank.question-papers.store') }}" method="POST" id="question-paper-form">
    @csrf
    
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4 fade-in">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold">Question Paper Information</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                        <small class="form-text text-muted">A descriptive title for this question paper (e.g., "Midterm Exam - Mathematics 101 - Spring 2023")</small>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        <small class="form-text text-muted">Additional instructions or information about this question paper.</small>
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
                            <small class="form-text text-muted">Leave blank for multi-subject question papers.</small>
                            @error('subject_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="time_limit" class="form-label">Time Limit (minutes)</label>
                            <input type="number" class="form-control @error('time_limit') is-invalid @enderror" id="time_limit" name="time_limit" value="{{ old('time_limit') }}" min="1">
                            <small class="form-text text-muted">The time limit for this question paper in minutes (optional).</small>
                            @error('time_limit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="blueprint_id" class="form-label">Blueprint <span class="text-danger">*</span></label>
                            <select class="form-select @error('blueprint_id') is-invalid @enderror" id="blueprint_id" name="blueprint_id" required>
                                <option value="">Select Blueprint</option>
                                @foreach($blueprints as $blueprint)
                                    <option value="{{ $blueprint->id }}" data-total-marks="{{ $blueprint->total_marks }}" {{ old('blueprint_id', request('blueprint_id')) == $blueprint->id ? 'selected' : '' }}>
                                        {{ $blueprint->title }} ({{ $blueprint->total_marks }} marks)
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">The blueprint to use for generating this question paper.</small>
                            @error('blueprint_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
                                <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                            <small class="form-text text-muted">The current status of this question paper.</small>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="instructions" class="form-label">Instructions for Students</label>
                        <textarea class="form-control @error('instructions') is-invalid @enderror" id="instructions" name="instructions" rows="3">{{ old('instructions') }}</textarea>
                        <small class="form-text text-muted">Instructions that will appear at the top of the question paper.</small>
                        @error('instructions')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="card shadow fade-in">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold">Blueprint Preview</h6>
                </div>
                <div class="card-body">
                    <div id="blueprint-preview">
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i> Select a blueprint to see its details.
                        </div>
                    </div>
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
                        <label class="fw-bold">Blueprint:</label>
                        <p id="summary-blueprint">Not selected</p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Total Marks:</label>
                        <p id="summary-total-marks">0</p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Estimated Questions:</label>
                        <p id="summary-questions">0</p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Generation Method:</label>
                        <p><span class="badge bg-primary">Blueprint-based</span></p>
                    </div>
                </div>
            </div>
            
            <div class="card shadow mb-4 fade-in">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold">Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('question-bank.question-papers.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-magic"></i> Generate Question Paper
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Update blueprint preview when blueprint is selected
        $('#blueprint_id').on('change', function() {
            const blueprintId = $(this).val();
            const blueprintName = $(this).find('option:selected').text();
            const totalMarks = $(this).find('option:selected').data('total-marks') || 0;
            
            // Update summary
            $('#summary-blueprint').text(blueprintId ? blueprintName : 'Not selected');
            $('#summary-total-marks').text(totalMarks);
            $('#summary-questions').text(blueprintId ? 'Loading...' : '0');
            
            if (blueprintId) {
                // Fetch blueprint details
                $.ajax({
                    url: `/api/blueprints/${blueprintId}`,
                    type: 'GET',
                    success: function(data) {
                        // Update blueprint preview
                        let previewHtml = `
                            <div class="mb-3">
                                <h5>${data.title}</h5>
                                <p>${data.description || 'No description available.'}</p>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Filter Type</th>
                                            <th>Filter Value</th>
                                            <th>Questions</th>
                                            <th>Marks per Question</th>
                                            <th>Total Marks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                        `;
                        
                        let totalQuestions = 0;
                        
                        data.conditions.forEach(condition => {
                            previewHtml += `
                                <tr>
                                    <td>${condition.filter_type_label}</td>
                                    <td>${condition.filter_value_label}</td>
                                    <td>${condition.question_count}</td>
                                    <td>${condition.marks_per_question}</td>
                                    <td>${condition.question_count * condition.marks_per_question}</td>
                                </tr>
                            `;
                            
                            totalQuestions += parseInt(condition.question_count);
                        });
                        
                        previewHtml += `
                                    </tbody>
                                    <tfoot>
                                        <tr class="fw-bold">
                                            <td colspan="2">Total</td>
                                            <td>${totalQuestions}</td>
                                            <td></td>
                                            <td>${data.total_marks}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        `;
                        
                        $('#blueprint-preview').html(previewHtml);
                        $('#summary-questions').text(totalQuestions);
                    },
                    error: function() {
                        $('#blueprint-preview').html(`
                            <div class="alert alert-danger mb-0">
                                <i class="fas fa-exclamation-circle me-2"></i> Failed to load blueprint details.
                            </div>
                        `);
                        $('#summary-questions').text('Unknown');
                    }
                });
            } else {
                // Clear blueprint preview
                $('#blueprint-preview').html(`
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i> Select a blueprint to see its details.
                    </div>
                `);
            }
        });
        
        // Trigger change event to load blueprint details if a blueprint is already selected
        if ($('#blueprint_id').val()) {
            $('#blueprint_id').trigger('change');
        }
    });
</script>
@endpush

