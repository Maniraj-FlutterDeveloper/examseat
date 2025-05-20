@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Course Details</h1>
        <div>
            <a href="{{ route('courses.edit', $course->id) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('courses.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Courses
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
                            <th style="width: 30%">ID:</th>
                            <td>{{ $course->id }}</td>
                        </tr>
                        <tr>
                            <th>Course Name:</th>
                            <td>{{ $course->course_name }}</td>
                        </tr>
                        <tr>
                            <th>Course Code:</th>
                            <td>{{ $course->course_code }}</td>
                        </tr>
                        <tr>
                            <th>Duration:</th>
                            <td>{{ $course->duration }} {{ Str::plural('year', $course->duration) }}</td>
                        </tr>
                        <tr>
                            <th>Description:</th>
                            <td>{{ $course->description ?: 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Created At:</th>
                            <td>{{ $course->created_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At:</th>
                            <td>{{ $course->updated_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Students in this Course</h5>
                    <a href="{{ route('students.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus-circle me-1"></i>Add Student
                    </a>
                </div>
                <div class="card-body">
                    @if($course->students->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Roll Number</th>
                                        <th>Name</th>
                                        <th>Year</th>
                                        <th>Section</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($course->students as $student)
                                        <tr>
                                            <td>{{ $student->roll_number }}</td>
                                            <td>{{ $student->name }}</td>
                                            <td>{{ $student->year }}</td>
                                            <td>{{ $student->section }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('students.show', $student->id) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('students.edit', $student->id) }}" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-user-graduate fa-3x text-muted mb-3"></i>
                            <p>No students have been added to this course yet.</p>
                            <a href="{{ route('students.create') }}" class="btn btn-primary">Add Student</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

