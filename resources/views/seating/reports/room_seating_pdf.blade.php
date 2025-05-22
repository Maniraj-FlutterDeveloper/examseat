<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Room Seating Plan - {{ $room->room_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0 0 5px 0;
            color: #000080; /* Navy blue */
        }
        .header h2 {
            font-size: 16px;
            margin: 0 0 5px 0;
        }
        .header p {
            margin: 0 0 5px 0;
        }
        .room-info {
            margin-bottom: 20px;
        }
        .room-info h3 {
            font-size: 16px;
            margin: 0 0 5px 0;
            color: #000080; /* Navy blue */
        }
        .room-info p {
            margin: 0 0 5px 0;
        }
        .seating-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .row {
            display: table-row;
        }
        .seat {
            display: table-cell;
            border: 1px solid #ddd;
            padding: 5px;
            text-align: center;
            vertical-align: middle;
            width: 16.66%; /* 6 seats per row */
            height: 80px;
        }
        .seat-number {
            font-weight: bold;
            margin-bottom: 3px;
        }
        .student-name {
            font-weight: bold;
        }
        .student-roll {
            color: #666;
        }
        .student-course {
            font-size: 10px;
            color: #666;
        }
        .empty-seat {
            background-color: #f9f9f9;
            color: #999;
        }
        .override {
            background-color: #ffe6e6;
        }
        .disability {
            background-color: #e6f7ff;
        }
        .legend {
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .legend-item {
            display: inline-block;
            margin-right: 20px;
        }
        .legend-color {
            display: inline-block;
            width: 15px;
            height: 15px;
            margin-right: 5px;
            vertical-align: middle;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $seatingPlan->exam_name }}</h1>
        <h2>Room Seating Plan</h2>
        <p>Date: {{ $seatingPlan->exam_date->format('F j, Y') }}</p>
        <p>Time: {{ $seatingPlan->start_time->format('g:i A') }} - {{ $seatingPlan->end_time->format('g:i A') }}</p>
    </div>
    
    <div class="room-info">
        <h3>Room: {{ $room->room_number }}</h3>
        <p>Capacity: {{ $room->capacity }} seats</p>
        <p>Block: {{ $room->block->block_name ?? 'N/A' }}</p>
        <p>Location: {{ $room->block->location ?? 'N/A' }}</p>
    </div>
    
    <div class="legend">
        <div class="legend-item">
            <div class="legend-color" style="background-color: #ffffff; border: 1px solid #ddd;"></div>
            <span>Regular Seat</span>
        </div>
        <div class="legend-item">
            <div class="legend-color" style="background-color: #ffe6e6;"></div>
            <span>Override</span>
        </div>
        <div class="legend-item">
            <div class="legend-color" style="background-color: #e6f7ff;"></div>
            <span>Special Needs</span>
        </div>
        <div class="legend-item">
            <div class="legend-color" style="background-color: #f9f9f9;"></div>
            <span>Empty Seat</span>
        </div>
    </div>
    
    <div class="seating-grid">
        @php
            $seatsPerRow = $room->layout['seats_per_row'] ?? 6;
            $totalRows = ceil($room->capacity / $seatsPerRow);
        @endphp
        
        @for($row = 1; $row <= $totalRows; $row++)
            <div class="row">
                @for($col = 1; $col <= $seatsPerRow; $col++)
                    @php
                        $seatNumber = (($row - 1) * $seatsPerRow) + $col;
                        $assignment = $assignments[$seatNumber] ?? null;
                    @endphp
                    
                    @if($seatNumber <= $room->capacity)
                        @if($assignment)
                            <div class="seat {{ $assignment->is_override ? 'override' : '' }} {{ $assignment->student->has_disability ? 'disability' : '' }}">
                                <div class="seat-number">{{ $seatNumber }}</div>
                                <div class="student-name">{{ $assignment->student->name }}</div>
                                <div class="student-roll">{{ $assignment->student->roll_number }}</div>
                                <div class="student-course">{{ $assignment->student->course->course_name ?? 'N/A' }}</div>
                            </div>
                        @else
                            <div class="seat empty-seat">
                                <div class="seat-number">{{ $seatNumber }}</div>
                                <div>Empty</div>
                            </div>
                        @endif
                    @else
                        <div class="seat" style="border: none;"></div>
                    @endif
                @endfor
            </div>
        @endfor
    </div>
    
    <div class="footer">
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
        <p>Exam Seat Arrangement System</p>
    </div>
</body>
</html>

