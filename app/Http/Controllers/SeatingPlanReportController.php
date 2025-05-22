<?php

namespace App\Http\Controllers;

use App\Models\SeatingPlan;
use App\Models\Room;
use App\Services\SeatingPlanPdfService;
use App\Services\InvigilatorReportService;
use App\Services\StudentNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class SeatingPlanReportController extends Controller
{
    protected $pdfService;
    protected $reportService;
    protected $notificationService;
    
    /**
     * Create a new controller instance.
     *
     * @param SeatingPlanPdfService $pdfService
     * @param InvigilatorReportService $reportService
     * @param StudentNotificationService $notificationService
     */
    public function __construct(
        SeatingPlanPdfService $pdfService,
        InvigilatorReportService $reportService,
        StudentNotificationService $notificationService
    ) {
        $this->pdfService = $pdfService;
        $this->reportService = $reportService;
        $this->notificationService = $notificationService;
    }
    
    /**
     * Display the reports index page.
     *
     * @param SeatingPlan $seatingPlan
     * @return \Illuminate\Http\Response
     */
    public function index(SeatingPlan $seatingPlan)
    {
        return view('seating.reports.index', compact('seatingPlan'));
    }
    
    /**
     * Generate and download a seating plan PDF.
     *
     * @param SeatingPlan $seatingPlan
     * @return \Illuminate\Http\Response
     */
    public function downloadSeatingPlanPdf(SeatingPlan $seatingPlan)
    {
        $pdf = $this->pdfService->generatePdf($seatingPlan);
        
        return $pdf->download("seating_plan_{$seatingPlan->id}.pdf");
    }
    
    /**
     * Generate and stream a seating plan PDF.
     *
     * @param SeatingPlan $seatingPlan
     * @return \Illuminate\Http\Response
     */
    public function viewSeatingPlanPdf(SeatingPlan $seatingPlan)
    {
        $pdf = $this->pdfService->generatePdf($seatingPlan);
        
        return $pdf->stream("seating_plan_{$seatingPlan->id}.pdf");
    }
    
    /**
     * Generate and download a room seating plan PDF.
     *
     * @param SeatingPlan $seatingPlan
     * @param Room $room
     * @return \Illuminate\Http\Response
     */
    public function downloadRoomPdf(SeatingPlan $seatingPlan, Room $room)
    {
        $pdf = $this->pdfService->generateRoomPdf($seatingPlan, $room);
        
        return $pdf->download("room_{$room->room_number}_seating_plan.pdf");
    }
    
    /**
     * Generate and download student seating cards PDF.
     *
     * @param SeatingPlan $seatingPlan
     * @return \Illuminate\Http\Response
     */
    public function downloadStudentCardsPdf(SeatingPlan $seatingPlan)
    {
        $pdf = $this->pdfService->generateStudentCardsPdf($seatingPlan);
        
        return $pdf->download("student_cards_{$seatingPlan->id}.pdf");
    }
    
    /**
     * Generate and download invigilator report PDF.
     *
     * @param SeatingPlan $seatingPlan
     * @return \Illuminate\Http\Response
     */
    public function downloadInvigilatorReportPdf(SeatingPlan $seatingPlan)
    {
        $pdf = $this->pdfService->generateInvigilatorReportPdf($seatingPlan);
        
        return $pdf->download("invigilator_report_{$seatingPlan->id}.pdf");
    }
    
    /**
     * Generate and download attendance sheet PDF.
     *
     * @param SeatingPlan $seatingPlan
     * @param Room|null $room
     * @return \Illuminate\Http\Response
     */
    public function downloadAttendanceSheetPdf(SeatingPlan $seatingPlan, Room $room = null)
    {
        $pdf = $this->pdfService->generateAttendanceSheetPdf($seatingPlan, $room);
        
        $filename = $room 
            ? "attendance_sheet_{$room->room_number}.pdf"
            : "attendance_sheet_{$seatingPlan->id}.pdf";
        
        return $pdf->download($filename);
    }
    
    /**
     * Send notifications to all students.
     *
     * @param SeatingPlan $seatingPlan
     * @return \Illuminate\Http\Response
     */
    public function notifyAllStudents(SeatingPlan $seatingPlan)
    {
        $results = $this->notificationService->notifyAllStudents($seatingPlan);
        
        return redirect()->back()->with('success', "Notifications sent: {$results['success']} successful, {$results['failed']} failed, {$results['skipped']} skipped.");
    }
    
    /**
     * Send reminder notifications to all students.
     *
     * @param SeatingPlan $seatingPlan
     * @return \Illuminate\Http\Response
     */
    public function sendReminders(SeatingPlan $seatingPlan)
    {
        $results = $this->notificationService->sendReminders($seatingPlan);
        
        return redirect()->back()->with('success', "Reminders sent: {$results['success']} successful, {$results['failed']} failed, {$results['skipped']} skipped.");
    }
    
    /**
     * Log an incident during an exam.
     *
     * @param Request $request
     * @param SeatingPlan $seatingPlan
     * @return \Illuminate\Http\Response
     */
    public function logIncident(Request $request, SeatingPlan $seatingPlan)
    {
        $request->validate([
            'incident_type' => 'required|string',
            'description' => 'required|string',
            'room_id' => 'nullable|exists:rooms,id',
            'student_id' => 'nullable|exists:students,id',
        ]);
        
        $incidentData = $request->only(['incident_type', 'description', 'room_id', 'student_id']);
        $incidentData['reported_by'] = auth()->user()->name ?? 'System';
        $incidentData['reported_at'] = now();
        
        $success = $this->reportService->logIncident($seatingPlan, $incidentData);
        
        if ($success) {
            return redirect()->back()->with('success', 'Incident logged successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to log incident.');
        }
    }
}

