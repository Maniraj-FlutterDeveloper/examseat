@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Seating Override Details</h5>
                    <a href="{{ route('seating.overrides.edit', $override) }}" class="btn btn-warning">Edit</a>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Exam Information</h6>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Exam Name</th>
                                    <td>{{ $override->seatingPlan->exam_name }}</td>
                                </tr>
                                <tr>
                                    <th>Date</th>
                                    <td>{{ $override->seatingPlan->exam_date->format('F d, Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Time</th>
                                    <td>{{ $override->seatingPlan->start_time->format('h:i A') }} - {{ $override->seatingPlan->end_time->format('h:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Student Information</h6>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Name</th>
                                    <td>{{ $override->student->name }}</td>
                                </tr>
                                <tr>
                                    <th>Roll Number</th>
                                    <td>{{ $override->student->roll_number }}</td>
                                </tr>
                                <tr>
                                    <th>Course</th>
                                    <td>{{ $override->student->course->course_name }}</td>
                                </tr>
                                <tr>
                                    <th>Year</th>
                                    <td>{{ $override->student->year }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Seat Information</h6>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Room</th>
                                    <td>{{ $override->room->room_number }}</td>
                                </tr>
                                <tr>
                                    <th>Block</th>
                                    <td>{{ $override->room->block->block_name }}</td>
                                </tr>
                                <tr>
                                    <th>Seat Number</th>
                                    <td>{{ $override->seat_number }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Override Information</h6>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Created By</th>
                                    <td>{{ $override->created_by }}</td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $override->created_at->format('F d, Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Last Updated</th>
                                    <td>{{ $override->updated_at->format('F d, Y h:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6>Reason for Override</h6>
                        <div class="card">
                            <div class="card-body">
                                {{ $override->reason }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('seating.overrides.index') }}" class="btn btn-secondary">Back to List</a>
                        <form action="{{ route('seating.overrides.destroy', $override) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this override?')">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

