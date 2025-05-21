@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Student Priority Details</h5>
                    <a href="{{ route('seating.priorities.edit', $priority) }}" class="btn btn-warning">Edit</a>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Student Information</h6>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Name</th>
                                    <td>{{ $priority->student->name }}</td>
                                </tr>
                                <tr>
                                    <th>Roll Number</th>
                                    <td>{{ $priority->student->roll_number }}</td>
                                </tr>
                                <tr>
                                    <th>Course</th>
                                    <td>{{ $priority->student->course->course_name }}</td>
                                </tr>
                                <tr>
                                    <th>Year</th>
                                    <td>{{ $priority->student->year }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Priority Information</h6>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Priority Type</th>
                                    <td>
                                        <span class="badge bg-{{ $priority->priority_type == 'disability' ? 'danger' : ($priority->priority_type == 'medical' ? 'warning' : 'info') }}">
                                            {{ ucfirst($priority->priority_type) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Priority Level</th>
                                    <td>{{ $priority->priority_level }} / 10</td>
                                </tr>
                                <tr>
                                    <th>Valid Until</th>
                                    <td>
                                        @if($priority->valid_until)
                                            {{ $priority->valid_until->format('F d, Y') }}
                                            @if($priority->valid_until->isPast())
                                                <span class="badge bg-danger">Expired</span>
                                            @endif
                                        @else
                                            <span class="badge bg-success">Permanent</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Verified</th>
                                    <td>
                                        <span class="badge bg-{{ $priority->is_verified ? 'success' : 'warning' }}">
                                            {{ $priority->is_verified ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6>Description</h6>
                        <div class="card">
                            <div class="card-body">
                                {{ $priority->description ?? 'No description provided.' }}
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6>Special Requirements</h6>
                        <div class="card">
                            <div class="card-body">
                                {{ $priority->requirements ?? 'No special requirements specified.' }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('seating.priorities.index') }}" class="btn btn-secondary">Back to List</a>
                        <form action="{{ route('seating.priorities.destroy', $priority) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this priority?')">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

