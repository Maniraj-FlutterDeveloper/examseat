<?php

namespace App\Services;

use App\Models\Room;
use App\Models\Student;
use App\Models\SeatingPlan;
use App\Models\SeatingRule;
use App\Models\StudentPriority;
use App\Models\SeatingOverride;
use App\Models\SeatingAssignment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SeatingRuleService
{
    /**
     * Apply all active seating rules to generate a seating plan.
     *
     * @param SeatingPlan $seatingPlan
     * @param Collection $students
     * @param Collection $rooms
     * @return array
     */
    public function applyRules(SeatingPlan $seatingPlan, Collection $students, Collection $rooms): array
    {
        // Get all active rules ordered by priority
        $rules = SeatingRule::where('is_active', true)
            ->orderBy('priority', 'desc')
            ->get();
        
        // Initialize the seating assignments array
        $assignments = [];
        
        // Apply each rule in order
        foreach ($rules as $rule) {
            $assignments = $this->applyRule($rule, $seatingPlan, $students, $rooms, $assignments);
        }
        
        // Apply any manual overrides
        $assignments = $this->applyOverrides($seatingPlan, $assignments);
        
        return $assignments;
    }
    
    /**
     * Apply a specific seating rule.
     *
     * @param SeatingRule $rule
     * @param SeatingPlan $seatingPlan
     * @param Collection $students
     * @param Collection $rooms
     * @param array $currentAssignments
     * @return array
     */
    protected function applyRule(SeatingRule $rule, SeatingPlan $seatingPlan, Collection $students, Collection $rooms, array $currentAssignments): array
    {
        // Apply different rules based on the rule type
        switch ($rule->type) {
            case 'alternate_courses':
                return $this->applyAlternateCoursesRule($rule, $students, $rooms, $currentAssignments);
            
            case 'distance':
                return $this->applyDistanceRule($rule, $students, $rooms, $currentAssignments);
            
            case 'priority':
                return $this->applyPriorityRule($rule, $seatingPlan, $students, $rooms, $currentAssignments);
            
            default:
                return $currentAssignments;
        }
    }
    
    /**
     * Apply the alternate courses rule.
     * This rule ensures students from different courses are seated next to each other.
     *
     * @param SeatingRule $rule
     * @param Collection $students
     * @param Collection $rooms
     * @param array $currentAssignments
     * @return array
     */
    protected function applyAlternateCoursesRule(SeatingRule $rule, Collection $students, Collection $rooms, array $currentAssignments): array
    {
        // Group students by course
        $studentsByCourse = $students->groupBy('course_id');
        
        // Initialize assignments if empty
        if (empty($currentAssignments)) {
            $currentAssignments = $this->initializeAssignments($rooms);
        }
        
        // Get parameters from the rule
        $parameters = $rule->parameters ?? [];
        $minDistance = $parameters['min_distance'] ?? 1;
        
        // For each room
        foreach ($rooms as $room) {
            $roomId = $room->id;
            $capacity = $room->capacity;
            $seatsPerRow = $room->layout['seats_per_row'] ?? 5;
            $totalRows = ceil($capacity / $seatsPerRow);
            
            // Skip if room is already fully assigned
            if (isset($currentAssignments[$roomId]) && count($currentAssignments[$roomId]) >= $capacity) {
                continue;
            }
            
            // Initialize room assignments if not set
            if (!isset($currentAssignments[$roomId])) {
                $currentAssignments[$roomId] = [];
            }
            
            // Get courses with remaining students
            $coursesWithStudents = $studentsByCourse->filter(function ($students) {
                return $students->isNotEmpty();
            });
            
            // Skip if no courses with students
            if ($coursesWithStudents->isEmpty()) {
                continue;
            }
            
            // Create a map of the room with rows and columns
            $seatMap = [];
            for ($row = 0; $row < $totalRows; $row++) {
                for ($col = 0; $col < $seatsPerRow; $col++) {
                    $seatNumber = ($row * $seatsPerRow) + $col + 1;
                    if ($seatNumber <= $capacity) {
                        $seatMap[$row][$col] = [
                            'seat_number' => $seatNumber,
                            'assigned' => isset($currentAssignments[$roomId][$seatNumber]),
                            'course_id' => isset($currentAssignments[$roomId][$seatNumber]) ? 
                                Student::find($currentAssignments[$roomId][$seatNumber])->course_id : null
                        ];
                    }
                }
            }
            
            // Assign seats in alternating pattern
            $courseIds = $coursesWithStudents->keys()->toArray();
            $courseIndex = 0;
            
            // First pass: assign seats with alternating courses
            for ($row = 0; $row < $totalRows; $row++) {
                for ($col = 0; $col < $seatsPerRow; $col++) {
                    if (!isset($seatMap[$row][$col]) || $seatMap[$row][$col]['assigned']) {
                        continue;
                    }
                    
                    $seatNumber = $seatMap[$row][$col]['seat_number'];
                    
                    // Check if this seat violates the minimum distance rule
                    $validSeat = true;
                    for ($checkRow = max(0, $row - $minDistance); $checkRow <= min($totalRows - 1, $row + $minDistance); $checkRow++) {
                        for ($checkCol = max(0, $col - $minDistance); $checkCol <= min($seatsPerRow - 1, $col + $minDistance); $checkCol++) {
                            if (isset($seatMap[$checkRow][$checkCol]) && 
                                $seatMap[$checkRow][$checkCol]['assigned'] && 
                                $courseIds[$courseIndex] == $seatMap[$checkRow][$checkCol]['course_id']) {
                                $validSeat = false;
                                break 2;
                            }
                        }
                    }
                    
                    if (!$validSeat) {
                        continue;
                    }
                    
                    // Get the current course
                    $courseId = $courseIds[$courseIndex];
                    
                    // Get a student from this course
                    $student = $studentsByCourse[$courseId]->shift();
                    
                    // If no more students in this course, remove it from the list
                    if ($studentsByCourse[$courseId]->isEmpty()) {
                        $studentsByCourse->forget($courseId);
                        $coursesWithStudents = $studentsByCourse->filter(function ($students) {
                            return $students->isNotEmpty();
                        });
                        $courseIds = $coursesWithStudents->keys()->toArray();
                        
                        // If no more courses with students, break
                        if (empty($courseIds)) {
                            break 2;
                        }
                        
                        // Reset course index if needed
                        $courseIndex = $courseIndex % count($courseIds);
                    } else {
                        // Move to the next course
                        $courseIndex = ($courseIndex + 1) % count($courseIds);
                    }
                    
                    // Assign the student to this seat
                    if ($student) {
                        $currentAssignments[$roomId][$seatNumber] = $student->id;
                        $seatMap[$row][$col]['assigned'] = true;
                        $seatMap[$row][$col]['course_id'] = $student->course_id;
                    }
                }
            }
            
            // Second pass: fill in any remaining seats with any remaining students
            if (!$coursesWithStudents->isEmpty()) {
                for ($row = 0; $row < $totalRows; $row++) {
                    for ($col = 0; $col < $seatsPerRow; $col++) {
                        if (!isset($seatMap[$row][$col]) || $seatMap[$row][$col]['assigned']) {
                            continue;
                        }
                        
                        $seatNumber = $seatMap[$row][$col]['seat_number'];
                        
                        // Find a course with available students
                        foreach ($coursesWithStudents as $courseId => $courseStudents) {
                            if ($courseStudents->isEmpty()) {
                                continue;
                            }
                            
                            // Get a student from this course
                            $student = $courseStudents->shift();
                            
                            // If no more students in this course, remove it from the list
                            if ($courseStudents->isEmpty()) {
                                $studentsByCourse->forget($courseId);
                                $coursesWithStudents = $studentsByCourse->filter(function ($students) {
                                    return $students->isNotEmpty();
                                });
                            }
                            
                            // Assign the student to this seat
                            if ($student) {
                                $currentAssignments[$roomId][$seatNumber] = $student->id;
                                $seatMap[$row][$col]['assigned'] = true;
                                $seatMap[$row][$col]['course_id'] = $student->course_id;
                                break;
                            }
                        }
                        
                        // If no more courses with students, break
                        if ($coursesWithStudents->isEmpty()) {
                            break 2;
                        }
                    }
                }
            }
        }
        
        return $currentAssignments;
    }
    
    /**
     * Apply the distance rule.
     * This rule ensures students are seated with appropriate distance between them.
     *
     * @param SeatingRule $rule
     * @param Collection $students
     * @param Collection $rooms
     * @param array $currentAssignments
     * @return array
     */
    protected function applyDistanceRule(SeatingRule $rule, Collection $students, Collection $rooms, array $currentAssignments): array
    {
        // Initialize assignments if empty
        if (empty($currentAssignments)) {
            $currentAssignments = $this->initializeAssignments($rooms);
        }
        
        // Get parameters from the rule
        $parameters = $rule->parameters ?? [];
        $distance = $parameters['distance'] ?? 1; // Default to 1 seat distance
        
        // For each room
        foreach ($rooms as $room) {
            $roomId = $room->id;
            $capacity = $room->capacity;
            $seatsPerRow = $room->layout['seats_per_row'] ?? 5;
            $totalRows = ceil($capacity / $seatsPerRow);
            
            // Skip if room is already fully assigned
            if (isset($currentAssignments[$roomId]) && count($currentAssignments[$roomId]) >= $capacity) {
                continue;
            }
            
            // Initialize room assignments if not set
            if (!isset($currentAssignments[$roomId])) {
                $currentAssignments[$roomId] = [];
            }
            
            // Create a map of the room with rows and columns
            $seatMap = [];
            for ($row = 0; $row < $totalRows; $row++) {
                for ($col = 0; $col < $seatsPerRow; $col++) {
                    $seatNumber = ($row * $seatsPerRow) + $col + 1;
                    if ($seatNumber <= $capacity) {
                        $seatMap[$row][$col] = [
                            'seat_number' => $seatNumber,
                            'assigned' => isset($currentAssignments[$roomId][$seatNumber])
                        ];
                    }
                }
            }
            
            // Assign seats with distance
            for ($row = 0; $row < $totalRows; $row += $distance) {
                for ($col = 0; $col < $seatsPerRow; $col += $distance) {
                    if (!isset($seatMap[$row][$col]) || $seatMap[$row][$col]['assigned']) {
                        continue;
                    }
                    
                    $seatNumber = $seatMap[$row][$col]['seat_number'];
                    
                    // Get a student
                    $student = $students->shift();
                    
                    // If no more students, break
                    if (!$student) {
                        break 2;
                    }
                    
                    // Assign the student to this seat
                    $currentAssignments[$roomId][$seatNumber] = $student->id;
                    $seatMap[$row][$col]['assigned'] = true;
                }
            }
            
            // Second pass: fill in any remaining seats with any remaining students
            if ($students->isNotEmpty()) {
                for ($row = 0; $row < $totalRows; $row++) {
                    for ($col = 0; $col < $seatsPerRow; $col++) {
                        if (!isset($seatMap[$row][$col]) || $seatMap[$row][$col]['assigned']) {
                            continue;
                        }
                        
                        $seatNumber = $seatMap[$row][$col]['seat_number'];
                        
                        // Get a student
                        $student = $students->shift();
                        
                        // If no more students, break
                        if (!$student) {
                            break 2;
                        }
                        
                        // Assign the student to this seat
                        $currentAssignments[$roomId][$seatNumber] = $student->id;
                        $seatMap[$row][$col]['assigned'] = true;
                    }
                }
            }
        }
        
        return $currentAssignments;
    }
    
    /**
     * Apply the priority rule.
     * This rule ensures students with priorities are seated according to their needs.
     *
     * @param SeatingRule $rule
     * @param SeatingPlan $seatingPlan
     * @param Collection $students
     * @param Collection $rooms
     * @param array $currentAssignments
     * @return array
     */
    protected function applyPriorityRule(SeatingRule $rule, SeatingPlan $seatingPlan, Collection $students, Collection $rooms, array $currentAssignments): array
    {
        // Initialize assignments if empty
        if (empty($currentAssignments)) {
            $currentAssignments = $this->initializeAssignments($rooms);
        }
        
        // Get parameters from the rule
        $parameters = $rule->parameters ?? [];
        
        // Get students with priorities
        $studentsWithPriorities = $students->filter(function ($student) {
            return $student->has_disability || $student->priorities()->where(function($query) {
                $query->whereNull('valid_until')->orWhere('valid_until', '>=', now());
            })->exists();
        });
        
        // Sort students by priority level
        $studentsWithPriorities = $studentsWithPriorities->sortByDesc(function ($student) {
            $priority = $student->priorities()->where(function($query) {
                $query->whereNull('valid_until')->orWhere('valid_until', '>=', now());
            })->orderBy('priority_level', 'desc')->first();
            
            return $priority ? $priority->priority_level : ($student->has_disability ? 10 : 0);
        });
        
        // Remove students with priorities from the main collection
        $students = $students->diff($studentsWithPriorities);
        
        // For each room
        foreach ($rooms as $room) {
            $roomId = $room->id;
            $capacity = $room->capacity;
            
            // Skip if room is already fully assigned
            if (isset($currentAssignments[$roomId]) && count($currentAssignments[$roomId]) >= $capacity) {
                continue;
            }
            
            // Initialize room assignments if not set
            if (!isset($currentAssignments[$roomId])) {
                $currentAssignments[$roomId] = [];
            }
            
            // Get available seats in this room
            $availableSeats = $capacity - count($currentAssignments[$roomId]);
            
            // Skip if no available seats
            if ($availableSeats <= 0) {
                continue;
            }
            
            // Get priority seats for this room
            $prioritySeats = $this->getPrioritySeats($room, $parameters);
            
            // Assign priority students to priority seats
            foreach ($prioritySeats as $seatNumber) {
                // Skip if seat is already assigned
                if (isset($currentAssignments[$roomId][$seatNumber])) {
                    continue;
                }
                
                // Get a student with priority
                $student = $studentsWithPriorities->shift();
                
                // If no more students with priority, break
                if (!$student) {
                    break;
                }
                
                // Assign the student to this seat
                $currentAssignments[$roomId][$seatNumber] = $student->id;
            }
            
            // If there are still priority students, assign them to other available seats
            if ($studentsWithPriorities->isNotEmpty()) {
                for ($seatNumber = 1; $seatNumber <= $capacity; $seatNumber++) {
                    // Skip if seat is already assigned or is a priority seat
                    if (isset($currentAssignments[$roomId][$seatNumber]) || in_array($seatNumber, $prioritySeats)) {
                        continue;
                    }
                    
                    // Get a student with priority
                    $student = $studentsWithPriorities->shift();
                    
                    // If no more students with priority, break
                    if (!$student) {
                        break;
                    }
                    
                    // Assign the student to this seat
                    $currentAssignments[$roomId][$seatNumber] = $student->id;
                }
            }
        }
        
        return $currentAssignments;
    }
    
    /**
     * Apply manual overrides to the seating assignments.
     *
     * @param SeatingPlan $seatingPlan
     * @param array $assignments
     * @return array
     */
    protected function applyOverrides(SeatingPlan $seatingPlan, array $assignments): array
    {
        // Get all overrides for this seating plan
        $overrides = SeatingOverride::where('seating_plan_id', $seatingPlan->id)->get();
        
        // Apply each override
        foreach ($overrides as $override) {
            $roomId = $override->room_id;
            $seatNumber = $override->seat_number;
            $studentId = $override->student_id;
            
            // Initialize room assignments if not set
            if (!isset($assignments[$roomId])) {
                $assignments[$roomId] = [];
            }
            
            // Apply the override
            $assignments[$roomId][$seatNumber] = $studentId;
        }
        
        return $assignments;
    }
    
    /**
     * Initialize empty assignments for all rooms.
     *
     * @param Collection $rooms
     * @return array
     */
    protected function initializeAssignments(Collection $rooms): array
    {
        $assignments = [];
        
        foreach ($rooms as $room) {
            $assignments[$room->id] = [];
        }
        
        return $assignments;
    }
    
    /**
     * Get priority seats for a room based on parameters.
     *
     * @param Room $room
     * @param array $parameters
     * @return array
     */
    protected function getPrioritySeats(Room $room, array $parameters): array
    {
        $prioritySeats = [];
        $capacity = $room->capacity;
        $seatsPerRow = $parameters['seats_per_row'] ?? ($room->layout['seats_per_row'] ?? 5);
        $totalRows = ceil($capacity / $seatsPerRow);
        
        // Front row seats
        for ($i = 1; $i <= $seatsPerRow && $i <= $capacity; $i++) {
            $prioritySeats[] = $i;
        }
        
        // Aisle seats (assuming aisles are at the edges of each row)
        for ($row = 0; $row < $totalRows; $row++) {
            // Left edge of row
            $leftSeat = ($row * $seatsPerRow) + 1;
            if ($leftSeat <= $capacity) {
                $prioritySeats[] = $leftSeat;
            }
            
            // Right edge of row
            $rightSeat = ($row + 1) * $seatsPerRow;
            if ($rightSeat <= $capacity) {
                $prioritySeats[] = $rightSeat;
            }
        }
        
        // Seats near the door (assuming the door is at a specific location)
        $doorSeat = $parameters['door_seat'] ?? 1;
        $prioritySeats[] = $doorSeat;
        
        // Seats near accessible facilities (if specified)
        if (isset($parameters['accessible_seats'])) {
            foreach ($parameters['accessible_seats'] as $seat) {
                if ($seat <= $capacity) {
                    $prioritySeats[] = $seat;
                }
            }
        }
        
        // Remove duplicates and sort
        $prioritySeats = array_unique($prioritySeats);
        sort($prioritySeats);
        
        return $prioritySeats;
    }
    
    /**
     * Save the generated seating assignments to the database.
     *
     * @param SeatingPlan $seatingPlan
     * @param array $assignments
     * @return bool
     */
    public function saveAssignments(SeatingPlan $seatingPlan, array $assignments): bool
    {
        try {
            // Start a database transaction
            DB::beginTransaction();
            
            // Delete any existing assignments for this seating plan
            SeatingAssignment::where('seating_plan_id', $seatingPlan->id)->delete();
            
            // Create new assignments
            foreach ($assignments as $roomId => $roomAssignments) {
                foreach ($roomAssignments as $seatNumber => $studentId) {
                    // Check if this is an override
                    $isOverride = SeatingOverride::where('seating_plan_id', $seatingPlan->id)
                        ->where('room_id', $roomId)
                        ->where('seat_number', $seatNumber)
                        ->exists();
                    
                    // Create the assignment
                    SeatingAssignment::create([
                        'seating_plan_id' => $seatingPlan->id,
                        'student_id' => $studentId,
                        'room_id' => $roomId,
                        'seat_number' => $seatNumber,
                        'is_override' => $isOverride,
                    ]);
                }
            }
            
            // Update the seating plan status if it's still scheduled
            if ($seatingPlan->status === 'scheduled') {
                $seatingPlan->update(['status' => 'ready']);
            }
            
            // Commit the transaction
            DB::commit();
            
            return true;
        } catch (\Exception $e) {
            // Rollback the transaction if something goes wrong
            DB::rollBack();
            
            // Log the error
            \Log::error('Error saving seating assignments: ' . $e->getMessage());
            
            return false;
        }
    }
}

