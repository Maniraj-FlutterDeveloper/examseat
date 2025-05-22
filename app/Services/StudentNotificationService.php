<?php

namespace App\Services;

use App\Models\SeatingPlan;
use App\Models\Student;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class StudentNotificationService
{
    /**
     * Send seating plan notifications to all students
     *
     * @param SeatingPlan $seatingPlan
     * @return array
     */
    public function notifyAllStudents(SeatingPlan $seatingPlan)
    {
        // Get all assignments for this seating plan
        $assignments = $seatingPlan->assignments()->with(['student', 'room'])->get();
        
        $results = [
            'total' => $assignments->count(),
            'success' => 0,
            'failed' => 0,
            'skipped' => 0,
        ];
        
        foreach ($assignments as $assignment) {
            $student = $assignment->student;
            
            // Skip if student has no email
            if (empty($student->email)) {
                $results['skipped']++;
                continue;
            }
            
            $success = $this->notifyStudent($student, $seatingPlan, $assignment);
            
            if ($success) {
                $results['success']++;
            } else {
                $results['failed']++;
            }
        }
        
        return $results;
    }
    
    /**
     * Send seating plan notification to a specific student
     *
     * @param Student $student
     * @param SeatingPlan $seatingPlan
     * @param mixed $assignment
     * @return bool
     */
    public function notifyStudent(Student $student, SeatingPlan $seatingPlan, $assignment = null)
    {
        // If no assignment is provided, find it
        if (!$assignment) {
            $assignment = $seatingPlan->assignments()
                ->where('student_id', $student->id)
                ->with('room')
                ->first();
            
            // Skip if student has no assignment
            if (!$assignment) {
                return false;
            }
        }
        
        try {
            // In a real implementation, we would send an email here
            // For now, we'll just log it
            Log::info("Notification sent to student {$student->name} ({$student->email}) for seating plan {$seatingPlan->exam_name}");
            
            // Record that the notification was sent
            $assignment->update([
                'notification_sent' => true,
                'notification_sent_at' => now(),
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send notification to student {$student->name}: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send reminder notifications to all students
     *
     * @param SeatingPlan $seatingPlan
     * @return array
     */
    public function sendReminders(SeatingPlan $seatingPlan)
    {
        // Get all assignments for this seating plan
        $assignments = $seatingPlan->assignments()->with(['student', 'room'])->get();
        
        $results = [
            'total' => $assignments->count(),
            'success' => 0,
            'failed' => 0,
            'skipped' => 0,
        ];
        
        foreach ($assignments as $assignment) {
            $student = $assignment->student;
            
            // Skip if student has no email
            if (empty($student->email)) {
                $results['skipped']++;
                continue;
            }
            
            $success = $this->sendReminder($student, $seatingPlan, $assignment);
            
            if ($success) {
                $results['success']++;
            } else {
                $results['failed']++;
            }
        }
        
        return $results;
    }
    
    /**
     * Send a reminder notification to a specific student
     *
     * @param Student $student
     * @param SeatingPlan $seatingPlan
     * @param mixed $assignment
     * @return bool
     */
    public function sendReminder(Student $student, SeatingPlan $seatingPlan, $assignment = null)
    {
        // If no assignment is provided, find it
        if (!$assignment) {
            $assignment = $seatingPlan->assignments()
                ->where('student_id', $student->id)
                ->with('room')
                ->first();
            
            // Skip if student has no assignment
            if (!$assignment) {
                return false;
            }
        }
        
        try {
            // In a real implementation, we would send an email here
            // For now, we'll just log it
            Log::info("Reminder sent to student {$student->name} ({$student->email}) for seating plan {$seatingPlan->exam_name}");
            
            // Record that the reminder was sent
            $assignment->update([
                'reminder_sent' => true,
                'reminder_sent_at' => now(),
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send reminder to student {$student->name}: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Generate a QR code for a student's seating assignment
     *
     * @param Student $student
     * @param SeatingPlan $seatingPlan
     * @return string|null
     */
    public function generateQrCode(Student $student, SeatingPlan $seatingPlan)
    {
        // Find the student's assignment
        $assignment = $seatingPlan->assignments()
            ->where('student_id', $student->id)
            ->with('room')
            ->first();
        
        // Skip if student has no assignment
        if (!$assignment) {
            return null;
        }
        
        // Generate QR code data
        $qrData = [
            'student_id' => $student->id,
            'student_name' => $student->name,
            'roll_number' => $student->roll_number,
            'exam_name' => $seatingPlan->exam_name,
            'exam_date' => $seatingPlan->exam_date,
            'room_number' => $assignment->room->room_number,
            'seat_number' => $assignment->seat_number,
        ];
        
        // Convert to JSON
        $qrJson = json_encode($qrData);
        
        // In a real implementation, we would generate a QR code here
        // For now, we'll just return the data
        return $qrJson;
    }
}

