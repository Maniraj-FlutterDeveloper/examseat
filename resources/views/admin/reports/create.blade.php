@extends('layouts.admin')

@section('title', 'Create Report')

@section('styles')
<style>
    .parameter-group {
        display: none;
    }
    
    .parameter-group.active {
        display: block;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Create New Report</h1>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Back to Reports
        </a>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Report Details</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.reports.store') }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Report Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="type" class="form-label">Report Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                            <option value="">Select Report Type</option>
                            @foreach($types as $key => $value)
                                <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_favorite" name="is_favorite" {{ old('is_favorite') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_favorite">
                            Add to Favorites
                        </label>
                    </div>
                </div>
                
                <hr>
                
                <!-- Parameters Section -->
                <h5 class="mb-3">Report Parameters</h5>
                
                <!-- Exam Statistics Parameters -->
                <div class="parameter-group" id="exam_statistics_params">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="date_range" class="form-label">Date Range</label>
                            <select class="form-select" id="date_range" name="parameters[date_range]">
                                <option value="all_time" {{ old('parameters.date_range') == 'all_time' ? 'selected' : '' }}>All Time</option>
                                <option value="this_month" {{ old('parameters.date_range') == 'this_month' ? 'selected' : '' }}>This Month</option>
                                <option value="last_month" {{ old('parameters.date_range') == 'last_month' ? 'selected' : '' }}>Last Month</option>
                                <option value="this_year" {{ old('parameters.date_range') == 'this_year' ? 'selected' : '' }}>This Year</option>
                                <option value="last_year" {{ old('parameters.date_range') == 'last_year' ? 'selected' : '' }}>Last Year</option>
                                <option value="custom" {{ old('parameters.date_range') == 'custom' ? 'selected' : '' }}>Custom</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="include_charts" class="form-label">Include Charts</label>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="include_charts" name="parameters[include_charts]" value="1" {{ old('parameters.include_charts') ? 'checked' : '' }}>
                                <label class="form-check-label" for="include_charts">
                                    Include visual charts in the report
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Seating Plan Parameters -->
                <div class="parameter-group" id="seating_plan_params">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="block_id" class="form-label">Block</label>
                            <select class="form-select" id="block_id" name="parameters[block_id]">
                                <option value="">All Blocks</option>
                                <!-- Blocks would be populated here -->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="room_id" class="form-label">Room</label>
                            <select class="form-select" id="room_id" name="parameters[room_id]">
                                <option value="">All Rooms</option>
                                <!-- Rooms would be populated here -->
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Question Paper Parameters -->
                <div class="parameter-group" id="question_paper_params">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="subject_id" class="form-label">Subject</label>
                            <select class="form-select" id="subject_id" name="parameters[subject_id]">
                                <option value="">All Subjects</option>
                                <!-- Subjects would be populated here -->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="difficulty_level" class="form-label">Difficulty Level</label>
                            <select class="form-select" id="difficulty_level" name="parameters[difficulty_level]">
                                <option value="">All Difficulty Levels</option>
                                <option value="easy" {{ old('parameters.difficulty_level') == 'easy' ? 'selected' : '' }}>Easy</option>
                                <option value="medium" {{ old('parameters.difficulty_level') == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="hard" {{ old('parameters.difficulty_level') == 'hard' ? 'selected' : '' }}>Hard</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Student Performance Parameters -->
                <div class="parameter-group" id="student_performance_params">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="course_id" class="form-label">Course</label>
                            <select class="form-select" id="course_id" name="parameters[course_id]">
                                <option value="">All Courses</option>
                                <!-- Courses would be populated here -->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="year" class="form-label">Year</label>
                            <select class="form-select" id="year" name="parameters[year]">
                                <option value="">All Years</option>
                                <option value="1" {{ old('parameters.year') == '1' ? 'selected' : '' }}>Year 1</option>
                                <option value="2" {{ old('parameters.year') == '2' ? 'selected' : '' }}>Year 2</option>
                                <option value="3" {{ old('parameters.year') == '3' ? 'selected' : '' }}>Year 3</option>
                                <option value="4" {{ old('parameters.year') == '4' ? 'selected' : '' }}>Year 4</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Custom Report Parameters -->
                <div class="parameter-group" id="custom_params">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Custom reports allow you to define your own parameters. Add them as needed.
                    </div>
                    <div id="custom_parameters_container">
                        <!-- Custom parameters would be added here dynamically -->
                    </div>
                    <button type="button" class="btn btn-sm btn-secondary mt-2" id="add_custom_param">
                        <i class="fas fa-plus-circle me-1"></i> Add Parameter
                    </button>
                </div>
                
                <hr>
                
                <!-- Schedule Section -->
                <h5 class="mb-3">Report Schedule (Optional)</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="schedule_type" class="form-label">Schedule Type</label>
                        <select class="form-select" id="schedule_type" name="schedule[type]">
                            <option value="">No Schedule</option>
                            <option value="daily" {{ old('schedule.type') == 'daily' ? 'selected' : '' }}>Daily</option>
                            <option value="weekly" {{ old('schedule.type') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                            <option value="monthly" {{ old('schedule.type') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="schedule_time" class="form-label">Time</label>
                        <input type="time" class="form-control" id="schedule_time" name="schedule[time]" value="{{ old('schedule.time') }}">
                    </div>
                </div>
                
                <div class="mb-3 schedule-options" id="weekly_options" style="display: none;">
                    <label class="form-label">Day of Week</label>
                    <div class="row">
                        @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $index => $day)
                            <div class="col-md-3 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="schedule[day_of_week]" id="day_{{ $index + 1 }}" value="{{ $index + 1 }}" {{ old('schedule.day_of_week') == ($index + 1) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="day_{{ $index + 1 }}">
                                        {{ $day }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="mb-3 schedule-options" id="monthly_options" style="display: none;">
                    <label class="form-label">Day of Month</label>
                    <select class="form-select" name="schedule[day_of_month]">
                        @for($i = 1; $i <= 31; $i++)
                            <option value="{{ $i }}" {{ old('schedule.day_of_month') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                
                <div class="d-flex justify-content-end mt-4">
                    <button type="button" class="btn btn-secondary me-2" onclick="window.history.back();">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Report</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show/hide parameter groups based on report type
        const typeSelect = document.getElementById('type');
        const paramGroups = document.querySelectorAll('.parameter-group');
        
        function updateParameterGroups() {
            paramGroups.forEach(group => {
                group.classList.remove('active');
            });
            
            const selectedType = typeSelect.value;
            if (selectedType) {
                const targetGroup = document.getElementById(selectedType + '_params');
                if (targetGroup) {
                    targetGroup.classList.add('active');
                }
            }
        }
        
        typeSelect.addEventListener('change', updateParameterGroups);
        updateParameterGroups(); // Initial update
        
        // Show/hide schedule options based on schedule type
        const scheduleTypeSelect = document.getElementById('schedule_type');
        const weeklyOptions = document.getElementById('weekly_options');
        const monthlyOptions = document.getElementById('monthly_options');
        
        function updateScheduleOptions() {
            weeklyOptions.style.display = 'none';
            monthlyOptions.style.display = 'none';
            
            const selectedScheduleType = scheduleTypeSelect.value;
            if (selectedScheduleType === 'weekly') {
                weeklyOptions.style.display = 'block';
            } else if (selectedScheduleType === 'monthly') {
                monthlyOptions.style.display = 'block';
            }
        }
        
        scheduleTypeSelect.addEventListener('change', updateScheduleOptions);
        updateScheduleOptions(); // Initial update
        
        // Add custom parameter functionality
        const addCustomParamBtn = document.getElementById('add_custom_param');
        const customParamsContainer = document.getElementById('custom_parameters_container');
        let paramCount = 0;
        
        addCustomParamBtn.addEventListener('click', function() {
            const paramRow = document.createElement('div');
            paramRow.className = 'row mb-3 custom-param-row';
            paramRow.innerHTML = `
                <div class="col-md-5">
                    <input type="text" class="form-control" name="parameters[custom][${paramCount}][name]" placeholder="Parameter Name" required>
                </div>
                <div class="col-md-5">
                    <input type="text" class="form-control" name="parameters[custom][${paramCount}][value]" placeholder="Parameter Value">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm remove-param">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            customParamsContainer.appendChild(paramRow);
            
            // Add event listener to remove button
            paramRow.querySelector('.remove-param').addEventListener('click', function() {
                paramRow.remove();
            });
            
            paramCount++;
        });
    });
</script>
@endpush

