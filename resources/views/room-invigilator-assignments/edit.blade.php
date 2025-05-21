@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Edit Invigilator Assignment</h1>
        <a href="{{ route('room-invigilator-assignments.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Assignments
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('room-invigilator-assignments.update', $assignment->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="exam_date" class="form-label">Exam Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('exam_date') is-invalid @enderror" id="exam_date" name="exam_date" value="{{ old('exam_date', $assignment->exam_date->format('Y-m-d')) }}" required>
                        @error('exam_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="time_slot" class="form-label">Time Slot <span class="text-danger">*</span></label>
                        <select class="form-select @error('time_slot') is-invalid @enderror" id="time_slot" name="time_slot" required>
                            <option value="">Select Time Slot</option>
                            <option value="Morning (9:00 AM - 12:00 PM)" {{ old('time_slot', $assignment->time_slot) == 'Morning (9:00 AM - 12:00 PM)' ? 'selected' : '' }}>Morning (9:00 AM - 12:00 PM)</option>
                            <option value="Afternoon (2:00 PM - 5:00 PM)" {{ old('time_slot', $assignment->time_slot) == 'Afternoon (2:00 PM - 5:00 PM)' ? 'selected' : '' }}>Afternoon (2:00 PM - 5:00 PM)</option>
                            <option value="Evening (6:00 PM - 9:00 PM)" {{ old('time_slot', $assignment->time_slot) == 'Evening (6:00 PM - 9:00 PM)' ? 'selected' : '' }}>Evening (6:00 PM - 9:00 PM)</option>
                        </select>
                        @error('time_slot')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="is_chief_invigilator" class="form-label">Role <span class="text-danger">*</span></label>
                        <select class="form-select @error('is_chief_invigilator') is-invalid @enderror" id="is_chief_invigilator" name="is_chief_invigilator" required>
                            <option value="0" {{ old('is_chief_invigilator', $assignment->is_chief_invigilator) == '0' ? 'selected' : '' }}>Assistant Invigilator</option>
                            <option value="1" {{ old('is_chief_invigilator', $assignment->is_chief_invigilator) == '1' ? 'selected' : '' }}>Chief Invigilator</option>
                        </select>
                        @error('is_chief_invigilator')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="block_id" class="form-label">Block <span class="text-danger">*</span></label>
                        <select class="form-select @error('block_id') is-invalid @enderror" id="block_id" name="block_id" required>
                            <option value="">Select Block</option>
                            @foreach($blocks as $block)
                                <option value="{{ $block->id }}" {{ old('block_id', $assignment->room->block_id) == $block->id ? 'selected' : '' }}>
                                    {{ $block->block_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('block_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="room_id" class="form-label">Room <span class="text-danger">*</span></label>
                        <select class="form-select @error('room_id') is-invalid @enderror" id="room_id" name="room_id" required>
                            <option value="">Select Room</option>
                            @foreach($rooms->where('block_id', old('block_id', $assignment->room->block_id)) as $room)
                                <option value="{{ $room->id }}" {{ old('room_id', $assignment->room_id) == $room->id ? 'selected' : '' }}>
                                    {{ $room->room_number }} (Capacity: {{ $room->capacity }})
                                </option>
                            @endforeach
                        </select>
                        @error('room_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="department_filter" class="form-label">Filter Invigilators by Department</label>
                        <select class="form-select" id="department_filter">
                            <option value="">All Departments</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept }}" {{ $assignment->invigilator->department == $dept ? 'selected' : '' }}>
                                    {{ $dept }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="invigilator_search" class="form-label">Search Invigilators</label>
                        <input type="text" class="form-control" id="invigilator_search" placeholder="Search by name, email or phone">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="invigilator_id" class="form-label">Select Invigilator <span class="text-danger">*</span></label>
                    <select class="form-select @error('invigilator_id') is-invalid @enderror" id="invigilator_id" name="invigilator_id" required>
                        <option value="">Select Invigilator</option>
                        @foreach($invigilators as $invigilator)
                            <option value="{{ $invigilator->id }}" data-department="{{ $invigilator->department }}" {{ old('invigilator_id', $assignment->invigilator_id) == $invigilator->id ? 'selected' : '' }}>
                                {{ $invigilator->name }} - {{ $invigilator->department }} ({{ $invigilator->designation }})
                            </option>
                        @endforeach
                    </select>
                    @error('invigilator_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $assignment->notes) }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="reset" class="btn btn-secondary me-md-2">Reset</button>
                    <button type="submit" class="btn btn-primary">Update Assignment</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const blockSelect = document.getElementById('block_id');
        const roomSelect = document.getElementById('room_id');
        const departmentFilter = document.getElementById('department_filter');
        const invigilatorSearch = document.getElementById('invigilator_search');
        const invigilatorSelect = document.getElementById('invigilator_id');
        
        // Load rooms when block changes
        blockSelect.addEventListener('change', function() {
            const blockId = this.value;
            
            // Clear room select
            roomSelect.innerHTML = '<option value="">Select Room</option>';
            roomSelect.disabled = !blockId;
            
            if (blockId) {
                // Fetch rooms for the selected block
                fetch(`/api/blocks/${blockId}/rooms`)
                    .then(response => response.json())
                    .then(rooms => {
                        rooms.forEach(room => {
                            const option = document.createElement('option');
                            option.value = room.id;
                            option.textContent = `${room.room_number} (Capacity: ${room.capacity})`;
                            roomSelect.appendChild(option);
                        });
                    });
            }
        });
        
        // Filter invigilators by department
        departmentFilter.addEventListener('change', filterInvigilators);
        
        // Filter invigilators by search term
        invigilatorSearch.addEventListener('input', filterInvigilators);
        
        function filterInvigilators() {
            const department = departmentFilter.value;
            const searchTerm = invigilatorSearch.value.toLowerCase();
            
            // Get all options except the first one (placeholder)
            const options = Array.from(invigilatorSelect.options).slice(1);
            
            options.forEach(option => {
                const optionDepartment = option.getAttribute('data-department');
                const optionText = option.textContent.toLowerCase();
                
                const departmentMatch = !department || optionDepartment === department;
                const searchMatch = !searchTerm || optionText.includes(searchTerm);
                
                option.style.display = departmentMatch && searchMatch ? '' : 'none';
            });
        }
    });
</script>
@endpush
@endsection
