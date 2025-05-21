<?php

namespace App\Services;

use App\Models\Room;
use App\Models\Student;
use App\Models\SeatingPlan;
use App\Models\SeatingRule;
use App\Models\StudentPriority;
use App\Models\SeatingOverride;
use Illuminate\Support\Collection;

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
                return $this->applyPriorityRule($rule, $students, $rooms, $currentAssignments);
            
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
        
        // Implement the alternate courses logic
        // This is a simplified version - in a real implementation, you would need more complex logic
        
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
            
            // Get courses with remaining students
            $coursesWithStudents = $studentsByCourse->filter(function ($students) {
                return $students->isNotEmpty();
            });
            
            // Skip if no courses with students
            if ($coursesWithStudents->isEmpty()) {
                continue;
            }
            
            // Assign seats in alternating pattern
            $courseIds = $coursesWithStudents->keys()->toArray();
            $courseIndex = 0;
            
            for ($seatNumber = 1; $seatNumber <= $capacity; $seatNumber++) {
                // Skip if seat is already assigned
                if (isset($currentAssignments[$roomId][$seatNumber])) {
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
                        break;
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
        
        // Implement the distance logic
        // This is a simplified version - in a real implementation, you would need more complex logic
        
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
            
            // Assign seats with distance
            for ($seatNumber = 1; $seatNumber <= $capacity; $seatNumber += ($distance + 1)) {
                // Skip if seat is already assigned
                if (isset($currentAssignments[$roomId][$seatNumber])) {
                    continue;
                }
                
                // Get a student
                $student = $students->shift();
                
                // If no more students, break
                if (!$student) {
                    break;
                }
                
                // Assign the student to this seat
                $currentAssignments[$roomId][$seatNumber] = $student->id;
            }
        }
        
        return $currentAssignments;
    }
    
    /**
     * Apply the priority rule.
     * This rule ensures students with priorities are seated according to their needs.
     *
     * @param SeatingRule $rule
     * @param Collection $students
     * @param Collection $rooms
     * @param array $currentAssignments
     * @return array
     */
    protected function applyPriorityRule(SeatingRule $rule, Collection $students, Collection $rooms, array $currentAssignments): array
    {
        // Initialize assignments if empty
        if (empty($currentAssignments)) {
            $currentAssignments = $this->initializeAssignments($rooms);
        }
        
        // Get parameters from the rule
        $parameters = $rule->parameters ?? [];
        
        // Get students with priorities
        $studentsWithPriorities = $students->filter(function ($student) {
            return $student->has_disability || $student->priorities()->where('valid_until', '>=', now())->exists();
        });
        
        // Sort students by priority level
        $studentsWithPriorities = $studentsWithPriorities->sortByDesc(function ($student) {
            $priority = $student->priorities()->where('valid_until', '>=', now())->orderBy('priority_level', 'desc')->first();
            return $priority ? $priority->priority_level : ($student->has_disability ? 10 : 0);
        });
        
        // Remove students with priorities from the main collection
        $students = $students->diff($studentsWithPriorities);
        
        // Implement the priority logic
        // This is a simplified version - in a real implementation, you would need more complex logic
        
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
            
            // Assign priority seats first (e.g., front row, near door, etc.)
            $prioritySeats = $this->getPrioritySeats($room, $parameters);
            
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
        // This is a simplified version - in a real implementation, you would need more complex logic
        // based on the room layout and specific needs
        
        $prioritySeats = [];
        
        // Front row seats (assuming seats are numbered in rows)
        $seatsPerRow = $parameters['seats_per_row'] ?? 5;
        for ($i = 1; $i <= $seatsPerRow; $i++) {
            $prioritySeats[] = $i;
        }
        
        // Seats near the door (assuming the door is at a specific location)
        $doorSeat = $parameters['door_seat'] ?? 1;
        $prioritySeats[] = $doorSeat;
        
        // Remove duplicates
        $prioritySeats = array_unique($prioritySeats);
        
        return $prioritySeats;
    }
}

