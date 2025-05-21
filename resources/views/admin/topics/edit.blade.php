@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Edit Topic</h1>
        <a href="{{ route('admin.topics.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Topics
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.topics.update', $topic->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="subject_id" class="form-label">Subject <span class="text-danger">*</span></label>
                        <select class="form-select @error('subject_id') is-invalid @enderror" id="subject_id" name="subject_id" required>
                            <option value="">Select Subject</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id', $topic->unit->subject_id) == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->subject_name }} ({{ $subject->subject_code }})
                                </option>
                            @endforeach
                        </select>
                        @error('subject_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="unit_id" class="form-label">Unit <span class="text-danger">*</span></label>
                        <select class="form-select @error('unit_id') is-invalid @enderror" id="unit_id" name="unit_id" required>
                            <option value="">Select Unit</option>
                            @foreach($units->where('subject_id', $topic->unit->subject_id) as $unit)
                                <option value="{{ $unit->id }}" {{ old('unit_id', $topic->unit_id) == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->unit_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('unit_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="topic_name" class="form-label">Topic Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('topic_name') is-invalid @enderror" id="topic_name" name="topic_name" value="{{ old('topic_name', $topic->topic_name) }}" required>
                    @error('topic_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="topic_code" class="form-label">Topic Code</label>
                    <input type="text" class="form-control @error('topic_code') is-invalid @enderror" id="topic_code" name="topic_code" value="{{ old('topic_code', $topic->topic_code) }}">
                    @error('topic_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Enter a code for this topic (e.g., TOPIC1, EQUATIONS)</small>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $topic->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="order" class="form-label">Order</label>
                    <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', $topic->order) }}" min="0">
                    @error('order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Topics will be displayed in ascending order (0, 1, 2, ...)</small>
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $topic->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('admin.topics.index') }}" class="btn btn-light me-md-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Topic</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const subjectSelect = document.getElementById('subject_id');
        const unitSelect = document.getElementById('unit_id');
        
        subjectSelect.addEventListener('change', function() {
            const subjectId = this.value;
            
            // Clear unit select
            unitSelect.innerHTML = '<option value="">Select Unit</option>';
            
            if (subjectId) {
                // Fetch units for the selected subject
                fetch(`{{ url('admin/units/by-subject') }}/${subjectId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(unit => {
                            const option = document.createElement('option');
                            option.value = unit.id;
                            option.textContent = unit.unit_name;
                            unitSelect.appendChild(option);
                        });
                    });
            }
        });
    });
</script>
@endpush
@endsection

