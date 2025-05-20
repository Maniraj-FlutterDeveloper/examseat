@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Student Details</h1>
        <div>
            <a href="{{ route('students.edit', $student->id) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('students.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Students
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
                            <th style="width: 30%">ID:</th>
                            <td>{{ $student->id }}</td>
                        </tr>
                        <tr>
                            <th>Name:</th>
                            <td>{{ $student->name }}</td>
                        </tr>
                        <tr>
                            <th>Roll Number:</th>
                            <td>{{ $student->roll_number }}</td>
                        </tr>
                        <tr>
                            <th>Course:</th>
                            <td>
                                <a href="{{ route('courses.show', $student->course_id) }}">
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
                            <td>Section {{ $student->section }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $student->email ?: 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Phone:</th>
                            <td>{{ $student->phone ?: 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Address:</th>
                            <td>{{ $student->address ?: 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Special Needs:</th>
                            <td>
                                @if($student->has_special_needs)
                                    <span class="badge bg-warning">Yes</span>
                                    <p class="mt-2 mb-0 small">{{ $student->special_needs_details }}</p>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Created At:</th>
                            <td>{{ $student->created_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At:</th>
                            <td>{{ $student->updated_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Seating Plans</h5>
                </div>
                <div class="card-body">
                    @if($student->seatingPlans->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Exam</th>
                                        <th>Room</th>
                                        <th>Block</th>
                                        <th>Seat Number</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($student->seatingPlans as $plan)
                                        <tr>
                                            <td>{{ $plan->exam_name }}</td>
                                            <td>
                                                <a href="{{ route('rooms.show', $plan->room_id) }}">
                                                    {{ $plan->room->room_number }}
                                                </a>
                                            </td>
                                            <td>{{ $plan->room->block->block_name }}</td>
                                            <td>{{ $plan->seat_number }}</td>
                                            <td>{{ $plan->exam_date->format('M d, Y') }}</td>
                                            <td>
                                                <a href="{{ route('seating-plans.show', $plan->id) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-chair fa-3x text-muted mb-3"></i>
                            <p>No seating plans have been assigned to this student yet.</p>
                            <a href="{{ route('seating-plans.create') }}" class="btn btn-primary">Create Seating Plan</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

