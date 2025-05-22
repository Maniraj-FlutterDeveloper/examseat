@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Seating Plan Details</h5>
                    <div>
                        <a href="{{ route('seating.plans.edit', $seatingPlan) }}" class="btn btn-warning">Edit</a>
                        <a href="{{ route('seating.plans.generate', $seatingPlan) }}" class="btn btn-primary">Generate Assignments</a>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Exam Information</h6>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Exam Name</th>
                                    <td>{{ $seatingPlan->exam_name }}</td>
                                </tr>
                                <tr>
                                    <th>Date</th>
                                    <td>{{ $seatingPlan->exam_date->format('F d, Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Time</th>
                                    <td>{{ $seatingPlan->start_time->format('h:i A') }} - {{ $seatingPlan->end_time->format('h:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge bg-{{ $seatingPlan->status == 'scheduled' ? 'primary' : ($seatingPlan->status == 'ongoing' ? 'warning' : ($seatingPlan->status == 'completed' ? 'success' : 'danger')) }}">
                                            {{ ucfirst($seatingPlan->status) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Room Information</h6>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Room Number</th>
                                    <td>{{ $seatingPlan->room->room_number }}</td>
                                </tr>
                                <tr>
                                    <th>Block</th>
                                    <td>{{ $seatingPlan->room->block->block_name }}</td>
                                </tr>
                                <tr>
                                    <th>Capacity</th>
                                    <td>{{ $seatingPlan->room->capacity }} seats</td>
                                </tr>
                                <tr>
                                    <th>Accessible</th>
                                    <td>{{ $seatingPlan->room->is_accessible ? 'Yes' : 'No' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <h6>Seating Assignments</h6>
                    @if($seatingPlan->assignments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Seat Number</th>
                                        <th>Student Name</th>
                                        <th>Roll Number</th>
                                        <th>Course</th>
                                        <th>Year</th>
                                        <th>Override</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($seatingPlan->assignments->sortBy('seat_number') as $assignment)
                                        <tr>
                                            <td>{{ $assignment->seat_number }}</td>
                                            <td>{{ $assignment->student->name }}</td>
                                            <td>{{ $assignment->student->roll_number }}</td>
                                            <td>{{ $assignment->student->course->course_name }}</td>
                                            <td>{{ $assignment->student->year }}</td>
                                            <td>
                                                @if($assignment->is_override)
                                                    <span class="badge bg-info">Manual Override</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            No seating assignments have been generated yet. Click the "Generate Assignments" button to create assignments.
                        </div>
                    @endif

                    <div class="mt-4">
                        <a href="{{ route('seating.plans.index') }}" class="btn btn-secondary">Back to List</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

