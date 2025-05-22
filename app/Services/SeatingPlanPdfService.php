<?php

namespace App\Services;

use App\Models\SeatingPlan;
use App\Models\Room;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;

class SeatingPlanPdfService
{
    /**
     * Generate a PDF for a seating plan
     *
     * @param SeatingPlan $seatingPlan
     * @return \Barryvdh\DomPDF\PDF
     */
    public function generatePdf(SeatingPlan $seatingPlan)
    {
        // Get all assignments for this seating plan
        $assignments = $seatingPlan->assignments()->with(['student', 'room'])->get();
        
        // Group assignments by room
        $roomAssignments = [];
        foreach ($assignments as $assignment) {
            if (!isset($roomAssignments[$assignment->room_id])) {
                $roomAssignments[$assignment->room_id] = [
                    'room' => $assignment->room,
                    'assignments' => []
                ];
            }
            
            $roomAssignments[$assignment->room_id]['assignments'][$assignment->seat_number] = $assignment;
        }
        
        // Generate PDF
        $pdf = PDF::loadView('seating.reports.seating_plan_pdf', [
            'seatingPlan' => $seatingPlan,
            'roomAssignments' => $roomAssignments,
        ]);
        
        $pdf->setPaper('a4', 'landscape');
        
        return $pdf;
    }
    
    /**
     * Generate a PDF for a room's seating plan
     *
     * @param SeatingPlan $seatingPlan
     * @param Room $room
     * @return \Barryvdh\DomPDF\PDF
     */
    public function generateRoomPdf(SeatingPlan $seatingPlan, Room $room)
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
        
        // Generate PDF
        $pdf = PDF::loadView('seating.reports.room_seating_pdf', [
            'seatingPlan' => $seatingPlan,
            'room' => $room,
            'assignments' => $seatAssignments,
        ]);
        
        return $pdf;
    }
    
    /**
     * Generate a PDF with student seating cards
     *
     * @param SeatingPlan $seatingPlan
     * @return \Barryvdh\DomPDF\PDF
     */
    public function generateStudentCardsPdf(SeatingPlan $seatingPlan)
    {
        // Get all assignments for this seating plan
        $assignments = $seatingPlan->assignments()->with(['student', 'room'])->get();
        
        // Generate PDF
        $pdf = PDF::loadView('seating.reports.student_cards_pdf', [
            'seatingPlan' => $seatingPlan,
            'assignments' => $assignments,
        ]);
        
        // Set paper size for cards (A6 size)
        $pdf->setPaper([0, 0, 419.53, 595.28]);
        
        return $pdf;
    }
    
    /**
     * Generate a PDF with the invigilator report
     *
     * @param SeatingPlan $seatingPlan
     * @return \Barryvdh\DomPDF\PDF
     */
    public function generateInvigilatorReportPdf(SeatingPlan $seatingPlan)
    {
        // Get all assignments for this seating plan
        $assignments = $seatingPlan->assignments()->with(['student', 'room'])->get();
        
        // Group assignments by room
        $roomAssignments = [];
        foreach ($assignments as $assignment) {
            if (!isset($roomAssignments[$assignment->room_id])) {
                $roomAssignments[$assignment->room_id] = [
                    'room' => $assignment->room,
                    'assignments' => []
                ];
            }
            
            $roomAssignments[$assignment->room_id]['assignments'][$assignment->seat_number] = $assignment;
        }
        
        // Calculate statistics
        $stats = [
            'total_students' => $assignments->count(),
            'total_rooms' => count($roomAssignments),
            'students_with_disabilities' => $assignments->filter(function ($assignment) {
                return $assignment->student->has_disability;
            })->count(),
            'overrides' => $assignments->filter(function ($assignment) {
                return $assignment->is_override;
            })->count(),
        ];
        
        // Generate PDF
        $pdf = PDF::loadView('seating.reports.invigilator_report_pdf', [
            'seatingPlan' => $seatingPlan,
            'roomAssignments' => $roomAssignments,
            'stats' => $stats,
        ]);
        
        return $pdf;
    }
    
    /**
     * Generate a PDF with the attendance sheet
     *
     * @param SeatingPlan $seatingPlan
     * @param Room|null $room
     * @return \Barryvdh\DomPDF\PDF
     */
    public function generateAttendanceSheetPdf(SeatingPlan $seatingPlan, Room $room = null)
    {
        // Get assignments
        $query = $seatingPlan->assignments()->with(['student', 'room']);
        
        if ($room) {
            $query->where('room_id', $room->id);
        }
        
        $assignments = $query->get();
        
        // Group assignments by room
        $roomAssignments = [];
        foreach ($assignments as $assignment) {
            if (!isset($roomAssignments[$assignment->room_id])) {
                $roomAssignments[$assignment->room_id] = [
                    'room' => $assignment->room,
                    'assignments' => []
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
        
        // Generate PDF
        $pdf = PDF::loadView('seating.reports.attendance_sheet_pdf', [
            'seatingPlan' => $seatingPlan,
            'roomAssignments' => $roomAssignments,
            'singleRoom' => $room,
        ]);
        
        return $pdf;
    }
}

