@extends('layouts.admin')

@section('title', 'Student Details')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Student Details</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back to Students
            </a>
            <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i> Edit Student
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Student Information</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th style="width: 30%">Roll Number:</th>
                            <td>{{ $student->roll_number }}</td>
                        </tr>
                        <tr>
                            <th>Name:</th>
                            <td>{{ $student->name }}</td>
                        </tr>
                        <tr>
                            <th>Course:</th>
                            <td>
                                <a href="{{ route('admin.courses.show', $student->course) }}">
                                    {{ $student->course->course_name }} ({{ $student->course->course_code }})
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Year:</th>
                            <td>Year {{ $student->year }}</td>
                        </tr>
                        <tr>
                            <th>Section:</th>
                            <td>{{ $student->section ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $student->email ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Phone:</th>
                            <td>{{ $student->phone ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Disability:</th>
                            <td>
                                @if($student->has_disability)
                                <span class="badge bg-warning text-dark">Yes</span>
                                <div class="mt-2 small">
                                    <strong>Details:</strong> {{ $student->disability_details }}
                                </div>
                                @else
                                <span class="badge bg-secondary">No</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                @if($student->is_active)
                                <span class="badge bg-success">Active</span>
                                @else
                                <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Created At:</th>
                            <td>{{ $student->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Last Updated:</th>
                            <td>{{ $student->updated_at->format('M d, Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Seating Plans</h5>
                    <a href="{{ route('admin.seating-plans.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus-circle me-2"></i> Create Seating Plan
                    </a>
                </div>
                <div class="card-body">
                    @if($student->seatingPlans->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Exam Name</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Block</th>
                                    <th>Room</th>
                                    <th>Seat</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($student->seatingPlans as $plan)
                                <tr>
                                    <td>{{ $plan->exam_name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($plan->exam_date)->format('M d, Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($plan->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($plan->end_time)->format('h:i A') }}</td>
                                    <td>{{ $plan->room->block->block_name }}</td>
                                    <td>{{ $plan->room->room_number }}</td>
                                    <td>{{ $plan->seat_number }}</td>
                                    <td>
                                        <span class="badge bg-{{ $plan->status === 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($plan->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.seating-plans.show', $plan) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        No seating plans have been assigned to this student yet.
                        <a href="{{ route('admin.seating-plans.create') }}" class="alert-link">Create a seating plan now</a>.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
