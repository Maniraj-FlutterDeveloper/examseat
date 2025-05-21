@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Seating Plan Details</h1>
        <div>
            <a href="{{ route('seating-plans.download', $seatingPlan->id) }}" class="btn btn-success me-2">
                <i class="fas fa-download me-2"></i>Download PDF
            </a>
            <a href="{{ route('seating-plans.edit', $seatingPlan->id) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('seating-plans.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Seating Plans
            </a>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Seating Plan Information</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th style="width: 40%">ID:</th>
                            <td>{{ $seatingPlan->id }}</td>
                        </tr>
                        <tr>
                            <th>Exam Date:</th>
                            <td>{{ $seatingPlan->exam_date->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <th>Time Slot:</th>
                            <td>{{ $seatingPlan->time_slot }}</td>
                        </tr>
                        <tr>
                            <th>Allocation Method:</th>
                            <td>{{ ucfirst(str_replace('_', ' ', $seatingPlan->allocation_method)) }}</td>
                        </tr>
                        <tr>
                            <th>Blocks:</th>
                            <td>{{ $blocks->count() }}</td>
                        </tr>
                        <tr>
                            <th>Rooms:</th>
                            <td>{{ $rooms->count() }}</td>
                        </tr>
                        <tr>
                            <th>Students:</th>
                            <td>{{ $assignments->count() }}</td>
                        </tr>
                        <tr>
                            <th>Created At:</th>
                            <td>{{ $seatingPlan->created_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At:</th>
                            <td>{{ $seatingPlan->updated_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Summary by Course</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Course</th>
                                    <th>Students</th>
                                    <th>Blocks</th>
                                    <th>Rooms</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($courseStats as $courseStat)
                                    <tr>
                                        <td>{{ $courseStat['course_name'] }}</td>
                                        <td>{{ $courseStat['student_count'] }}</td>
                                        <td>{{ $courseStat['block_count'] }}</td>
                                        <td>{{ $courseStat['room_count'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Room Assignments</h5>
            <div>
                <button type="button" class="btn btn-sm btn-outline-primary" id="expandAllRooms">Expand All</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="collapseAllRooms">Collapse All</button>
            </div>
        </div>
        <div class="card-body">
            <div class="accordion" id="roomsAccordion">
                @foreach($blocks as $block)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="blockHeading{{ $block->id }}">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#blockCollapse{{ $block->id }}" aria-expanded="false" aria-controls="blockCollapse{{ $block->id }}">
                                <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                    <span>{{ $block->block_name }}</span>
                                    <span class="badge bg-primary">{{ $block->rooms->count() }} Rooms</span>
                                </div>
                            </button>
                        </h2>
                        <div id="blockCollapse{{ $block->id }}" class="accordion-collapse collapse" aria-labelledby="blockHeading{{ $block->id }}" data-bs-parent="#roomsAccordion">
                            <div class="accordion-body p-0">
                                <div class="list-group list-group-flush">
                                    @foreach($block->rooms as $room)
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="mb-0">Room: {{ $room->room_number }}</h6>
                                                <span class="badge bg-info">Capacity: {{ $room->capacity }}</span>
                                            </div>
                                            
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Seat No.</th>
                                                            <th>Roll Number</th>
                                                            <th>Name</th>
                                                            <th>Course</th>
                                                            <th>Year</th>
                                                            <th>Section</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $roomAssignments = $assignments->where('room_id', $room->id)->sortBy('seat_number');
                                                        @endphp
                                                        
                                                        @if($roomAssignments->count() > 0)
                                                            @foreach($roomAssignments as $assignment)
                                                                <tr>
                                                                    <td>{{ $assignment->seat_number }}</td>
                                                                    <td>{{ $assignment->student->roll_number }}</td>
                                                                    <td>{{ $assignment->student->name }}</td>
                                                                    <td>{{ $assignment->student->course->course_name }}</td>
                                                                    <td>{{ $assignment->student->year }}</td>
                                                                    <td>{{ $assignment->student->section }}</td>
                                                                </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="6" class="text-center">No students assigned to this room.</td>
                                                            </tr>
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                            
                                            <div class="d-flex justify-content-between align-items-center mt-2">
                                                <span class="text-muted">Total Students: {{ $roomAssignments->count() }}</span>
                                                <div>
                                                    <a href="{{ route('room-invigilator-assignments.create', ['room_id' => $room->id, 'exam_date' => $seatingPlan->exam_date->format('Y-m-d'), 'time_slot' => $seatingPlan->time_slot]) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-user-tie me-1"></i>Assign Invigilator
                                                    </a>
                                                    <a href="{{ route('seating-plans.print-room', ['id' => $seatingPlan->id, 'room_id' => $room->id]) }}" class="btn btn-sm btn-outline-success">
                                                        <i class="fas fa-print me-1"></i>Print
                                                    </a>
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
    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Student Search</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" class="form-control" id="studentSearch" placeholder="Search by roll number or name...">
                        <button class="btn btn-primary" type="button" id="searchButton">
                            <i class="fas fa-search me-1"></i>Search
                        </button>
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="courseFilter">
                        <option value="">All Courses</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-outline-secondary w-100" type="button" id="clearSearch">
                        <i class="fas fa-times me-1"></i>Clear
                    </button>
                </div>
            </div>
            
            <div id="searchResults" style="display: none;">
                <div class="table-responsive">
                    <table class="table table-striped" id="resultsTable">
                        <thead>
                            <tr>
                                <th>Roll Number</th>
                                <th>Name</th>
                                <th>Course</th>
                                <th>Block</th>
                                <th>Room</th>
                                <th>Seat Number</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Search results will be populated here -->
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div id="noResults" class="text-center py-4" style="display: none;">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <p>No students found matching your search criteria.</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Expand/Collapse all rooms
        document.getElementById('expandAllRooms').addEventListener('click', function() {
            document.querySelectorAll('.accordion-collapse').forEach(item => {
                item.classList.add('show');
                const button = document.querySelector(`[data-bs-target="#${item.id}"]`);
                button.classList.remove('collapsed');
                button.setAttribute('aria-expanded', 'true');
            });
        });
        
        document.getElementById('collapseAllRooms').addEventListener('click', function() {
            document.querySelectorAll('.accordion-collapse').forEach(item => {
                item.classList.remove('show');
                const button = document.querySelector(`[data-bs-target="#${item.id}"]`);
                button.classList.add('collapsed');
                button.setAttribute('aria-expanded', 'false');
            });
        });
        
        // Student search
        const studentSearch = document.getElementById('studentSearch');
        const courseFilter = document.getElementById('courseFilter');
        const searchButton = document.getElementById('searchButton');
        const clearSearch = document.getElementById('clearSearch');
        const searchResults = document.getElementById('searchResults');
        const noResults = document.getElementById('noResults');
        const resultsTable = document.getElementById('resultsTable').querySelector('tbody');
        
        // All assignments data
        const assignments = @json($assignments->map(function($assignment) {
            return [
                'id' => $assignment->id,
                'student_id' => $assignment->student_id,
                'roll_number' => $assignment->student->roll_number,
                'student_name' => $assignment->student->name,
                'course_id' => $assignment->student->course_id,
                'course_name' => $assignment->student->course->course_name,
                'block_id' => $assignment->room->block_id,
                'block_name' => $assignment->room->block->block_name,
                'room_id' => $assignment->room_id,
                'room_number' => $assignment->room->room_number,
                'seat_number' => $assignment->seat_number
            ];
        }));
        
        // Search function
        function performSearch() {
            const searchTerm = studentSearch.value.toLowerCase();
            const courseId = courseFilter.value;
            
            let results = assignments;
            
            if (searchTerm) {
                results = results.filter(assignment => 
                    assignment.roll_number.toLowerCase().includes(searchTerm) || 
                    assignment.student_name.toLowerCase().includes(searchTerm)
                );
            }
            
            if (courseId) {
                results = results.filter(assignment => assignment.course_id == courseId);
            }
            
            // Display results
            if (results.length > 0) {
                resultsTable.innerHTML = '';
                
                results.forEach(assignment => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${assignment.roll_number}</td>
                        <td>${assignment.student_name}</td>
                        <td>${assignment.course_name}</td>
                        <td>${assignment.block_name}</td>
                        <td>${assignment.room_number}</td>
                        <td>${assignment.seat_number}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-info locate-student" 
                                data-block-id="${assignment.block_id}" 
                                data-room-id="${assignment.room_id}">
                                <i class="fas fa-map-marker-alt"></i> Locate
                            </button>
                        </td>
                    `;
                    resultsTable.appendChild(row);
                });
                
                searchResults.style.display = 'block';
                noResults.style.display = 'none';
            } else {
                searchResults.style.display = 'none';
                noResults.style.display = 'block';
            }
        }
        
        // Search button click
        searchButton.addEventListener('click', performSearch);
        
        // Enter key in search input
        studentSearch.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
        
        // Course filter change
        courseFilter.addEventListener('change', performSearch);
        
        // Clear search
        clearSearch.addEventListener('click', function() {
            studentSearch.value = '';
            courseFilter.value = '';
            searchResults.style.display = 'none';
            noResults.style.display = 'none';
        });
        
        // Locate student
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('locate-student') || e.target.parentElement.classList.contains('locate-student')) {
                const button = e.target.closest('.locate-student');
                const blockId = button.getAttribute('data-block-id');
                const roomId = button.getAttribute('data-room-id');
                
                // Expand the block accordion
                const blockCollapse = document.getElementById(`blockCollapse${blockId}`);
                blockCollapse.classList.add('show');
                
                const blockButton = document.querySelector(`[data-bs-target="#blockCollapse${blockId}"]`);
                blockButton.classList.remove('collapsed');
                blockButton.setAttribute('aria-expanded', 'true');
                
                // Scroll to the room
                const roomElement = blockCollapse.querySelector(`.list-group-item:has(table tbody tr td:contains("${roomId}"))`);
                if (roomElement) {
                    roomElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    roomElement.classList.add('highlight');
                    
                    setTimeout(() => {
                        roomElement.classList.remove('highlight');
                    }, 3000);
                }
            }
        });
    });
</script>

<style>
    .highlight {
        background-color: rgba(255, 243, 205, 0.5);
        transition: background-color 1s ease;
    }
</style>
@endpush
@endsection
