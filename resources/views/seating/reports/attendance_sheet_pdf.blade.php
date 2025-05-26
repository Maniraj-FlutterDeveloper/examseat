<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Attendance Sheet - {{ $seatingPlan->exam_name }}</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .signature-cell {
            width: 20%;
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
        .special-needs {
            background-color: #e6f7ff;
        }
        .override {
            background-color: #ffe6e6;
        }
        .invigilator-section {
            margin-top: 30px;
        }
        .invigilator-signature {
            margin-top: 20px;
            border-top: 1px solid #000;
            width: 200px;
            text-align: center;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $seatingPlan->exam_name }}</h1>
        <h2>Attendance Sheet</h2>
        <p>Date: {{ $seatingPlan->exam_date->format('F j, Y') }}</p>
        <p>Time: {{ $seatingPlan->start_time->format('g:i A') }} - {{ $seatingPlan->end_time->format('g:i A') }}</p>
    </div>
    
    @foreach($roomAssignments as $roomId => $data)
        <div class="room-header">
            Room: {{ $data['room']->room_number }} ({{ count($data['assignments']) }} students)
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Seat</th>
                    <th>Roll Number</th>
                    <th>Name</th>
                    <th>Course</th>
                    <th class="signature-cell">Signature</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['assignments'] as $index => $assignment)
                    <tr class="{{ $assignment->student->has_disability ? 'special-needs' : '' }} {{ $assignment->is_override ? 'override' : '' }}">
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $assignment->seat_number }}</td>
                        <td>{{ $assignment->student->roll_number }}</td>
                        <td>{{ $assignment->student->name }}</td>
                        <td>{{ $assignment->student->course->course_name ?? 'N/A' }}</td>
                        <td class="signature-cell"></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="invigilator-section">
            <p><strong>Total Students:</strong> {{ count($data['assignments']) }}</p>
            <p><strong>Present:</strong> _______________</p>
            <p><strong>Absent:</strong> _______________</p>
            
            <p><strong>Invigilator Name:</strong> ______________________________________</p>
            
            <div class="invigilator-signature">
                Signature
            </div>
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

