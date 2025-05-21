@extends('layouts.admin')

@section('title', 'Edit Student')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Edit Student</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back to Students
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Edit Student: {{ $student->name }} ({{ $student->roll_number }})</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.students.update', $student) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="course_id" class="form-label">Course <span class="text-danger">*</span></label>
                        <select class="form-select @error('course_id') is-invalid @enderror" id="course_id" name="course_id" required>
                            <option value="">Select Course</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ old('course_id', $student->course_id) == $course->id ? 'selected' : '' }}>
                                    {{ $course->course_name }} ({{ $course->course_code }})
                                </option>
                            @endforeach
                        </select>
                        @error('course_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="roll_number" class="form-label">Roll Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('roll_number') is-invalid @enderror" id="roll_number" name="roll_number" value="{{ old('roll_number', $student->roll_number) }}" required>
                        @error('roll_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $student->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $student->email) }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $student->phone) }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="year" class="form-label">Year <span class="text-danger">*</span></label>
                        <select class="form-select @error('year') is-invalid @enderror" id="year" name="year" required>
                            <option value="">Select Year</option>
                            @for($i = 1; $i <= 6; $i++)
                                <option value="{{ $i }}" {{ old('year', $student->year) == $i ? 'selected' : '' }}>Year {{ $i }}</option>
                            @endfor
                        </select>
                        @error('year')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="section" class="form-label">Section</label>
                        <input type="text" class="form-control @error('section') is-invalid @enderror" id="section" name="section" value="{{ old('section', $student->section) }}">
                        @error('section')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="has_disability" name="has_disability" value="1" {{ old('has_disability', $student->has_disability) ? 'checked' : '' }}>
                        <label class="form-check-label" for="has_disability">Student has disability</label>
                    </div>
                </div>
                
                <div class="mb-3" id="disability_details_container" style="{{ old('has_disability', $student->has_disability) ? '' : 'display: none;' }}">
                    <label for="disability_details" class="form-label">Disability Details</label>
                    <textarea class="form-control @error('disability_details') is-invalid @enderror" id="disability_details" name="disability_details" rows="3">{{ old('disability_details', $student->disability_details) }}</textarea>
                    @error('disability_details')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $student->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                    <div class="form-text">Inactive students will not be included in seating plans</div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Update Student
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const hasDisabilityCheckbox = document.getElementById('has_disability');
        const disabilityDetailsContainer = document.getElementById('disability_details_container');
        
        hasDisabilityCheckbox.addEventListener('change', function() {
            if (this.checked) {
                disabilityDetailsContainer.style.display = 'block';
            } else {
                disabilityDetailsContainer.style.display = 'none';
            }
        });
    });
</script>
@endsection
@endsection
