@extends('layouts.app')

@section('title', 'Create Room')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-plus-circle mr-2"></i> Create New Room
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('rooms.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="block_id">Block <span class="text-danger">*</span></label>
                                    <select class="form-control @error('block_id') is-invalid @enderror" id="block_id" name="block_id" required>
                                        <option value="">Select Block</option>
                                        @foreach($blocks as $block)
                                            <option value="{{ $block->id }}" {{ old('block_id') == $block->id ? 'selected' : '' }}>
                                                {{ $block->name }} ({{ $block->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('block_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="floor">Floor</label>
                                    <input type="text" class="form-control @error('floor') is-invalid @enderror" id="floor" name="floor" value="{{ old('floor') }}">
                                    <small class="form-text text-muted">Floor number or name (e.g., Ground, First, G, 1, 2)</small>
                                    @error('floor')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Room Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code">Room Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" required>
                                    <small class="form-text text-muted">A unique code for the room (e.g., R101, R102)</small>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="capacity">Capacity <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('capacity') is-invalid @enderror" id="capacity" name="capacity" value="{{ old('capacity') }}" min="1" required>
                                    <small class="form-text text-muted">Maximum number of students that can be seated</small>
                                    @error('capacity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="rows">Rows</label>
                                    <input type="number" class="form-control @error('rows') is-invalid @enderror" id="rows" name="rows" value="{{ old('rows') }}" min="1">
                                    <small class="form-text text-muted">Number of rows in the seating layout</small>
                                    @error('rows')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="columns">Columns</label>
                                    <input type="number" class="form-control @error('columns') is-invalid @enderror" id="columns" name="columns" value="{{ old('columns') }}" min="1">
                                    <small class="form-text text-muted">Number of columns in the seating layout</small>
                                    @error('columns')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="has_projector">Has Projector</label>
                                    <select class="form-control @error('has_projector') is-invalid @enderror" id="has_projector" name="has_projector">
                                        <option value="1" {{ old('has_projector') == '1' ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ old('has_projector', '0') == '0' ? 'selected' : '' }}>No</option>
                                    </select>
                                    @error('has_projector')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="has_computer">Has Computer</label>
                                    <select class="form-control @error('has_computer') is-invalid @enderror" id="has_computer" name="has_computer">
                                        <option value="1" {{ old('has_computer') == '1' ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ old('has_computer', '0') == '0' ? 'selected' : '' }}>No</option>
                                    </select>
                                    @error('has_computer')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="is_active">Status</label>
                                    <select class="form-control @error('is_active') is-invalid @enderror" id="is_active" name="is_active">
                                        <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('is_active')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            <small class="form-text text-muted">Additional information about the room</small>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group text-center mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i> Create Room
                            </button>
                            <a href="{{ route('rooms.index') }}" class="btn btn-secondary ml-2">
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

@section('scripts')
<script>
    $(document).ready(function() {
        // Auto-generate code from block and name
        $('#name, #block_id').on('change', function() {
            if ($('#code').val() === '') {
                let blockId = $('#block_id').val();
                let name = $('#name').val().trim();
                
                if (blockId && name) {
                    let blockCode = $('#block_id option:selected').text().match(/\(([^)]+)\)/)[1];
                    let roomNumber = name.replace(/[^0-9]/g, '');
                    
                    if (roomNumber) {
                        $('#code').val(blockCode + '-' + roomNumber);
                    }
                }
            }
        });
        
        // Update capacity when rows and columns change
        $('#rows, #columns').on('change', function() {
            let rows = parseInt($('#rows').val()) || 0;
            let columns = parseInt($('#columns').val()) || 0;
            
            if (rows > 0 && columns > 0) {
                $('#capacity').val(rows * columns);
            }
        });
    });
</script>
@endsection

