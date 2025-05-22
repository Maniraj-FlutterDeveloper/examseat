@extends('layouts.app')

@section('title', 'Create Seating Plan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-plus-circle mr-2"></i> Create New Seating Plan
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('seating-plans.store') }}" method="POST" id="seating-plan-form">
                        @csrf

                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle mr-1"></i> Fill in the basic information about the exam and select the seating allocation method. You'll be able to configure room and student details in the next steps.
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">Basic Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="title">Exam Title <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                                            <small class="form-text text-muted">E.g., "Midterm Examination - Fall 2023"</small>
                                            @error('title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exam_code">Exam Code</label>
                                            <input type="text" class="form-control @error('exam_code') is-invalid @enderror" id="exam_code" name="exam_code" value="{{ old('exam_code') }}">
                                            <small class="form-text text-muted">Optional unique identifier for this exam</small>
                                            @error('exam_code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="exam_date">Exam Date <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control @error('exam_date') is-invalid @enderror" id="exam_date" name="exam_date" value="{{ old('exam_date', date('Y-m-d')) }}" required>
                                            @error('exam_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="start_time">Start Time <span class="text-danger">*</span></label>
                                            <input type="time" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" value="{{ old('start_time', '09:00') }}" required>
                                            @error('start_time')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="end_time">End Time <span class="text-danger">*</span></label>
                                            <input type="time" class="form-control @error('end_time') is-invalid @enderror" id="end_time" name="end_time" value="{{ old('end_time', '12:00') }}" required>
                                            @error('end_time')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="description">Description</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                            <small class="form-text text-muted">Additional details about this examination</small>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0">Seating Allocation Method</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Select Allocation Method <span class="text-danger">*</span></label>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="card allocation-method-card mb-3">
                                                        <div class="card-body">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" id="allocation_method_course" name="allocation_method" class="custom-control-input" value="course" {{ old('allocation_method', 'course') == 'course' ? 'checked' : '' }} required>
                                                                <label class="custom-control-label" for="allocation_method_course">
                                                                    <h6>Course-based Allocation</h6>
                                                                </label>
                                                            </div>
                                                            <p class="text-muted small mt-2">
                                                                Students from the same course will be seated together. You'll select specific courses in the next step.
                                                            </p>
                                                            <div class="text-center mt-2">
                                                                <i class="fas fa-users fa-2x text-primary"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="card allocation-method-card mb-3">
                                                        <div class="card-body">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" id="allocation_method_mixed" name="allocation_method" class="custom-control-input" value="mixed" {{ old('allocation_method') == 'mixed' ? 'checked' : '' }} required>
                                                                <label class="custom-control-label" for="allocation_method_mixed">
                                                                    <h6>Mixed Allocation</h6>
                                                                </label>
                                                            </div>
                                                            <p class="text-muted small mt-2">
                                                                Students from different courses will be mixed to minimize cheating. Alternate seating patterns will be used.
                                                            </p>
                                                            <div class="text-center mt-2">
                                                                <i class="fas fa-random fa-2x text-success"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="card allocation-method-card mb-3">
                                                        <div class="card-body">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" id="allocation_method_manual" name="allocation_method" class="custom-control-input" value="manual" {{ old('allocation_method') == 'manual' ? 'checked' : '' }} required>
                                                                <label class="custom-control-label" for="allocation_method_manual">
                                                                    <h6>Manual Allocation</h6>
                                                                </label>
                                                            </div>
                                                            <p class="text-muted small mt-2">
                                                                You'll manually assign students to specific seats. This gives you complete control over the seating arrangement.
                                                            </p>
                                                            <div class="text-center mt-2">
                                                                <i class="fas fa-hand-pointer fa-2x text-warning"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @error('allocation_method')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="seating_gap">Seating Gap</label>
                                            <select class="form-control @error('seating_gap') is-invalid @enderror" id="seating_gap" name="seating_gap">
                                                <option value="0" {{ old('seating_gap', '1') == '0' ? 'selected' : '' }}>No Gap (Every Seat)</option>
                                                <option value="1" {{ old('seating_gap', '1') == '1' ? 'selected' : '' }}>1 Seat Gap (Alternate Seating)</option>
                                                <option value="2" {{ old('seating_gap', '1') == '2' ? 'selected' : '' }}>2 Seat Gap</option>
                                            </select>
                                            <small class="form-text text-muted">Number of empty seats between students</small>
                                            @error('seating_gap')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="special_needs_handling">Special Needs Handling</label>
                                            <select class="form-control @error('special_needs_handling') is-invalid @enderror" id="special_needs_handling" name="special_needs_handling">
                                                <option value="prioritize" {{ old('special_needs_handling', 'prioritize') == 'prioritize' ? 'selected' : '' }}>Prioritize Accessible Seating</option>
                                                <option value="separate" {{ old('special_needs_handling', 'prioritize') == 'separate' ? 'selected' : '' }}>Separate Room for Special Needs</option>
                                                <option value="ignore" {{ old('special_needs_handling', 'prioritize') == 'ignore' ? 'selected' : '' }}>No Special Handling</option>
                                            </select>
                                            <small class="form-text text-muted">How to handle students with special needs</small>
                                            @error('special_needs_handling')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header bg-warning text-white">
                                <h6 class="mb-0">Additional Settings</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="auto_assign_invigilators" name="auto_assign_invigilators" value="1" {{ old('auto_assign_invigilators') ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="auto_assign_invigilators">Auto-assign Invigilators</label>
                                            </div>
                                            <small class="form-text text-muted">Automatically assign invigilators based on room capacity</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="notify_students" name="notify_students" value="1" {{ old('notify_students') ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="notify_students">Notify Students</label>
                                            </div>
                                            <small class="form-text text-muted">Send email notifications to students about their seating assignments</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="notify_invigilators" name="notify_invigilators" value="1" {{ old('notify_invigilators') ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="notify_invigilators">Notify Invigilators</label>
                                            </div>
                                            <small class="form-text text-muted">Send email notifications to invigilators about their duty assignments</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="publish_immediately" name="publish_immediately" value="1" {{ old('publish_immediately') ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="publish_immediately">Publish Immediately</label>
                                            </div>
                                            <small class="form-text text-muted">Publish the seating plan immediately after creation (otherwise saved as draft)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-center mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-arrow-right mr-1"></i> Continue to Next Step
                            </button>
                            <a href="{{ route('seating-plans.index') }}" class="btn btn-secondary ml-2">
                                <i class="fas fa-times mr-1"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .allocation-method-card {
        height: 100%;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .allocation-method-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    .custom-control-input:checked ~ .custom-control-label::before {
        border-color: #0a2463;
        background-color: #0a2463;
    }
    
    .custom-radio .custom-control-input:checked ~ .custom-control-label::after {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e");
    }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Make the entire card clickable for allocation method
        $('.allocation-method-card').click(function() {
            $(this).find('input[type="radio"]').prop('checked', true);
        });
        
        // Validate end time is after start time
        $('#seating-plan-form').submit(function(e) {
            var startTime = $('#start_time').val();
            var endTime = $('#end_time').val();
            
            if (startTime >= endTime) {
                e.preventDefault();
                alert('End time must be after start time');
                $('#end_time').addClass('is-invalid');
            }
        });
        
        // Auto-generate exam code
        $('#title').blur(function() {
            if ($('#exam_code').val() === '') {
                let title = $(this).val().trim();
                if (title) {
                    // Create a code from the first letters of each word
                    let code = title.split(' ').map(word => word.charAt(0).toUpperCase()).join('');
                    
                    // Add date
                    let date = new Date($('#exam_date').val());
                    let month = date.getMonth() + 1;
                    let year = date.getFullYear().toString().substr(-2);
                    
                    $('#exam_code').val(code + '-' + month + year);
                }
            }
        });
    });
</script>
@endsection

