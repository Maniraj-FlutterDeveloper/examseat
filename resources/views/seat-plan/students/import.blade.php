@extends('layouts.app')

@section('title', 'Import Students')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-file-import mr-2"></i> Import Students
                    </h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('import_errors'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <h5 class="alert-heading">Import Completed with Errors</h5>
                            <p>The following errors were encountered during import:</p>
                            <ul>
                                @foreach(session('import_errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">Upload File</h6>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('students.process-import') }}" method="POST" enctype="multipart/form-data">
                                        @csrf

                                        <div class="form-group">
                                            <label for="file">Select Excel/CSV File <span class="text-danger">*</span></label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input @error('file') is-invalid @enderror" id="file" name="file" required>
                                                <label class="custom-file-label" for="file">Choose file</label>
                                                @error('file')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <small class="form-text text-muted">Accepted formats: .xlsx, .xls, .csv</small>
                                        </div>

                                        <div class="form-group">
                                            <label for="course_id">Default Course (Optional)</label>
                                            <select class="form-control select2 @error('course_id') is-invalid @enderror" id="course_id" name="course_id">
                                                <option value="">Select Course</option>
                                                @foreach($courses as $course)
                                                    <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                                        {{ $course->name }} ({{ $course->code }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="form-text text-muted">If course is not specified in the file, this course will be used</small>
                                            @error('course_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="header_row" name="header_row" value="1" {{ old('header_row', '1') == '1' ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="header_row">File contains header row</label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="update_existing" name="update_existing" value="1" {{ old('update_existing') == '1' ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="update_existing">Update existing students</label>
                                                <small class="form-text text-muted">If checked, existing students will be updated based on roll number</small>
                                            </div>
                                        </div>

                                        <div class="form-group text-center mt-4">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-upload mr-1"></i> Upload and Process
                                            </button>
                                            <a href="{{ route('students.index') }}" class="btn btn-secondary ml-2">
                                                <i class="fas fa-arrow-left mr-1"></i> Back to List
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">Instructions</h6>
                                </div>
                                <div class="card-body">
                                    <h6>File Format</h6>
                                    <p>Your Excel/CSV file should contain the following columns:</p>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Column</th>
                                                    <th>Description</th>
                                                    <th>Required</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>name</td>
                                                    <td>Full name of the student</td>
                                                    <td><span class="badge badge-success">Yes</span></td>
                                                </tr>
                                                <tr>
                                                    <td>roll_number</td>
                                                    <td>Unique roll number/ID</td>
                                                    <td><span class="badge badge-success">Yes</span></td>
                                                </tr>
                                                <tr>
                                                    <td>course_code</td>
                                                    <td>Course code (e.g., CS101)</td>
                                                    <td><span class="badge badge-warning">Optional</span></td>
                                                </tr>
                                                <tr>
                                                    <td>year</td>
                                                    <td>Year of study (1-5)</td>
                                                    <td><span class="badge badge-success">Yes</span></td>
                                                </tr>
                                                <tr>
                                                    <td>section</td>
                                                    <td>Section (e.g., A, B, C)</td>
                                                    <td><span class="badge badge-warning">Optional</span></td>
                                                </tr>
                                                <tr>
                                                    <td>gender</td>
                                                    <td>Gender (male, female, other)</td>
                                                    <td><span class="badge badge-warning">Optional</span></td>
                                                </tr>
                                                <tr>
                                                    <td>email</td>
                                                    <td>Email address</td>
                                                    <td><span class="badge badge-warning">Optional</span></td>
                                                </tr>
                                                <tr>
                                                    <td>phone</td>
                                                    <td>Phone number</td>
                                                    <td><span class="badge badge-warning">Optional</span></td>
                                                </tr>
                                                <tr>
                                                    <td>has_special_needs</td>
                                                    <td>Has special needs (1 or 0)</td>
                                                    <td><span class="badge badge-warning">Optional</span></td>
                                                </tr>
                                                <tr>
                                                    <td>special_needs_details</td>
                                                    <td>Details about special needs</td>
                                                    <td><span class="badge badge-warning">Optional</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="mt-4">
                                        <h6>Sample File</h6>
                                        <p>You can download a sample file to use as a template:</p>
                                        <a href="{{ route('students.download-sample') }}" class="btn btn-info">
                                            <i class="fas fa-download mr-1"></i> Download Sample File
                                        </a>
                                    </div>

                                    <div class="mt-4">
                                        <h6>Notes</h6>
                                        <ul>
                                            <li>If <strong>course_code</strong> is not provided, the default course selected above will be used.</li>
                                            <li>If <strong>update_existing</strong> is checked, existing students with the same roll number will be updated.</li>
                                            <li>Maximum file size: 5MB</li>
                                            <li>For large imports, the process may take some time.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: 'Select a course',
            allowClear: true
        });
        
        // Show filename when selected
        $('.custom-file-input').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
        });
    });
</script>
@endsection

