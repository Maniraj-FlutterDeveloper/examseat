@extends('layouts.app')

@section('title', 'Students Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user-graduate mr-2"></i> Students Management
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-md-8">
                                <form action="{{ route('students.index') }}" method="GET" class="form-inline">
                                    <div class="form-group mr-2">
                                        <select name="course_id" class="form-control select2">
                                            <option value="">All Courses</option>
                                            @foreach($courses as $course)
                                                <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                                    {{ $course->name }} ({{ $course->code }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group mr-2">
                                        <select name="year" class="form-control">
                                            <option value="">All Years</option>
                                            @for($i = 1; $i <= 5; $i++)
                                                <option value="{{ $i }}" {{ request('year') == $i ? 'selected' : '' }}>
                                                    Year {{ $i }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="form-group mr-2">
                                        <select name="section" class="form-control">
                                            <option value="">All Sections</option>
                                            @foreach($sections as $section)
                                                <option value="{{ $section }}" {{ request('section') == $section ? 'selected' : '' }}>
                                                    Section {{ $section }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-info">
                                        <i class="fas fa-filter mr-1"></i> Filter
                                    </button>
                                    <a href="{{ route('students.index') }}" class="btn btn-secondary ml-2">
                                        <i class="fas fa-sync-alt mr-1"></i> Reset
                                    </a>
                                </form>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="{{ route('students.create') }}" class="btn btn-success">
                                    <i class="fas fa-plus-circle mr-1"></i> Add New Student
                                </a>
                                <a href="{{ route('students.import') }}" class="btn btn-warning ml-2">
                                    <i class="fas fa-file-import mr-1"></i> Import Students
                                </a>
                            </div>
                        </div>
                    </div>

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

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="students-table">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Roll Number</th>
                                    <th>Name</th>
                                    <th>Course</th>
                                    <th>Year</th>
                                    <th>Section</th>
                                    <th>Gender</th>
                                    <th>Special Needs</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $student)
                                    <tr>
                                        <td>{{ $student->id }}</td>
                                        <td>{{ $student->roll_number }}</td>
                                        <td>{{ $student->name }}</td>
                                        <td>
                                            @if($student->course)
                                                <a href="{{ route('courses.show', $student->course) }}">
                                                    {{ $student->course->code }}
                                                </a>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>{{ $student->year }}</td>
                                        <td>{{ $student->section ?? 'N/A' }}</td>
                                        <td>{{ ucfirst($student->gender ?? 'N/A') }}</td>
                                        <td>
                                            @if($student->has_special_needs)
                                                <span class="badge badge-warning" data-toggle="tooltip" title="{{ $student->special_needs_details }}">
                                                    <i class="fas fa-wheelchair mr-1"></i> Yes
                                                </span>
                                            @else
                                                <span class="badge badge-secondary">No</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $student->is_active ? 'success' : 'danger' }}">
                                                {{ $student->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('students.show', $student) }}" class="btn btn-info btn-sm" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('students.edit', $student) }}" class="btn btn-primary btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('students.destroy', $student) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this student?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('students.toggle-active', $student) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-{{ $student->is_active ? 'warning' : 'success' }} btn-sm" title="{{ $student->is_active ? 'Deactivate' : 'Activate' }}">
                                                        <i class="fas fa-{{ $student->is_active ? 'times' : 'check' }}"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">No students found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $students->appends(request()->except('page'))->links() }}
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
        
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endsection

