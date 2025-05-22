<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Student Seating Cards - {{ $seatingPlan->exam_name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .page-break {
            page-break-after: always;
        }
        .card {
            border: 1px solid #000;
            padding: 10px;
            margin-bottom: 10px;
            height: 250px;
            position: relative;
            page-break-inside: avoid;
        }
        .card-header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .card-header h1 {
            font-size: 16px;
            margin: 0 0 5px 0;
            color: #000080; /* Navy blue */
        }
        .card-header h2 {
            font-size: 14px;
            margin: 0 0 5px 0;
        }
        .student-info {
            margin-bottom: 15px;
        }
        .student-info p {
            margin: 0 0 5px 0;
        }
        .student-name {
            font-weight: bold;
            font-size: 14px;
        }
        .exam-info {
            margin-bottom: 15px;
        }
        .exam-info p {
            margin: 0 0 5px 0;
        }
        .seating-info {
            margin-bottom: 15px;
            padding: 5px;
            background-color: #f9f9f9;
            border: 1px dashed #ccc;
        }
        .seating-info p {
            margin: 0 0 5px 0;
            font-weight: bold;
        }
        .room-number, .seat-number {
            font-size: 16px;
            color: #000080; /* Navy blue */
        }
        .qr-code {
            position: absolute;
            bottom: 10px;
            right: 10px;
            width: 80px;
            height: 80px;
            border: 1px solid #ddd;
            text-align: center;
            line-height: 80px;
            font-size: 10px;
            color: #999;
        }
        .footer {
            position: absolute;
            bottom: 10px;
            left: 10px;
            font-size: 9px;
            color: #666;
        }
        .card-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-gap: 10px;
        }
    </style>
</head>
<body>
    <div class="card-grid">
        @foreach($assignments as $assignment)
            <div class="card">
                <div class="card-header">
                    <h1>{{ $seatingPlan->exam_name }}</h1>
                    <h2>Student Seating Card</h2>
                </div>
                
                <div class="student-info">
                    <p class="student-name">{{ $assignment->student->name }}</p>
                    <p><strong>Roll Number:</strong> {{ $assignment->student->roll_number }}</p>
                    <p><strong>Course:</strong> {{ $assignment->student->course->course_name ?? 'N/A' }}</p>
                </div>
                
                <div class="exam-info">
                    <p><strong>Date:</strong> {{ $seatingPlan->exam_date->format('F j, Y') }}</p>
                    <p><strong>Time:</strong> {{ $seatingPlan->start_time->format('g:i A') }} - {{ $seatingPlan->end_time->format('g:i A') }}</p>
                </div>
                
                <div class="seating-info">
                    <p><strong>Room:</strong> <span class="room-number">{{ $assignment->room->room_number }}</span></p>
                    <p><strong>Seat:</strong> <span class="seat-number">{{ $assignment->seat_number }}</span></p>
                    @if($assignment->room->block)
                        <p><strong>Block:</strong> {{ $assignment->room->block->block_name }}</p>
                    @endif
                </div>
                
                <div class="qr-code">
                    QR Code
                </div>
                
                <div class="footer">
                    <p>Please arrive at least 15 minutes before the exam starts.</p>
                    <p>Bring your student ID and this card with you.</p>
                </div>
            </div>
            
            @if($loop->iteration % 2 == 0 && !$loop->last)
                <div class="page-break"></div>
            @endif
        @endforeach
    </div>
</body>
</html>

