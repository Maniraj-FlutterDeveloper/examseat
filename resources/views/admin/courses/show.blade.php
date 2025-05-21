@extends('layouts.admin')

@section('title', 'Course Details')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Course Details</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back to Courses
            </a>
            <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i> Edit Course
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Course Information</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th style="width: 30%">Course Name:</th>
                            <td>{{ $course->course_name }}</td>
                        </tr>
                        <tr>
                            <th>Course Code:</th>
                            <td>{{ $course->course_code }}</td>
                        </tr>
                        <tr>
                            <th>Duration:</th>
                            <td>{{ $course->duration }} {{ $course->duration == 1 ? 'Year' : 'Years' }}</td>
                        </tr>
                        <tr>
                            <th>Description:</th>
                            <td>{{ $course->description ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                @if($course->is_active)
                                <span class="badge bg-success">Active</span>
                                @else
                                <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Total Students:</th>
                            <td>{{ $course->students->count() }}</td>
                        </tr>
                        <tr>
                            <th>Created At:</th>
                            <td>{{ $course->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Last Updated:</th>
                            <td>{{ $course->updated_at->format('M d, Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Students Enrolled in this Course</h5>
                    <div>
                        <a href="{{ route('admin.students.create', ['course_id' => $course->id]) }}" class="btn btn-sm btn-primary me-2">
                            <i class="fas fa-plus-circle me-2"></i> Add Student
                        </a>
                        <a href="{{ route('admin.students.import', ['course_id' => $course->id]) }}" class="btn btn-sm btn-success">
                            <i class="fas fa-file-import me-2"></i> Import Students
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($course->students->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Roll Number</th>
                                    <th>Name</th>
                                    <th>Year</th>
                                    <th>Section</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $student)
                                <tr>
                                    <td>{{ $student->roll_number }}</td>
                                    <td>{{ $student->name }}</td>
                                    <td>{{ $student->year }}</td>
                                    <td>{{ $student->section ?? 'N/A' }}</td>
                                    <td>
                                        @if($student->is_active)
                                        <span class="badge bg-success">Active</span>
                                        @else
                                        <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.students.show', $student) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $students->links() }}
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        No students have been enrolled in this course yet.
                        <a href="{{ route('admin.students.create', ['course_id' => $course->id]) }}" class="alert-link">Add a student now</a> or 
                        <a href="{{ route('admin.students.import', ['course_id' => $course->id]) }}" class="alert-link">import students</a>.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
