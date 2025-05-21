@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Generated Seating Assignments</h5>
                    <div>
                        <form action="{{ route('seating.plans.save', $seatingPlan) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success">Save Assignments</button>
                        </form>
                        <a href="{{ route('seating.plans.show', $seatingPlan) }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Note:</strong> These are the generated seating assignments based on the defined rules. Review them and click "Save Assignments" to finalize.
                    </div>

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
                            </table>
                        </div>
                    </div>

                    <h6>Seating Layout</h6>
                    <div class="seating-layout mb-4">
                        @php
                            $roomId = $seatingPlan->room_id;
                            $roomAssignments = $assignments[$roomId] ?? [];
                            $capacity = $seatingPlan->room->capacity;
                            $seatsPerRow = $seatingPlan->room->layout['seats_per_row'] ?? 5;
                            $totalRows = ceil($capacity / $seatsPerRow);
                        @endphp

                        <div class="room-layout">
                            <div class="front-label text-center mb-2">Front of Room</div>
                            
                            @for ($row = 1; $row <= $totalRows; $row++)
                                <div class="row mb-2">
                                    @for ($col = 1; $col <= $seatsPerRow; $col++)
                                        @php
                                            $seatNumber = (($row - 1) * $seatsPerRow) + $col;
                                            $studentId = $roomAssignments[$seatNumber] ?? null;
                                        @endphp
                                        
                                        @if ($seatNumber <= $capacity)
                                            <div class="col">
                                                <div class="seat-box {{ $studentId ? 'occupied' : 'empty' }}">
                                                    <div class="seat-number">{{ $seatNumber }}</div>
                                                    @if ($studentId)
                                                        @php
                                                            $student = App\Models\Student::find($studentId);
                                                        @endphp
                                                        <div class="student-info">
                                                            <div class="student-name">{{ $student->name }}</div>
                                                            <div class="student-roll">{{ $student->roll_number }}</div>
                                                            <div class="student-course">{{ $student->course->course_code }}</div>
                                                        </div>
                                                    @else
                                                        <div class="empty-seat">Empty</div>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <div class="col"></div>
                                        @endif
                                    @endfor
                                </div>
                            @endfor
                            
                            <div class="back-label text-center mt-2">Back of Room</div>
                        </div>
                    </div>

                    <h6>Assignment List</h6>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Seat Number</th>
                                    <th>Student Name</th>
                                    <th>Roll Number</th>
                                    <th>Course</th>
                                    <th>Year</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($roomAssignments as $seatNumber => $studentId)
                                    @php
                                        $student = App\Models\Student::find($studentId);
                                    @endphp
                                    <tr>
                                        <td>{{ $seatNumber }}</td>
                                        <td>{{ $student->name }}</td>
                                        <td>{{ $student->roll_number }}</td>
                                        <td>{{ $student->course->course_name }}</td>
                                        <td>{{ $student->year }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No assignments generated.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .seating-layout {
        margin-top: 20px;
    }
    
    .room-layout {
        border: 2px solid #333;
        padding: 20px;
        background-color: #f8f9fa;
    }
    
    .seat-box {
        border: 1px solid #ccc;
        padding: 10px;
        height: 100px;
        text-align: center;
        border-radius: 5px;
    }
    
    .seat-box.occupied {
        background-color: #d1e7dd;
        border-color: #badbcc;
    }
    
    .seat-box.empty {
        background-color: #f8d7da;
        border-color: #f5c2c7;
    }
    
    .seat-number {
        font-weight: bold;
        margin-bottom: 5px;
    }
    
    .student-info {
        font-size: 0.8rem;
    }
    
    .student-name {
        font-weight: bold;
    }
    
    .front-label, .back-label {
        font-weight: bold;
        color: #6c757d;
    }
</style>
@endsection

