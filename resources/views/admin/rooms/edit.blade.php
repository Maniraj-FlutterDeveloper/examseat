@extends('layouts.admin')

@section('title', 'Edit Room')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Edit Room</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.rooms.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back to Rooms
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Edit Room: {{ $room->room_number }} ({{ $room->block->block_name }})</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.rooms.update', $room) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="block_id" class="form-label">Block <span class="text-danger">*</span></label>
                        <select class="form-select @error('block_id') is-invalid @enderror" id="block_id" name="block_id" required>
                            <option value="">Select Block</option>
                            @foreach($blocks as $block)
                                <option value="{{ $block->id }}" {{ old('block_id', $room->block_id) == $block->id ? 'selected' : '' }}>
                                    {{ $block->block_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('block_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="room_number" class="form-label">Room Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('room_number') is-invalid @enderror" id="room_number" name="room_number" value="{{ old('room_number', $room->room_number) }}" required>
                        @error('room_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="capacity" class="form-label">Capacity <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('capacity') is-invalid @enderror" id="capacity" name="capacity" value="{{ old('capacity', $room->capacity) }}" min="1" required>
                        @error('capacity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="rows" class="form-label">Rows <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('rows') is-invalid @enderror" id="rows" name="rows" value="{{ old('rows', $room->rows) }}" min="1" required>
                        @error('rows')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="columns" class="form-label">Columns <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('columns') is-invalid @enderror" id="columns" name="columns" value="{{ old('columns', $room->columns) }}" min="1" required>
                        @error('columns')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Note: Rows × Columns should equal Capacity</div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $room->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $room->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                    <div class="form-text">Inactive rooms will not be used for seating allocation</div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('admin.rooms.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Update Room
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    // Auto-calculate capacity based on rows and columns
    document.addEventListener('DOMContentLoaded', function() {
        const rowsInput = document.getElementById('rows');
        const columnsInput = document.getElementById('columns');
        const capacityInput = document.getElementById('capacity');
        
        function updateCapacity() {
            const rows = parseInt(rowsInput.value) || 0;
            const columns = parseInt(columnsInput.value) || 0;
            if (rows > 0 && columns > 0) {
                capacityInput.value = rows * columns;
            }
        }
        
        rowsInput.addEventListener('input', updateCapacity);
        columnsInput.addEventListener('input', updateCapacity);
    });
</script>
@endsection
@endsection
