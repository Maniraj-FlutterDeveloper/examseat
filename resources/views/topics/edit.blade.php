@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Edit Topic</h1>
        <a href="{{ route('topics.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Topics
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('topics.update', $topic->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6 mb-3">
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
                    
                    <div class="col-md-6 mb-3">
                        <label for="unit_id" class="form-label">Unit <span class="text-danger">*</span></label>
                        <select class="form-select @error('unit_id') is-invalid @enderror" id="unit_id" name="unit_id" required>
                            <option value="">Select Unit</option>
                            @foreach($units->where('subject_id', old('subject_id', $topic->unit->subject_id)) as $unit)
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
                    <label for="topic_number" class="form-label">Topic Number <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('topic_number') is-invalid @enderror" id="topic_number" name="topic_number" value="{{ old('topic_number', $topic->topic_number) }}" min="1" required>
                    @error('topic_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $topic->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="reset" class="btn btn-secondary me-md-2">Reset</button>
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
                fetch(`/api/subjects/${subjectId}/units`)
                    .then(response => response.json())
                    .then(units => {
                        units.forEach(unit => {
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

