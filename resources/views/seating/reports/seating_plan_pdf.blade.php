<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Seating Plan - {{ $seatingPlan->exam_name }}</title>
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
        .room-header {
            background-color: #000080; /* Navy blue */
            color: white;
            padding: 5px 10px;
            margin-top: 20px;
            margin-bottom: 10px;
            font-size: 14px;
            font-weight: bold;
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
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $seatingPlan->exam_name }}</h1>
        <h2>Seating Plan</h2>
        <p>Date: {{ $seatingPlan->exam_date->format('F j, Y') }}</p>
        <p>Time: {{ $seatingPlan->start_time->format('g:i A') }} - {{ $seatingPlan->end_time->format('g:i A') }}</p>
    </div>
    
    @foreach($roomAssignments as $roomId => $data)
        <div class="room-header">
            Room: {{ $data['room']->room_number }} (Capacity: {{ $data['room']->capacity }})
        </div>
        
        <div class="seating-grid">
            @php
                $seatsPerRow = $data['room']->layout['seats_per_row'] ?? 6;
                $totalRows = ceil($data['room']->capacity / $seatsPerRow);
                $assignments = $data['assignments'];
            @endphp
            
            @for($row = 1; $row <= $totalRows; $row++)
                <div class="row">
                    @for($col = 1; $col <= $seatsPerRow; $col++)
                        @php
                            $seatNumber = (($row - 1) * $seatsPerRow) + $col;
                            $assignment = $assignments[$seatNumber] ?? null;
                        @endphp
                        
                        @if($seatNumber <= $data['room']->capacity)
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
        
        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
    
    <div class="footer">
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
        <p>Exam Seat Arrangement System</p>
    </div>
</body>
</html>

