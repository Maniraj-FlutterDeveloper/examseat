@extends('layouts.admin')

@section('title', 'Import Students')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Import Students</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back to Students
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Upload Student Data</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.students.import.process') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="course_id" class="form-label">Course <span class="text-danger">*</span></label>
                            <select class="form-select @error('course_id') is-invalid @enderror" id="course_id" name="course_id" required>
                                <option value="">Select Course</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ old('course_id', request('course_id')) == $course->id ? 'selected' : '' }}>
                                        {{ $course->course_name }} ({{ $course->course_code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('course_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">All imported students will be assigned to this course</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="year" class="form-label">Year <span class="text-danger">*</span></label>
                            <select class="form-select @error('year') is-invalid @enderror" id="year" name="year" required>
                                <option value="">Select Year</option>
                                @for($i = 1; $i <= 6; $i++)
                                    <option value="{{ $i }}" {{ old('year') == $i ? 'selected' : '' }}>Year {{ $i }}</option>
                                @endfor
                            </select>
                            @error('year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">All imported students will be assigned to this year</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="section" class="form-label">Section</label>
                            <input type="text" class="form-control @error('section') is-invalid @enderror" id="section" name="section" value="{{ old('section') }}">
                            @error('section')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">All imported students will be assigned to this section (optional)</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="file" class="form-label">CSV File <span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" accept=".csv, .xlsx" required>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Upload a CSV or Excel file with student data</div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="header_row" name="header_row" value="1" {{ old('header_row', '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="header_row">File contains header row</label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Set all students as active</label>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="reset" class="btn btn-outline-secondary">Reset</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-file-import me-2"></i> Import Students
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Import Instructions</h5>
                </div>
                <div class="card-body">
                    <h6>File Format</h6>
                    <p>The CSV or Excel file should contain the following columns:</p>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Column</th>
                                    <th>Description</th>
                                    <th>Required</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Roll Number</td>
                                    <td>Unique identifier for the student</td>
                                    <td><span class="badge bg-success">Yes</span></td>
                                </tr>
                                <tr>
                                    <td>Name</td>
                                    <td>Full name of the student</td>
                                    <td><span class="badge bg-success">Yes</span></td>
                                </tr>
                                <tr>
                                    <td>Email</td>
                                    <td>Email address of the student</td>
                                    <td><span class="badge bg-secondary">No</span></td>
                                </tr>
                                <tr>
                                    <td>Phone</td>
                                    <td>Phone number of the student</td>
                                    <td><span class="badge bg-secondary">No</span></td>
                                </tr>
                                <tr>
                                    <td>Has Disability</td>
                                    <td>1 for Yes, 0 for No</td>
                                    <td><span class="badge bg-secondary">No</span></td>
                                </tr>
                                <tr>
                                    <td>Disability Details</td>
                                    <td>Details about the disability</td>
                                    <td><span class="badge bg-secondary">No</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <h6 class="mt-4">Sample CSV Format</h6>
                    <pre class="bg-light p-2 rounded"><code>Roll Number,Name,Email,Phone,Has Disability,Disability Details
B001,John Doe,john@example.com,1234567890,0,
B002,Jane Smith,jane@example.com,9876543210,1,Requires wheelchair access
B003,Bob Johnson,bob@example.com,5555555555,0,</code></pre>
                    
                    <div class="mt-3">
                        <a href="{{ route('admin.students.import.template') }}" class="btn btn-outline-primary">
                            <i class="fas fa-download me-2"></i> Download Template
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
