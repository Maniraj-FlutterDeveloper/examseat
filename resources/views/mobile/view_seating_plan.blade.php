@extends('layouts.mobile')

@section('title', 'View Seating Plan')

@section('custom-css')
.seat-info {
    background-color: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
}

.seat-number {
    font-size: 3rem;
    font-weight: bold;
    color: var(--primary-color);
    text-align: center;
    margin-bottom: 10px;
}

.room-layout {
    background-color: white;
    border-radius: 10px;
    padding: 20px;
    margin-top: 20px;
    overflow-x: auto;
}

.seat {
    width: 40px;
    height: 40px;
    margin: 5px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 5px;
    font-size: 0.8rem;
    font-weight: bold;
    color: white;
    background-color: #6c757d;
}

.seat.my-seat {
    background-color: var(--primary-color);
    box-shadow: 0 0 10px rgba(0, 0, 128, 0.5);
    transform: scale(1.1);
}

.seat.empty {
    background-color: #e9ecef;
    color: #adb5bd;
}

.exam-details {
    margin-bottom: 20px;
}

.exam-details .item {
    margin-bottom: 10px;
}

.exam-details .label {
    font-weight: bold;
    color: var(--primary-color);
}

.qr-code {
    text-align: center;
    margin-top: 20px;
}

.qr-code img {
    max-width: 200px;
    height: auto;
}
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chair me-2"></i> Seating Plan Details
            </div>
            <div class="card-body">
                <div class="seat-info">
                    <div class="seat-number">{{ $seatDetails->seat_number }}</div>
                    <div class="text-center mb-3">Your Seat Number</div>
                    
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="fw-bold">Room</div>
                            <div>{{ $room->name }}</div>
                        </div>
                        <div class="col-6">
                            <div class="fw-bold">Block</div>
                            <div>{{ $room->block->name }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="exam-details">
                    <div class="item">
                        <div class="label">Exam</div>
                        <div>{{ $seatingPlan->title }}</div>
                    </div>
                    <div class="item">
                        <div class="label">Subject</div>
                        <div>{{ $seatingPlan->subject }}</div>
                    </div>
                    <div class="item">
                        <div class="label">Date</div>
                        <div>{{ $seatingPlan->exam_date->format('l, F j, Y') }}</div>
                    </div>
                    <div class="item">
                        <div class="label">Time</div>
                        <div>{{ $seatingPlan->exam_date->format('h:i A') }} - {{ $seatingPlan->end_time->format('h:i A') }}</div>
                    </div>
                    <div class="item">
                        <div class="label">Duration</div>
                        <div>{{ $seatingPlan->duration }} minutes</div>
                    </div>
                    <div class="item">
                        <div class="label">Instructions</div>
                        <div>{{ $seatingPlan->instructions ?? 'No special instructions.' }}</div>
                    </div>
                </div>
                
                @if($seatingPlan->show_room_layout)
                    <div class="room-layout">
                        <h5 class="mb-3">Room Layout</h5>
                        <div class="text-center">
                            @for($row = 1; $row <= $room->rows; $row++)
                                <div class="mb-2">
                                    @for($col = 1; $col <= $room->columns; $col++)
                                        @php
                                            $seatNumber = (($row - 1) * $room->columns) + $col;
                                            $isMySeat = $seatDetails->seat_number == $seatNumber;
                                            $isEmpty = !$seatingPlan->students()->wherePivot('room_id', $room->id)->wherePivot('seat_number', $seatNumber)->exists();
                                        @endphp
                                        <div class="seat {{ $isMySeat ? 'my-seat' : ($isEmpty ? 'empty' : '') }}">
                                            {{ $seatNumber }}
                                        </div>
                                    @endfor
                                </div>
                            @endfor
                        </div>
                    </div>
                @endif
                
                <div class="qr-code">
                    <h5 class="mb-3">Scan for Verification</h5>
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode(route('mobile.seating_plans.view', $seatingPlan->id)) }}" alt="QR Code">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

