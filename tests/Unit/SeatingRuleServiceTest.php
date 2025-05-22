<?php

namespace Tests\Unit;

use App\Models\Room;
use App\Models\Block;
use App\Models\Student;
use App\Models\Course;
use App\Models\SeatingPlan;
use App\Models\SeatingRule;
use App\Models\StudentPriority;
use App\Models\SeatingOverride;
use App\Models\SeatingAssignment;
use App\Services\SeatingRuleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeatingRuleServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $seatingRuleService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seatingRuleService = new SeatingRuleService();
    }

    /**
     * Test applying alternate courses rule.
     *
     * @return void
     */
    public function test_apply_alternate_courses_rule()
    {
        // Create test data
        $block = Block::factory()->create();
        $room = Room::factory()->create([
            'block_id' => $block->id,
            'capacity' => 10,
            'layout' => ['seats_per_row' => 5]
        ]);
        
        $course1 = Course::factory()->create(['course_name' => 'Course A']);
        $course2 = Course::factory()->create(['course_name' => 'Course B']);
        
        $students1 = Student::factory()->count(3)->create(['course_id' => $course1->id]);
        $students2 = Student::factory()->count(3)->create(['course_id' => $course2->id]);
        
        $allStudents = $students1->merge($students2);
        
        $seatingPlan = SeatingPlan::factory()->create(['room_id' => $room->id]);
        
        // Create rule
        $rule = SeatingRule::factory()->create([
            'type' => 'alternate_courses',
            'parameters' => ['min_distance' => 1],
            'is_active' => true,
        ]);
        
        // Apply rule using reflection to access protected method
        $method = new \ReflectionMethod(SeatingRuleService::class, 'applyAlternateCoursesRule');
        $method->setAccessible(true);
        
        $assignments = $method->invoke($this->seatingRuleService, $rule, $allStudents, collect([$room]), []);
        
        // Check that assignments were generated
        $this->assertNotEmpty($assignments);
        $this->assertArrayHasKey($room->id, $assignments);
        $this->assertCount(6, $assignments[$room->id]);
        
        // Check that students from different courses are alternated
        $assignedStudents = [];
        foreach ($assignments[$room->id] as $seatNumber => $studentId) {
            $assignedStudents[$seatNumber] = Student::find($studentId);
        }
        
        // Sort by seat number
        ksort($assignedStudents);
        
        // Check alternating pattern (this is a simplified check)
        $previousCourseId = null;
        $alternatingPattern = true;
        
        foreach ($assignedStudents as $student) {
            if ($previousCourseId !== null && $previousCourseId === $student->course_id) {
                // Two students from the same course are adjacent
                $alternatingPattern = false;
                break;
            }
            $previousCourseId = $student->course_id;
        }
        
        $this->assertTrue($alternatingPattern, 'Students should be alternated by course');
    }

    /**
     * Test applying distance rule.
     *
     * @return void
     */
    public function test_apply_distance_rule()
    {
        // Create test data
        $block = Block::factory()->create();
        $room = Room::factory()->create([
            'block_id' => $block->id,
            'capacity' => 10,
            'layout' => ['seats_per_row' => 5]
        ]);
        
        $course = Course::factory()->create();
        $students = Student::factory()->count(5)->create(['course_id' => $course->id]);
        
        $seatingPlan = SeatingPlan::factory()->create(['room_id' => $room->id]);
        
        // Create rule
        $rule = SeatingRule::factory()->create([
            'type' => 'distance',
            'parameters' => ['distance' => 2], // 2 seats distance
            'is_active' => true,
        ]);
        
        // Apply rule using reflection to access protected method
        $method = new \ReflectionMethod(SeatingRuleService::class, 'applyDistanceRule');
        $method->setAccessible(true);
        
        $assignments = $method->invoke($this->seatingRuleService, $rule, $students, collect([$room]), []);
        
        // Check that assignments were generated
        $this->assertNotEmpty($assignments);
        $this->assertArrayHasKey($room->id, $assignments);
        
        // Check that students are seated with appropriate distance
        $assignedSeats = array_keys($assignments[$room->id]);
        sort($assignedSeats);
        
        // With distance=2, seats should be at least 3 positions apart (1, 4, 7, 10, ...)
        for ($i = 0; $i < count($assignedSeats) - 1; $i++) {
            $this->assertGreaterThanOrEqual(3, $assignedSeats[$i+1] - $assignedSeats[$i], 'Seats should be at least 3 positions apart');
        }
    }

    /**
     * Test applying priority rule.
     *
     * @return void
     */
    public function test_apply_priority_rule()
    {
        // Create test data
        $block = Block::factory()->create();
        $room = Room::factory()->create([
            'block_id' => $block->id,
            'capacity' => 10,
            'layout' => ['seats_per_row' => 5]
        ]);
        
        $course = Course::factory()->create();
        
        // Create a student with priority
        $studentWithPriority = Student::factory()->create([
            'course_id' => $course->id,
            'has_disability' => true,
        ]);
        
        // Create a student priority record
        StudentPriority::factory()->create([
            'student_id' => $studentWithPriority->id,
            'priority_type' => 'disability',
            'priority_level' => 10,
            'is_verified' => true,
        ]);
        
        // Create regular students
        $regularStudents = Student::factory()->count(5)->create([
            'course_id' => $course->id,
            'has_disability' => false,
        ]);
        
        $allStudents = collect([$studentWithPriority])->merge($regularStudents);
        
        $seatingPlan = SeatingPlan::factory()->create(['room_id' => $room->id]);
        
        // Create rule
        $rule = SeatingRule::factory()->create([
            'type' => 'priority',
            'parameters' => [
                'seats_per_row' => 5,
                'door_seat' => 1,
            ],
            'is_active' => true,
        ]);
        
        // Apply rule using reflection to access protected method
        $method = new \ReflectionMethod(SeatingRuleService::class, 'applyPriorityRule');
        $method->setAccessible(true);
        
        $assignments = $method->invoke($this->seatingRuleService, $rule, $seatingPlan, $allStudents, collect([$room]), []);
        
        // Check that assignments were generated
        $this->assertNotEmpty($assignments);
        $this->assertArrayHasKey($room->id, $assignments);
        
        // Check that the student with priority is assigned to a priority seat (front row or near door)
        $prioritySeats = [1, 2, 3, 4, 5]; // Front row seats
        
        $studentWithPrioritySeat = null;
        foreach ($assignments[$room->id] as $seatNumber => $studentId) {
            if ($studentId === $studentWithPriority->id) {
                $studentWithPrioritySeat = $seatNumber;
                break;
            }
        }
        
        $this->assertNotNull($studentWithPrioritySeat, 'Student with priority should be assigned a seat');
        $this->assertContains($studentWithPrioritySeat, $prioritySeats, 'Student with priority should be assigned a priority seat');
    }

    /**
     * Test applying overrides.
     *
     * @return void
     */
    public function test_apply_overrides()
    {
        // Create test data
        $block = Block::factory()->create();
        $room = Room::factory()->create([
            'block_id' => $block->id,
            'capacity' => 10,
        ]);
        
        $course = Course::factory()->create();
        $student = Student::factory()->create(['course_id' => $course->id]);
        
        $seatingPlan = SeatingPlan::factory()->create(['room_id' => $room->id]);
        
        // Create a manual override
        SeatingOverride::factory()->create([
            'seating_plan_id' => $seatingPlan->id,
            'student_id' => $student->id,
            'room_id' => $room->id,
            'seat_number' => 5, // Specific seat number
            'reason' => 'Test override',
        ]);
        
        // Apply overrides using reflection to access protected method
        $method = new \ReflectionMethod(SeatingRuleService::class, 'applyOverrides');
        $method->setAccessible(true);
        
        $assignments = $method->invoke($this->seatingRuleService, $seatingPlan, []);
        
        // Check that the override was applied
        $this->assertArrayHasKey($room->id, $assignments);
        $this->assertArrayHasKey(5, $assignments[$room->id]);
        $this->assertEquals($student->id, $assignments[$room->id][5]);
    }

    /**
     * Test saving assignments.
     *
     * @return void
     */
    public function test_save_assignments()
    {
        // Create test data
        $block = Block::factory()->create();
        $room = Room::factory()->create([
            'block_id' => $block->id,
            'capacity' => 10,
        ]);
        
        $course = Course::factory()->create();
        $student = Student::factory()->create(['course_id' => $course->id]);
        
        $seatingPlan = SeatingPlan::factory()->create([
            'room_id' => $room->id,
            'status' => 'scheduled',
        ]);
        
        // Create assignments
        $assignments = [
            $room->id => [
                1 => $student->id,
            ]
        ];
        
        // Save assignments
        $result = $this->seatingRuleService->saveAssignments($seatingPlan, $assignments);
        
        // Check that the assignments were saved
        $this->assertTrue($result);
        $this->assertDatabaseHas('seating_assignments', [
            'seating_plan_id' => $seatingPlan->id,
            'student_id' => $student->id,
            'room_id' => $room->id,
            'seat_number' => 1,
            'is_override' => 0,
        ]);
        
        // Check that the seating plan status was updated
        $this->assertEquals('ready', $seatingPlan->fresh()->status);
    }

    /**
     * Test getting priority seats.
     *
     * @return void
     */
    public function test_get_priority_seats()
    {
        // Create test data
        $block = Block::factory()->create();
        $room = Room::factory()->create([
            'block_id' => $block->id,
            'capacity' => 20,
            'layout' => ['seats_per_row' => 5]
        ]);
        
        // Define parameters
        $parameters = [
            'seats_per_row' => 5,
            'door_seat' => 1,
            'accessible_seats' => [10, 15],
        ];
        
        // Get priority seats using reflection to access protected method
        $method = new \ReflectionMethod(SeatingRuleService::class, 'getPrioritySeats');
        $method->setAccessible(true);
        
        $prioritySeats = $method->invoke($this->seatingRuleService, $room, $parameters);
        
        // Check that priority seats include front row, aisle seats, door seat, and accessible seats
        $expectedSeats = [1, 2, 3, 4, 5, 6, 10, 11, 15, 16];
        sort($prioritySeats);
        
        $this->assertEquals($expectedSeats, $prioritySeats);
    }
}

