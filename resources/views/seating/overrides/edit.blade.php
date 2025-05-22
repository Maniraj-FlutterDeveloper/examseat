@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit Seating Override</h5>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('seating.overrides.update', $override) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="seating_plan_id" class="form-label">Seating Plan</label>
                            <select class="form-select @error('seating_plan_id') is-invalid @enderror" id="seating_plan_id" name="seating_plan_id" required>
                                <option value="">Select Seating Plan</option>
                                @foreach ($seatingPlans as $plan)
                                    <option value="{{ $plan->id }}" {{ (old('seating_plan_id', $override->seating_plan_id) == $plan->id) ? 'selected' : '' }}>
                                        {{ $plan->exam_name }} ({{ $plan->exam_date->format('M d, Y') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('seating_plan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="student_id" class="form-label">Student</label>
                            <select class="form-select @error('student_id') is-invalid @enderror" id="student_id" name="student_id" required>
                                <option value="">Select Student</option>
                                @foreach ($students as $student)
                                    <option value="{{ $student->id }}" {{ (old('student_id', $override->student_id) == $student->id) ? 'selected' : '' }}>
                                        {{ $student->name }} ({{ $student->roll_number }})
                                    </option>
                                @endforeach
                            </select>
                            @error('student_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="room_id" class="form-label">Room</label>
                            <select class="form-select @error('room_id') is-invalid @enderror" id="room_id" name="room_id" required>
                                <option value="">Select Room</option>
                                @foreach ($rooms as $room)
                                    <option value="{{ $room->id }}" {{ (old('room_id', $override->room_id) == $room->id) ? 'selected' : '' }}>
                                        {{ $room->room_number }} (Block: {{ $room->block->block_name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('room_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="seat_number" class="form-label">Seat Number</label>
                            <input type="number" class="form-control @error('seat_number') is-invalid @enderror" id="seat_number" name="seat_number" value="{{ old('seat_number', $override->seat_number) }}" min="1" required>
                            <div class="form-text">Enter a valid seat number for the selected room.</div>
                            @error('seat_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="reason" class="form-label">Reason for Override</label>
                            <textarea class="form-control @error('reason') is-invalid @enderror" id="reason" name="reason" rows="3" required>{{ old('reason', $override->reason) }}</textarea>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="created_by" class="form-label">Created By</label>
                            <input type="text" class="form-control @error('created_by') is-invalid @enderror" id="created_by" name="created_by" value="{{ old('created_by', $override->created_by) }}" required>
                            @error('created_by')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('seating.overrides.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Override</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

