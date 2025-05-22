<?php

namespace Tests\Feature;

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
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SeatingPlanTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test creating a seating plan.
     *
     * @return void
     */
    public function test_can_create_seating_plan()
    {
        $block = Block::factory()->create();
        $room = Room::factory()->create(['block_id' => $block->id]);

        $response = $this->post(route('seating.plans.store'), [
            'room_id' => $room->id,
            'exam_name' => 'Midterm Exam',
            'exam_date' => '2023-12-15',
            'start_time' => '09:00',
            'end_time' => '11:00',
        ]);

        $response->assertRedirect(route('seating.plans.index'));
        $this->assertDatabaseHas('seating_plans', [
            'room_id' => $room->id,
            'exam_name' => 'Midterm Exam',
            'status' => 'scheduled',
        ]);
    }

    /**
     * Test updating a seating plan.
     *
     * @return void
     */
    public function test_can_update_seating_plan()
    {
        $block = Block::factory()->create();
        $room = Room::factory()->create(['block_id' => $block->id]);
        $seatingPlan = SeatingPlan::factory()->create(['room_id' => $room->id]);

        $response = $this->put(route('seating.plans.update', $seatingPlan), [
            'room_id' => $room->id,
            'exam_name' => 'Updated Exam Name',
            'exam_date' => '2023-12-20',
            'start_time' => '10:00',
            'end_time' => '12:00',
            'status' => 'ongoing',
        ]);

        $response->assertRedirect(route('seating.plans.index'));
        $this->assertDatabaseHas('seating_plans', [
            'id' => $seatingPlan->id,
            'exam_name' => 'Updated Exam Name',
            'status' => 'ongoing',
        ]);
    }

    /**
     * Test deleting a seating plan.
     *
     * @return void
     */
    public function test_can_delete_seating_plan()
    {
        $block = Block::factory()->create();
        $room = Room::factory()->create(['block_id' => $block->id]);
        $seatingPlan = SeatingPlan::factory()->create(['room_id' => $room->id]);

        $response = $this->delete(route('seating.plans.destroy', $seatingPlan));

        $response->assertRedirect(route('seating.plans.index'));
        $this->assertSoftDeleted($seatingPlan);
    }

    /**
     * Test generating seating assignments.
     *
     * @return void
     */
    public function test_can_generate_seating_assignments()
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
        
        // Create a seating rule
        SeatingRule::factory()->create([
            'type' => 'alternate_courses',
            'parameters' => ['min_distance' => 1],
            'is_active' => true,
        ]);

        // Generate assignments
        $response = $this->get(route('seating.plans.generate', $seatingPlan));

        $response->assertStatus(200);
        $response->assertViewIs('seating.plans.assignments');
        $response->assertViewHas('seatingPlan');
        $response->assertViewHas('assignments');
        
        // Check that the session contains the assignments
        $this->assertNotNull(session('seating_assignments'));
    }

    /**
     * Test saving seating assignments.
     *
     * @return void
     */
    public function test_can_save_seating_assignments()
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
        
        // Create mock assignments
        $assignments = [
            $room->id => [
                1 => $student->id,
            ]
        ];
        
        // Store assignments in session
        session(['seating_assignments' => $assignments]);
        
        // Save assignments
        $response = $this->post(route('seating.plans.save', $seatingPlan));

        $response->assertRedirect(route('seating.plans.show', $seatingPlan));
        
        // Check that the assignments were saved to the database
        $this->assertDatabaseHas('seating_assignments', [
            'seating_plan_id' => $seatingPlan->id,
            'student_id' => $student->id,
            'room_id' => $room->id,
            'seat_number' => 1,
        ]);
        
        // Check that the session was cleared
        $this->assertNull(session('seating_assignments'));
    }

    /**
     * Test applying seating rules.
     *
     * @return void
     */
    public function test_seating_rule_service_applies_rules()
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
        
        // Create seating rules
        SeatingRule::factory()->create([
            'type' => 'alternate_courses',
            'parameters' => ['min_distance' => 1],
            'priority' => 2,
            'is_active' => true,
        ]);
        
        SeatingRule::factory()->create([
            'type' => 'distance',
            'parameters' => ['distance' => 1],
            'priority' => 1,
            'is_active' => true,
        ]);
        
        // Apply rules
        $seatingRuleService = new SeatingRuleService();
        $assignments = $seatingRuleService->applyRules(
            $seatingPlan,
            $allStudents,
            collect([$room])
        );
        
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
     * Test priority seating for students with disabilities.
     *
     * @return void
     */
    public function test_priority_seating_for_students_with_disabilities()
    {
        // Create test data
        $block = Block::factory()->create();
        $room = Room::factory()->create([
            'block_id' => $block->id,
            'capacity' => 10,
            'layout' => ['seats_per_row' => 5]
        ]);
        
        $course = Course::factory()->create();
        
        // Create a student with disability
        $studentWithDisability = Student::factory()->create([
            'course_id' => $course->id,
            'has_disability' => true,
        ]);
        
        // Create regular students
        $regularStudents = Student::factory()->count(5)->create([
            'course_id' => $course->id,
            'has_disability' => false,
        ]);
        
        $allStudents = collect([$studentWithDisability])->merge($regularStudents);
        
        $seatingPlan = SeatingPlan::factory()->create(['room_id' => $room->id]);
        
        // Create priority rule
        SeatingRule::factory()->create([
            'type' => 'priority',
            'parameters' => [
                'seats_per_row' => 5,
                'door_seat' => 1,
            ],
            'priority' => 3, // Highest priority
            'is_active' => true,
        ]);
        
        // Apply rules
        $seatingRuleService = new SeatingRuleService();
        $assignments = $seatingRuleService->applyRules(
            $seatingPlan,
            $allStudents,
            collect([$room])
        );
        
        // Check that the student with disability is assigned to a priority seat (front row or near door)
        $prioritySeats = [1, 2, 3, 4, 5]; // Front row seats
        
        $studentWithDisabilitySeat = null;
        foreach ($assignments[$room->id] as $seatNumber => $studentId) {
            if ($studentId === $studentWithDisability->id) {
                $studentWithDisabilitySeat = $seatNumber;
                break;
            }
        }
        
        $this->assertNotNull($studentWithDisabilitySeat, 'Student with disability should be assigned a seat');
        $this->assertContains($studentWithDisabilitySeat, $prioritySeats, 'Student with disability should be assigned a priority seat');
    }

    /**
     * Test applying manual overrides.
     *
     * @return void
     */
    public function test_manual_overrides_are_applied()
    {
        // Create test data
        $block = Block::factory()->create();
        $room = Room::factory()->create([
            'block_id' => $block->id,
            'capacity' => 10,
        ]);
        
        $course = Course::factory()->create();
        $student1 = Student::factory()->create(['course_id' => $course->id]);
        $student2 = Student::factory()->create(['course_id' => $course->id]);
        
        $seatingPlan = SeatingPlan::factory()->create(['room_id' => $room->id]);
        
        // Create a manual override
        SeatingOverride::factory()->create([
            'seating_plan_id' => $seatingPlan->id,
            'student_id' => $student1->id,
            'room_id' => $room->id,
            'seat_number' => 5, // Specific seat number
            'reason' => 'Test override',
        ]);
        
        // Apply rules
        $seatingRuleService = new SeatingRuleService();
        $assignments = $seatingRuleService->applyRules(
            $seatingPlan,
            collect([$student1, $student2]),
            collect([$room])
        );
        
        // Check that the override was applied
        $this->assertArrayHasKey($room->id, $assignments);
        $this->assertArrayHasKey(5, $assignments[$room->id]);
        $this->assertEquals($student1->id, $assignments[$room->id][5]);
    }
}

