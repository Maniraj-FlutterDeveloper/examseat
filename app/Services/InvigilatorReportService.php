<?php

namespace App\Services;

use App\Models\SeatingPlan;
use App\Models\Room;
use Illuminate\Support\Facades\Log;

class InvigilatorReportService
{
    /**
     * Generate an invigilator report for a seating plan
     *
     * @param SeatingPlan $seatingPlan
     * @return array
     */
    public function generateReport(SeatingPlan $seatingPlan)
    {
        // Get all assignments for this seating plan
        $assignments = $seatingPlan->assignments()->with(['student', 'room'])->get();
        
        // Group assignments by room
        $roomAssignments = [];
        foreach ($assignments as $assignment) {
            if (!isset($roomAssignments[$assignment->room_id])) {
                $roomAssignments[$assignment->room_id] = [
                    'room' => $assignment->room,
                    'assignments' => [],
                    'stats' => [
                        'total' => 0,
                        'with_disabilities' => 0,
                        'overrides' => 0,
                    ],
                ];
            }
            
            $roomAssignments[$assignment->room_id]['assignments'][$assignment->seat_number] = $assignment;
            $roomAssignments[$assignment->room_id]['stats']['total']++;
            
            if ($assignment->student->has_disability) {
                $roomAssignments[$assignment->room_id]['stats']['with_disabilities']++;
            }
            
            if ($assignment->is_override) {
                $roomAssignments[$assignment->room_id]['stats']['overrides']++;
            }
        }
        
        // Calculate overall statistics
        $stats = [
            'total_students' => $assignments->count(),
            'total_rooms' => count($roomAssignments),
            'students_with_disabilities' => $assignments->filter(function ($assignment) {
                return $assignment->student->has_disability;
            })->count(),
            'overrides' => $assignments->filter(function ($assignment) {
                return $assignment->is_override;
            })->count(),
            'room_utilization' => $this->calculateRoomUtilization($roomAssignments),
        ];
        
        return [
            'seating_plan' => $seatingPlan,
            'room_assignments' => $roomAssignments,
            'stats' => $stats,
        ];
    }
    
    /**
     * Generate a room-specific invigilator report
     *
     * @param SeatingPlan $seatingPlan
     * @param Room $room
     * @return array
     */
    public function generateRoomReport(SeatingPlan $seatingPlan, Room $room)
    {
        // Get all assignments for this room in this seating plan
        $assignments = $seatingPlan->assignments()
            ->where('room_id', $room->id)
            ->with('student')
            ->get();
        
        // Organize assignments by seat number
        $seatAssignments = [];
        foreach ($assignments as $assignment) {
            $seatAssignments[$assignment->seat_number] = $assignment;
        }
        
        // Calculate statistics
        $stats = [
            'total_students' => $assignments->count(),
            'students_with_disabilities' => $assignments->filter(function ($assignment) {
                return $assignment->student->has_disability;
            })->count(),
            'overrides' => $assignments->filter(function ($assignment) {
                return $assignment->is_override;
            })->count(),
            'utilization' => $room->capacity > 0 ? ($assignments->count() / $room->capacity) * 100 : 0,
        ];
        
        return [
            'seating_plan' => $seatingPlan,
            'room' => $room,
            'assignments' => $seatAssignments,
            'stats' => $stats,
        ];
    }
    
    /**
     * Generate an attendance report for a seating plan
     *
     * @param SeatingPlan $seatingPlan
     * @return array
     */
    public function generateAttendanceReport(SeatingPlan $seatingPlan)
    {
        // Get all assignments for this seating plan
        $assignments = $seatingPlan->assignments()->with(['student', 'room'])->get();
        
        // Group assignments by room
        $roomAssignments = [];
        foreach ($assignments as $assignment) {
            if (!isset($roomAssignments[$assignment->room_id])) {
                $roomAssignments[$assignment->room_id] = [
                    'room' => $assignment->room,
                    'assignments' => [],
                ];
            }
            
            $roomAssignments[$assignment->room_id]['assignments'][] = $assignment;
        }
        
        // Sort assignments by seat number within each room
        foreach ($roomAssignments as &$roomData) {
            usort($roomData['assignments'], function ($a, $b) {
                return $a->seat_number <=> $b->seat_number;
            });
        }
        
        return [
            'seating_plan' => $seatingPlan,
            'room_assignments' => $roomAssignments,
        ];
    }
    
    /**
     * Calculate room utilization statistics
     *
     * @param array $roomAssignments
     * @return array
     */
    private function calculateRoomUtilization($roomAssignments)
    {
        $utilization = [];
        
        foreach ($roomAssignments as $roomId => $data) {
            $room = $data['room'];
            $totalAssignments = count($data['assignments']);
            
            $utilization[$roomId] = [
                'room_number' => $room->room_number,
                'capacity' => $room->capacity,
                'assigned' => $totalAssignments,
                'percentage' => $room->capacity > 0 ? ($totalAssignments / $room->capacity) * 100 : 0,
            ];
        }
        
        return $utilization;
    }
    
    /**
     * Log an incident during an exam
     *
     * @param SeatingPlan $seatingPlan
     * @param array $incidentData
     * @return bool
     */
    public function logIncident(SeatingPlan $seatingPlan, array $incidentData)
    {
        try {
            // In a real implementation, we would save this to the database
            // For now, we'll just log it
            Log::info("Incident logged for seating plan {$seatingPlan->exam_name}: " . json_encode($incidentData));
            
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to log incident: " . $e->getMessage());
            return false;
        }
    }
}

