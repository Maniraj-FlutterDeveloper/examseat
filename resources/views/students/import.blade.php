@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Import Students</h1>
        <div>
            <a href="{{ route('students.template') }}" class="btn btn-info me-2">
                <i class="fas fa-download me-2"></i>Download Template
            </a>
            <a href="{{ route('students.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Students
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Upload Student Data</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('students.import.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="file" class="form-label">Excel File <span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" accept=".xlsx, .xls, .csv" required>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Upload Excel file (.xlsx, .xls) or CSV file (.csv) containing student data.
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="course_id" class="form-label">Default Course <span class="text-danger">*</span></label>
                            <select class="form-select @error('course_id') is-invalid @enderror" id="course_id" name="course_id" required>
                                <option value="">Select Course</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->course_name }} ({{ $course->course_code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('course_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                This course will be used if the course is not specified in the Excel file.
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input @error('update_existing') is-invalid @enderror" type="checkbox" id="update_existing" name="update_existing" value="1" {{ old('update_existing') ? 'checked' : '' }}>
                                <label class="form-check-label" for="update_existing">
                                    Update existing students if roll number already exists
                                </label>
                                @error('update_existing')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="reset" class="btn btn-secondary me-md-2">Reset</button>
                            <button type="submit" class="btn btn-primary">Import Students</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Import Instructions</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="fw-bold">Step 1: Download Template</h6>
                        <p>Download the Excel template using the button above.</p>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="fw-bold">Step 2: Fill in the Data</h6>
                        <p>Fill in the student data in the Excel template. The following columns are required:</p>
                        <ul>
                            <li>Roll Number (required)</li>
                            <li>Name (required)</li>
                            <li>Year (required, 1-5)</li>
                            <li>Section (required, A-E)</li>
                            <li>Email (optional)</li>
                            <li>Phone (optional)</li>
                            <li>Address (optional)</li>
                            <li>Has Special Needs (optional, Yes/No)</li>
                            <li>Special Needs Details (optional)</li>
                        </ul>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="fw-bold">Step 3: Upload the File</h6>
                        <p>Upload the filled Excel file and select the default course.</p>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> The system will validate all data before importing. If any row has invalid data, the entire import will fail.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

