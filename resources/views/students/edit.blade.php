@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Edit Student</h1>
        <a href="{{ route('students.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Students
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('students.update', $student->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $student->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="roll_number" class="form-label">Roll Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('roll_number') is-invalid @enderror" id="roll_number" name="roll_number" value="{{ old('roll_number', $student->roll_number) }}" required>
                        @error('roll_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
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
                    
                    <div class="col-md-4 mb-3">
                        <label for="year" class="form-label">Year <span class="text-danger">*</span></label>
                        <select class="form-select @error('year') is-invalid @enderror" id="year" name="year" required>
                            <option value="">Select Year</option>
                            @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" {{ old('year', $student->year) == $i ? 'selected' : '' }}>
                                    Year {{ $i }}
                                </option>
                            @endfor
                        </select>
                        @error('year')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="section" class="form-label">Section <span class="text-danger">*</span></label>
                        <select class="form-select @error('section') is-invalid @enderror" id="section" name="section" required>
                            <option value="">Select Section</option>
                            @foreach(['A', 'B', 'C', 'D', 'E'] as $section)
                                <option value="{{ $section }}" {{ old('section', $student->section) == $section ? 'selected' : '' }}>
                                    Section {{ $section }}
                                </option>
                            @endforeach
                        </select>
                        @error('section')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $student->email) }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $student->phone) }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', $student->address) }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input @error('has_special_needs') is-invalid @enderror" id="has_special_needs" name="has_special_needs" value="1" {{ old('has_special_needs', $student->has_special_needs) ? 'checked' : '' }}>
                    <label class="form-check-label" for="has_special_needs">Student has special needs</label>
                    @error('has_special_needs')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3" id="special_needs_details_container" style="{{ old('has_special_needs', $student->has_special_needs) ? '' : 'display: none;' }}">
                    <label for="special_needs_details" class="form-label">Special Needs Details</label>
                    <textarea class="form-control @error('special_needs_details') is-invalid @enderror" id="special_needs_details" name="special_needs_details" rows="3">{{ old('special_needs_details', $student->special_needs_details) }}</textarea>
                    @error('special_needs_details')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="reset" class="btn btn-secondary me-md-2">Reset</button>
                    <button type="submit" class="btn btn-primary">Update Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const hasSpecialNeedsCheckbox = document.getElementById('has_special_needs');
        const specialNeedsDetailsContainer = document.getElementById('special_needs_details_container');
        
        hasSpecialNeedsCheckbox.addEventListener('change', function() {
            if (this.checked) {
                specialNeedsDetailsContainer.style.display = 'block';
            } else {
                specialNeedsDetailsContainer.style.display = 'none';
            }
        });
    });
</script>
@endpush
@endsection

