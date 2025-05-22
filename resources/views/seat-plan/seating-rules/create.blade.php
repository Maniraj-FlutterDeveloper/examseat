@extends('layouts.app')

@section('title', 'Create Seating Rule')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-plus-circle mr-2"></i> Create New Seating Rule
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('seating-rules.store') }}" method="POST" id="seating-rule-form">
                        @csrf

                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle mr-1"></i> Seating rules define how students are allocated to seats during exam seating plan generation. Rules with higher priority are applied first.
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
                                            <label for="name">Rule Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                            <small class="form-text text-muted">A descriptive name for this rule</small>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="type">Rule Type <span class="text-danger">*</span></label>
                                            <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                                <option value="">Select Rule Type</option>
                                                <option value="course" {{ old('type') == 'course' ? 'selected' : '' }}>Course Rule</option>
                                                <option value="room" {{ old('type') == 'room' ? 'selected' : '' }}>Room Rule</option>
                                                <option value="student" {{ old('type') == 'student' ? 'selected' : '' }}>Student Rule</option>
                                                <option value="invigilator" {{ old('type') == 'invigilator' ? 'selected' : '' }}>Invigilator Rule</option>
                                                <option value="general" {{ old('type') == 'general' ? 'selected' : '' }}>General Rule</option>
                                            </select>
                                            <small class="form-text text-muted">The category this rule belongs to</small>
                                            @error('type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="description">Description <span class="text-danger">*</span></label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" required>{{ old('description') }}</textarea>
                                            <small class="form-text text-muted">Detailed explanation of what this rule does</small>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="priority">Priority <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control @error('priority') is-invalid @enderror" id="priority" name="priority" value="{{ old('priority', 5) }}" min="1" max="10" required>
                                            <small class="form-text text-muted">Higher priority rules (10 is highest) are applied first</small>
                                            @error('priority')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="is_active">Status</label>
                                            <select class="form-control @error('is_active') is-invalid @enderror" id="is_active" name="is_active">
                                                <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                            <small class="form-text text-muted">Only active rules are applied during seating plan generation</small>
                                            @error('is_active')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0">Rule Configuration</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="rule_condition">Condition <span class="text-danger">*</span></label>
                                            <textarea class="form-control @error('rule_condition') is-invalid @enderror" id="rule_condition" name="rule_condition" rows="3" required>{{ old('rule_condition') }}</textarea>
                                            <small class="form-text text-muted">The condition that triggers this rule (e.g., "student.has_special_needs == true")</small>
                                            @error('rule_condition')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="rule_action">Action <span class="text-danger">*</span></label>
                                            <textarea class="form-control @error('rule_action') is-invalid @enderror" id="rule_action" name="rule_action" rows="3" required>{{ old('rule_action') }}</textarea>
                                            <small class="form-text text-muted">The action to take when the condition is met (e.g., "assign_to_accessible_seat")</small>
                                            @error('rule_action')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="parameters">Parameters (JSON)</label>
                                            <textarea class="form-control @error('parameters') is-invalid @enderror" id="parameters" name="parameters" rows="3">{{ old('parameters') }}</textarea>
                                            <small class="form-text text-muted">Additional parameters for this rule in JSON format (e.g., {"min_distance": 2})</small>
                                            @error('parameters')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header bg-warning text-white">
                                <h6 class="mb-0">Rule Templates</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Select a Template (Optional)</label>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="card template-card mb-3" data-type="student" data-condition="student.has_special_needs == true" data-action="assign_to_accessible_seat" data-parameters='{"priority": "high"}'>
                                                        <div class="card-body">
                                                            <h6>Special Needs Students</h6>
                                                            <p class="text-muted small">Assigns students with special needs to accessible seating</p>
                                                            <span class="badge badge-success">Student Rule</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="card template-card mb-3" data-type="course" data-condition="course.students.count > room.capacity" data-action="distribute_across_rooms" data-parameters='{"method": "alphabetical"}'>
                                                        <div class="card-body">
                                                            <h6>Course Overflow</h6>
                                                            <p class="text-muted small">Distributes students across multiple rooms when a course has more students than room capacity</p>
                                                            <span class="badge badge-primary">Course Rule</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="card template-card mb-3" data-type="general" data-condition="true" data-action="alternate_seating" data-parameters='{"pattern": "checkerboard"}'>
                                                        <div class="card-body">
                                                            <h6>Alternate Seating</h6>
                                                            <p class="text-muted small">Creates a checkerboard seating pattern to maximize distance between students</p>
                                                            <span class="badge badge-secondary">General Rule</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="card template-card mb-3" data-type="room" data-condition="room.is_accessible == true" data-action="prioritize_for_special_needs" data-parameters='{}'>
                                                        <div class="card-body">
                                                            <h6>Accessible Room Priority</h6>
                                                            <p class="text-muted small">Prioritizes accessible rooms for students with special needs</p>
                                                            <span class="badge badge-info">Room Rule</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="card template-card mb-3" data-type="invigilator" data-condition="room.capacity > 30" data-action="assign_chief_invigilator" data-parameters='{"min_experience": 2}'>
                                                        <div class="card-body">
                                                            <h6>Chief Invigilator</h6>
                                                            <p class="text-muted small">Assigns a chief invigilator to large rooms with more than 30 students</p>
                                                            <span class="badge badge-warning">Invigilator Rule</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="card template-card mb-3" data-type="student" data-condition="student.course_id == other_student.course_id" data-action="separate_by_distance" data-parameters='{"min_distance": 2}'>
                                                        <div class="card-body">
                                                            <h6>Same Course Separation</h6>
                                                            <p class="text-muted small">Ensures students from the same course are seated at least 2 seats apart</p>
                                                            <span class="badge badge-success">Student Rule</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-center mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i> Create Rule
                            </button>
                            <a href="{{ route('seating-rules.index') }}" class="btn btn-secondary ml-2">
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
    .template-card {
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .template-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    .template-card.selected {
        border: 2px solid #0a2463;
        background-color: #f8f9fa;
    }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Template card selection
        $('.template-card').click(function() {
            $('.template-card').removeClass('selected');
            $(this).addClass('selected');
            
            // Fill form with template data
            $('#type').val($(this).data('type'));
            $('#rule_condition').val($(this).data('condition'));
            $('#rule_action').val($(this).data('action'));
            $('#parameters').val(JSON.stringify($(this).data('parameters'), null, 2));
            
            // Generate a name based on the template
            if ($('#name').val() === '') {
                $('#name').val($(this).find('h6').text());
            }
            
            // Generate a description based on the template
            if ($('#description').val() === '') {
                $('#description').val($(this).find('p').text());
            }
        });
        
        // Validate JSON in parameters field
        $('#seating-rule-form').submit(function(e) {
            var parametersValue = $('#parameters').val().trim();
            
            if (parametersValue !== '') {
                try {
                    JSON.parse(parametersValue);
                } catch (error) {
                    e.preventDefault();
                    alert('Parameters must be valid JSON. Error: ' + error.message);
                    $('#parameters').addClass('is-invalid');
                }
            }
        });
    });
</script>
@endsection

