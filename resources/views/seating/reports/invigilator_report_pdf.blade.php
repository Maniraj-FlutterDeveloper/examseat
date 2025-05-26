<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invigilator Report - {{ $seatingPlan->exam_name }}</title>
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
        .section {
            margin-bottom: 20px;
        }
        .section h3 {
            font-size: 14px;
            margin: 0 0 10px 0;
            color: #000080; /* Navy blue */
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-gap: 10px;
            margin-bottom: 20px;
        }
        .stat-box {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #000080; /* Navy blue */
            margin-bottom: 5px;
        }
        .stat-label {
            font-size: 12px;
            color: #666;
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
        .special-needs {
            background-color: #e6f7ff;
        }
        .override {
            background-color: #ffe6e6;
        }
        .room-utilization {
            margin-bottom: 20px;
        }
        .utilization-bar {
            height: 20px;
            background-color: #f2f2f2;
            margin-bottom: 5px;
            position: relative;
        }
        .utilization-fill {
            height: 100%;
            background-color: #000080; /* Navy blue */
            position: absolute;
            top: 0;
            left: 0;
        }
        .utilization-text {
            position: absolute;
            width: 100%;
            text-align: center;
            color: white;
            font-weight: bold;
            font-size: 10px;
            line-height: 20px;
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
        .notes {
            border: 1px solid #ddd;
            padding: 10px;
            min-height: 100px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $seatingPlan->exam_name }}</h1>
        <h2>Invigilator Report</h2>
        <p>Date: {{ $seatingPlan->exam_date->format('F j, Y') }}</p>
        <p>Time: {{ $seatingPlan->start_time->format('g:i A') }} - {{ $seatingPlan->end_time->format('g:i A') }}</p>
    </div>
    
    <div class="section">
        <h3>Exam Overview</h3>
        
        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-value">{{ $stats['total_students'] }}</div>
                <div class="stat-label">Total Students</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">{{ $stats['total_rooms'] }}</div>
                <div class="stat-label">Total Rooms</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">{{ $stats['students_with_disabilities'] }}</div>
                <div class="stat-label">Students with Special Needs</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">{{ $stats['overrides'] }}</div>
                <div class="stat-label">Manual Overrides</div>
            </div>
        </div>
    </div>
    
    <div class="section">
        <h3>Room Utilization</h3>
        
        @foreach($stats['room_utilization'] as $roomId => $utilization)
            <div class="room-utilization">
                <p><strong>{{ $utilization['room_number'] }}</strong> ({{ $utilization['assigned'] }}/{{ $utilization['capacity'] }} seats)</p>
                <div class="utilization-bar">
                    <div class="utilization-fill" style="width: {{ min($utilization['percentage'], 100) }}%;"></div>
                    <div class="utilization-text">{{ round($utilization['percentage']) }}%</div>
                </div>
            </div>
        @endforeach
    </div>
    
    <div class="section">
        <h3>Students with Special Needs</h3>
        
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Roll Number</th>
                    <th>Room</th>
                    <th>Seat</th>
                    <th>Requirements</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $specialNeedsStudents = [];
                    foreach ($roomAssignments as $roomId => $data) {
                        foreach ($data['assignments'] as $seatNumber => $assignment) {
                            if ($assignment->student->has_disability) {
                                $specialNeedsStudents[] = $assignment;
                            }
                        }
                    }
                @endphp
                
                @if(count($specialNeedsStudents) > 0)
                    @foreach($specialNeedsStudents as $assignment)
                        <tr class="special-needs">
                            <td>{{ $assignment->student->name }}</td>
                            <td>{{ $assignment->student->roll_number }}</td>
                            <td>{{ $assignment->room->room_number }}</td>
                            <td>{{ $assignment->seat_number }}</td>
                            <td>
                                @php
                                    $priority = $assignment->student->priorities->first();
                                @endphp
                                {{ $priority ? $priority->requirements : 'No specific requirements' }}
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" style="text-align: center;">No students with special needs</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    
    <div class="section">
        <h3>Manual Overrides</h3>
        
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Roll Number</th>
                    <th>Room</th>
                    <th>Seat</th>
                    <th>Reason</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $overrideAssignments = [];
                    foreach ($roomAssignments as $roomId => $data) {
                        foreach ($data['assignments'] as $seatNumber => $assignment) {
                            if ($assignment->is_override) {
                                $overrideAssignments[] = $assignment;
                            }
                        }
                    }
                @endphp
                
                @if(count($overrideAssignments) > 0)
                    @foreach($overrideAssignments as $assignment)
                        <tr class="override">
                            <td>{{ $assignment->student->name }}</td>
                            <td>{{ $assignment->student->roll_number }}</td>
                            <td>{{ $assignment->room->room_number }}</td>
                            <td>{{ $assignment->seat_number }}</td>
                            <td>{{ $assignment->override_reason ?? 'Manual override' }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" style="text-align: center;">No manual overrides</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    
    <div class="section">
        <h3>Notes and Incidents</h3>
        
        <div class="notes">
            
        </div>
    </div>
    
    <div class="footer">
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
        <p>Exam Seat Arrangement System</p>
    </div>
</body>
</html>

