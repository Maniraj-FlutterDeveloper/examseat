@extends('layouts.app')

@section('title', 'Edit Seating Plan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-edit mr-2"></i> Edit Seating Plan: {{ $seatingPlan->title }}
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('seating-plans.update', $seatingPlan) }}" method="POST" id="seating-plan-form">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> 
                                    <strong>Warning:</strong> Editing a seating plan may affect existing student and invigilator assignments. 
                                    @if($seatingPlan->status == 'published')
                                        This seating plan is already published. Changes may require re-notification of students and invigilators.
                                    @endif
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
                                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $seatingPlan->title) }}" required>
                                            <small class="form-text text-muted">E.g., "Midterm Examination - Fall 2023"</small>
                                            @error('title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exam_code">Exam Code</label>
                                            <input type="text" class="form-control @error('exam_code') is-invalid @enderror" id="exam_code" name="exam_code" value="{{ old('exam_code', $seatingPlan->exam_code) }}">
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
                                            <input type="date" class="form-control @error('exam_date') is-invalid @enderror" id="exam_date" name="exam_date" value="{{ old('exam_date', $seatingPlan->exam_date) }}" required>
                                            @error('exam_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="start_time">Start Time <span class="text-danger">*</span></label>
                                            <input type="time" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" value="{{ old('start_time', date('H:i', strtotime($seatingPlan->start_time))) }}" required>
                                            @error('start_time')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="end_time">End Time <span class="text-danger">*</span></label>
                                            <input type="time" class="form-control @error('end_time') is-invalid @enderror" id="end_time" name="end_time" value="{{ old('end_time', date('H:i', strtotime($seatingPlan->end_time))) }}" required>
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
                                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $seatingPlan->description) }}</textarea>
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
                                                                <input type="radio" id="allocation_method_course" name="allocation_method" class="custom-control-input" value="course" {{ old('allocation_method', $seatingPlan->allocation_method) == 'course' ? 'checked' : '' }} required>
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
                                                                <input type="radio" id="allocation_method_mixed" name="allocation_method" class="custom-control-input" value="mixed" {{ old('allocation_method', $seatingPlan->allocation_method) == 'mixed' ? 'checked' : '' }} required>
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
                                                                <input type="radio" id="allocation_method_manual" name="allocation_method" class="custom-control-input" value="manual" {{ old('allocation_method', $seatingPlan->allocation_method) == 'manual' ? 'checked' : '' }} required>
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
                                                <option value="0" {{ old('seating_gap', $seatingPlan->seating_gap) == '0' ? 'selected' : '' }}>No Gap (Every Seat)</option>
                                                <option value="1" {{ old('seating_gap', $seatingPlan->seating_gap) == '1' ? 'selected' : '' }}>1 Seat Gap (Alternate Seating)</option>
                                                <option value="2" {{ old('seating_gap', $seatingPlan->seating_gap) == '2' ? 'selected' : '' }}>2 Seat Gap</option>
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
                                                <option value="prioritize" {{ old('special_needs_handling', $seatingPlan->special_needs_handling) == 'prioritize' ? 'selected' : '' }}>Prioritize Accessible Seating</option>
                                                <option value="separate" {{ old('special_needs_handling', $seatingPlan->special_needs_handling) == 'separate' ? 'selected' : '' }}>Separate Room for Special Needs</option>
                                                <option value="ignore" {{ old('special_needs_handling', $seatingPlan->special_needs_handling) == 'ignore' ? 'selected' : '' }}>No Special Handling</option>
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
                                                <input type="checkbox" class="custom-control-input" id="auto_assign_invigilators" name="auto_assign_invigilators" value="1" {{ old('auto_assign_invigilators', $seatingPlan->auto_assign_invigilators) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="auto_assign_invigilators">Auto-assign Invigilators</label>
                                            </div>
                                            <small class="form-text text-muted">Automatically assign invigilators based on room capacity</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="notify_students" name="notify_students" value="1" {{ old('notify_students') ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="notify_students">Notify Students of Changes</label>
                                            </div>
                                            <small class="form-text text-muted">Send email notifications to students about changes to their seating assignments</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="notify_invigilators" name="notify_invigilators" value="1" {{ old('notify_invigilators') ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="notify_invigilators">Notify Invigilators of Changes</label>
                                            </div>
                                            <small class="form-text text-muted">Send email notifications to invigilators about changes to their duty assignments</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status">
                                                <option value="draft" {{ old('status', $seatingPlan->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                                <option value="published" {{ old('status', $seatingPlan->status) == 'published' ? 'selected' : '' }}>Published</option>
                                                <option value="completed" {{ old('status', $seatingPlan->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                                <option value="cancelled" {{ old('status', $seatingPlan->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                            </select>
                                            <small class="form-text text-muted">Current status of the seating plan</small>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-center mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i> Update Seating Plan
                            </button>
                            <a href="{{ route('seating-plans.show', $seatingPlan) }}" class="btn btn-info ml-2">
                                <i class="fas fa-eye mr-1"></i> View Details
                            </a>
                            <a href="{{ route('seating-plans.index') }}" class="btn btn-secondary ml-2">
                                <i class="fas fa-arrow-left mr-1"></i> Back to List
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
        
        // Show warning when changing allocation method
        $('input[name="allocation_method"]').change(function() {
            var originalMethod = '{{ $seatingPlan->allocation_method }}';
            var newMethod = $(this).val();
            
            if (originalMethod !== newMethod && '{{ $seatingPlan->status }}' !== 'draft') {
                alert('Warning: Changing the allocation method may require re-generating all seating assignments. This could affect students who have already been notified.');
            }
        });
    });
</script>
@endsection

