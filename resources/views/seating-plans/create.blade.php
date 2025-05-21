@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Generate Seating Plan</h1>
        <a href="{{ route('seating-plans.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Seating Plans
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('seating-plans.store') }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="exam_date" class="form-label">Exam Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('exam_date') is-invalid @enderror" id="exam_date" name="exam_date" value="{{ old('exam_date') }}" required>
                        @error('exam_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="time_slot" class="form-label">Time Slot <span class="text-danger">*</span></label>
                        <select class="form-select @error('time_slot') is-invalid @enderror" id="time_slot" name="time_slot" required>
                            <option value="">Select Time Slot</option>
                            <option value="Morning (9:00 AM - 12:00 PM)" {{ old('time_slot') == 'Morning (9:00 AM - 12:00 PM)' ? 'selected' : '' }}>Morning (9:00 AM - 12:00 PM)</option>
                            <option value="Afternoon (2:00 PM - 5:00 PM)" {{ old('time_slot') == 'Afternoon (2:00 PM - 5:00 PM)' ? 'selected' : '' }}>Afternoon (2:00 PM - 5:00 PM)</option>
                            <option value="Evening (6:00 PM - 9:00 PM)" {{ old('time_slot') == 'Evening (6:00 PM - 9:00 PM)' ? 'selected' : '' }}>Evening (6:00 PM - 9:00 PM)</option>
                        </select>
                        @error('time_slot')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="allocation_method" class="form-label">Allocation Method <span class="text-danger">*</span></label>
                        <select class="form-select @error('allocation_method') is-invalid @enderror" id="allocation_method" name="allocation_method" required>
                            <option value="">Select Method</option>
                            <option value="sequential" {{ old('allocation_method') == 'sequential' ? 'selected' : '' }}>Sequential</option>
                            <option value="random" {{ old('allocation_method') == 'random' ? 'selected' : '' }}>Random</option>
                            <option value="mixed_courses" {{ old('allocation_method') == 'mixed_courses' ? 'selected' : '' }}>Mixed Courses</option>
                            <option value="alternate_roll_numbers" {{ old('allocation_method') == 'alternate_roll_numbers' ? 'selected' : '' }}>Alternate Roll Numbers</option>
                        </select>
                        @error('allocation_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <hr class="my-4">
                <h5 class="mb-3">Select Blocks and Rooms</h5>
                
                <div id="blocks_container">
                    @foreach($blocks as $block)
                        <div class="card mb-3">
                            <div class="card-header">
                                <div class="form-check">
                                    <input class="form-check-input block-checkbox" type="checkbox" id="block_{{ $block->id }}" name="blocks[]" value="{{ $block->id }}" data-block-id="{{ $block->id }}" {{ in_array($block->id, old('blocks', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="block_{{ $block->id }}">
                                        {{ $block->block_name }} ({{ $block->rooms->count() }} rooms)
                                    </label>
                                </div>
                            </div>
                            <div class="card-body" id="rooms_block_{{ $block->id }}" style="{{ in_array($block->id, old('blocks', [])) ? '' : 'display: none;' }}">
                                <div class="row">
                                    @foreach($block->rooms as $room)
                                        <div class="col-md-3 mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input room-checkbox" type="checkbox" id="room_{{ $room->id }}" name="rooms[]" value="{{ $room->id }}" data-block-id="{{ $block->id }}" data-capacity="{{ $room->capacity }}" {{ in_array($room->id, old('rooms', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="room_{{ $room->id }}">
                                                    {{ $room->room_number }} (Capacity: {{ $room->capacity }})
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="d-flex justify-content-between mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary select-all-rooms" data-block-id="{{ $block->id }}">Select All Rooms</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary deselect-all-rooms" data-block-id="{{ $block->id }}">Deselect All Rooms</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <hr class="my-4">
                <h5 class="mb-3">Select Courses and Students</h5>
                
                <div id="courses_container">
                    @foreach($courses as $course)
                        <div class="card mb-3">
                            <div class="card-header">
                                <div class="form-check">
                                    <input class="form-check-input course-checkbox" type="checkbox" id="course_{{ $course->id }}" name="courses[]" value="{{ $course->id }}" data-course-id="{{ $course->id }}" {{ in_array($course->id, old('courses', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="course_{{ $course->id }}">
                                        {{ $course->course_name }} ({{ $course->students->count() }} students)
                                    </label>
                                </div>
                            </div>
                            <div class="card-body" id="students_course_{{ $course->id }}" style="{{ in_array($course->id, old('courses', [])) ? '' : 'display: none;' }}">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-text">Year</span>
                                            <select class="form-select year-filter" data-course-id="{{ $course->id }}">
                                                <option value="">All Years</option>
                                                @foreach($course->students->pluck('year')->unique()->sort() as $year)
                                                    <option value="{{ $year }}">{{ $year }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-text">Section</span>
                                            <select class="form-select section-filter" data-course-id="{{ $course->id }}">
                                                <option value="">All Sections</option>
                                                @foreach($course->students->pluck('section')->unique()->sort() as $section)
                                                    <option value="{{ $section }}">{{ $section }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <input type="text" class="form-control student-search" placeholder="Search students..." data-course-id="{{ $course->id }}">
                                            <button class="btn btn-outline-secondary" type="button">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover student-table" data-course-id="{{ $course->id }}">
                                        <thead>
                                            <tr>
                                                <th style="width: 40px;">
                                                    <div class="form-check">
                                                        <input class="form-check-input select-all-students" type="checkbox" data-course-id="{{ $course->id }}">
                                                    </div>
                                                </th>
                                                <th>Roll Number</th>
                                                <th>Name</th>
                                                <th>Year</th>
                                                <th>Section</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($course->students as $student)
                                                <tr class="student-row" data-year="{{ $student->year }}" data-section="{{ $student->section }}">
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input student-checkbox" type="checkbox" name="students[]" value="{{ $student->id }}" data-course-id="{{ $course->id }}" {{ in_array($student->id, old('students', [])) ? 'checked' : '' }}>
                                                        </div>
                                                    </td>
                                                    <td>{{ $student->roll_number }}</td>
                                                    <td>{{ $student->name }}</td>
                                                    <td>{{ $student->year }}</td>
                                                    <td>{{ $student->section }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="alert alert-info mt-4">
                    <div class="d-flex">
                        <i class="fas fa-info-circle fa-2x me-3"></i>
                        <div>
                            <h5 class="alert-heading">Seating Plan Summary</h5>
                            <p class="mb-0">Selected Blocks: <strong><span id="selected_blocks_count">0</span></strong></p>
                            <p class="mb-0">Selected Rooms: <strong><span id="selected_rooms_count">0</span></strong></p>
                            <p class="mb-0">Total Capacity: <strong><span id="total_capacity">0</span></strong></p>
                            <p class="mb-0">Selected Students: <strong><span id="selected_students_count">0</span></strong></p>
                            <div id="capacity_warning" class="text-danger mt-2" style="display: none;">
                                Warning: The number of selected students exceeds the total room capacity.
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <button type="reset" class="btn btn-secondary me-md-2">Reset</button>
                    <button type="submit" class="btn btn-primary">Generate Seating Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Block checkboxes
        const blockCheckboxes = document.querySelectorAll('.block-checkbox');
        blockCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const blockId = this.getAttribute('data-block-id');
                const roomsContainer = document.getElementById(`rooms_block_${blockId}`);
                
                if (this.checked) {
                    roomsContainer.style.display = 'block';
                } else {
                    roomsContainer.style.display = 'none';
                    
                    // Uncheck all rooms in this block
                    const roomCheckboxes = roomsContainer.querySelectorAll('.room-checkbox');
                    roomCheckboxes.forEach(roomCheckbox => {
                        roomCheckbox.checked = false;
                    });
                }
                
                updateSummary();
            });
        });
        
        // Select/Deselect all rooms buttons
        const selectAllRoomsButtons = document.querySelectorAll('.select-all-rooms');
        selectAllRoomsButtons.forEach(button => {
            button.addEventListener('click', function() {
                const blockId = this.getAttribute('data-block-id');
                const roomCheckboxes = document.querySelectorAll(`.room-checkbox[data-block-id="${blockId}"]`);
                
                roomCheckboxes.forEach(checkbox => {
                    checkbox.checked = true;
                });
                
                updateSummary();
            });
        });
        
        const deselectAllRoomsButtons = document.querySelectorAll('.deselect-all-rooms');
        deselectAllRoomsButtons.forEach(button => {
            button.addEventListener('click', function() {
                const blockId = this.getAttribute('data-block-id');
                const roomCheckboxes = document.querySelectorAll(`.room-checkbox[data-block-id="${blockId}"]`);
                
                roomCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
                
                updateSummary();
            });
        });
        
        // Room checkboxes
        const roomCheckboxes = document.querySelectorAll('.room-checkbox');
        roomCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSummary);
        });
        
        // Course checkboxes
        const courseCheckboxes = document.querySelectorAll('.course-checkbox');
        courseCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const courseId = this.getAttribute('data-course-id');
                const studentsContainer = document.getElementById(`students_course_${courseId}`);
                
                if (this.checked) {
                    studentsContainer.style.display = 'block';
                } else {
                    studentsContainer.style.display = 'none';
                    
                    // Uncheck all students in this course
                    const studentCheckboxes = studentsContainer.querySelectorAll('.student-checkbox');
                    studentCheckboxes.forEach(studentCheckbox => {
                        studentCheckbox.checked = false;
                    });
                }
                
                updateSummary();
            });
        });
        
        // Select all students checkboxes
        const selectAllStudentsCheckboxes = document.querySelectorAll('.select-all-students');
        selectAllStudentsCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const courseId = this.getAttribute('data-course-id');
                const studentCheckboxes = document.querySelectorAll(`.student-checkbox[data-course-id="${courseId}"]`);
                
                studentCheckboxes.forEach(studentCheckbox => {
                    const row = studentCheckbox.closest('tr');
                    if (row.style.display !== 'none') {
                        studentCheckbox.checked = this.checked;
                    }
                });
                
                updateSummary();
            });
        });
        
        // Student checkboxes
        const studentCheckboxes = document.querySelectorAll('.student-checkbox');
        studentCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSummary);
        });
        
        // Year filters
        const yearFilters = document.querySelectorAll('.year-filter');
        yearFilters.forEach(filter => {
            filter.addEventListener('change', function() {
                const courseId = this.getAttribute('data-course-id');
                filterStudents(courseId);
            });
        });
        
        // Section filters
        const sectionFilters = document.querySelectorAll('.section-filter');
        sectionFilters.forEach(filter => {
            filter.addEventListener('change', function() {
                const courseId = this.getAttribute('data-course-id');
                filterStudents(courseId);
            });
        });
        
        // Student search
        const studentSearches = document.querySelectorAll('.student-search');
        studentSearches.forEach(search => {
            search.addEventListener('input', function() {
                const courseId = this.getAttribute('data-course-id');
                filterStudents(courseId);
            });
        });
        
        // Filter students
        function filterStudents(courseId) {
            const yearFilter = document.querySelector(`.year-filter[data-course-id="${courseId}"]`).value;
            const sectionFilter = document.querySelector(`.section-filter[data-course-id="${courseId}"]`).value;
            const searchFilter = document.querySelector(`.student-search[data-course-id="${courseId}"]`).value.toLowerCase();
            
            const studentRows = document.querySelectorAll(`.student-table[data-course-id="${courseId}"] .student-row`);
            
            studentRows.forEach(row => {
                const year = row.getAttribute('data-year');
                const section = row.getAttribute('data-section');
                const rollNumber = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const name = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                
                const yearMatch = !yearFilter || year === yearFilter;
                const sectionMatch = !sectionFilter || section === sectionFilter;
                const searchMatch = !searchFilter || rollNumber.includes(searchFilter) || name.includes(searchFilter);
                
                if (yearMatch && sectionMatch && searchMatch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Update select all checkbox
            updateSelectAllCheckbox(courseId);
        }
        
        // Update select all checkbox
        function updateSelectAllCheckbox(courseId) {
            const selectAllCheckbox = document.querySelector(`.select-all-students[data-course-id="${courseId}"]`);
            const visibleStudentCheckboxes = Array.from(document.querySelectorAll(`.student-checkbox[data-course-id="${courseId}"]`))
                .filter(checkbox => checkbox.closest('tr').style.display !== 'none');
            
            if (visibleStudentCheckboxes.length === 0) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = false;
                return;
            }
            
            const allChecked = visibleStudentCheckboxes.every(checkbox => checkbox.checked);
            const someChecked = visibleStudentCheckboxes.some(checkbox => checkbox.checked);
            
            if (allChecked) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = true;
            } else if (someChecked) {
                selectAllCheckbox.indeterminate = true;
                selectAllCheckbox.checked = false;
            } else {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = false;
            }
        }
        
        // Update summary
        function updateSummary() {
            const selectedBlocksCount = document.querySelectorAll('.block-checkbox:checked').length;
            const selectedRoomsCount = document.querySelectorAll('.room-checkbox:checked').length;
            const selectedStudentsCount = document.querySelectorAll('.student-checkbox:checked').length;
            
            let totalCapacity = 0;
            document.querySelectorAll('.room-checkbox:checked').forEach(checkbox => {
                totalCapacity += parseInt(checkbox.getAttribute('data-capacity')) || 0;
            });
            
            document.getElementById('selected_blocks_count').textContent = selectedBlocksCount;
            document.getElementById('selected_rooms_count').textContent = selectedRoomsCount;
            document.getElementById('total_capacity').textContent = totalCapacity;
            document.getElementById('selected_students_count').textContent = selectedStudentsCount;
            
            // Show warning if students exceed capacity
            const capacityWarning = document.getElementById('capacity_warning');
            if (selectedStudentsCount > totalCapacity && totalCapacity > 0) {
                capacityWarning.style.display = 'block';
            } else {
                capacityWarning.style.display = 'none';
            }
            
            // Update select all checkboxes
            document.querySelectorAll('.course-checkbox:checked').forEach(checkbox => {
                const courseId = checkbox.getAttribute('data-course-id');
                updateSelectAllCheckbox(courseId);
            });
        }
        
        // Initial update
        updateSummary();
    });
</script>
@endpush
@endsection
