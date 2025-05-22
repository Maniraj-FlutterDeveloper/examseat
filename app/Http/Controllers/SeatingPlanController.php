<?php

namespace App\Http\Controllers;

use App\Models\SeatingPlan;
use App\Models\Block;
use App\Models\Room;
use App\Models\Course;
use App\Models\Student;
use App\Models\Invigilator;
use App\Models\SeatingRule;
use App\Models\SeatingAssignment;
use App\Models\InvigilatorAssignment;
use App\Models\SeatingPlanRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDF;

class SeatingPlanController extends Controller
{
    /**
     * Display a listing of the seating plans.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $seatingPlans = SeatingPlan::withCount(['seatingAssignments', 'invigilatorAssignments'])
            ->orderBy('exam_date', 'desc')
            ->get();
            
        return view('seat-plan.seating-plans.index', compact('seatingPlans'));
    }

    /**
     * Show the form for creating a new seating plan.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('seat-plan.seating-plans.create');
    }

    /**
     * Store a newly created seating plan in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'exam_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'status' => 'required|in:draft,published,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $seatingPlan = SeatingPlan::create([
            'title' => $request->title,
            'description' => $request->description,
            'exam_date' => $request->exam_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => $request->status,
            'created_by' => auth()->user()->name ?? 'System',
        ]);

        return redirect()->route('seating-plans.show', $seatingPlan)
            ->with('success', 'Seating plan created successfully.');
    }

    /**
     * Display the specified seating plan.
     *
     * @param  \App\Models\SeatingPlan  $seatingPlan
     * @return \Illuminate\Http\Response
     */
    public function show(SeatingPlan $seatingPlan)
    {
        $seatingPlan->load([
            'seatingAssignments.student', 
            'seatingAssignments.room.block',
            'invigilatorAssignments.invigilator',
            'invigilatorAssignments.room.block',
            'seatingPlanRules.seatingRule'
        ]);
        
        $roomsUsed = Room::whereHas('seatingAssignments', function($query) use ($seatingPlan) {
            $query->where('seating_plan_id', $seatingPlan->id);
        })->with('block')->get();
        
        $studentsByCourse = SeatingAssignment::where('seating_plan_id', $seatingPlan->id)
            ->join('students', 'seating_assignments.student_id', '=', 'students.id')
            ->join('courses', 'students.course_id', '=', 'courses.id')
            ->select('courses.name', DB::raw('count(*) as count'))
            ->groupBy('courses.name')
            ->pluck('count', 'name')
            ->toArray();
            
        return view('seat-plan.seating-plans.show', compact('seatingPlan', 'roomsUsed', 'studentsByCourse'));
    }

    /**
     * Show the form for editing the specified seating plan.
     *
     * @param  \App\Models\SeatingPlan  $seatingPlan
     * @return \Illuminate\Http\Response
     */
    public function edit(SeatingPlan $seatingPlan)
    {
        if (!$seatingPlan->isEditable()) {
            return redirect()->route('seating-plans.show', $seatingPlan)
                ->with('error', 'This seating plan cannot be edited.');
        }
        
        return view('seat-plan.seating-plans.edit', compact('seatingPlan'));
    }

    /**
     * Update the specified seating plan in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SeatingPlan  $seatingPlan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SeatingPlan $seatingPlan)
    {
        if (!$seatingPlan->isEditable()) {
            return redirect()->route('seating-plans.show', $seatingPlan)
                ->with('error', 'This seating plan cannot be edited.');
        }
        
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'exam_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'status' => 'required|in:draft,published,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $seatingPlan->update($request->all());

        return redirect()->route('seating-plans.show', $seatingPlan)
            ->with('success', 'Seating plan updated successfully.');
    }

    /**
     * Remove the specified seating plan from storage.
     *
     * @param  \App\Models\SeatingPlan  $seatingPlan
     * @return \Illuminate\Http\Response
     */
    public function destroy(SeatingPlan $seatingPlan)
    {
        if ($seatingPlan->isCompleted()) {
            return redirect()->route('seating-plans.index')
                ->with('error', 'Cannot delete a completed seating plan.');
        }

        // Delete all related records
        DB::beginTransaction();
        
        try {
            // Delete seating assignments
            $seatingPlan->seatingAssignments()->delete();
            
            // Delete invigilator assignments
            $seatingPlan->invigilatorAssignments()->delete();
            
            // Delete seating plan rules
            $seatingPlan->seatingPlanRules()->delete();
            
            // Delete the seating plan
            $seatingPlan->delete();
            
            DB::commit();
            
            return redirect()->route('seating-plans.index')
                ->with('success', 'Seating plan deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting seating plan: ' . $e->getMessage());
            
            return redirect()->route('seating-plans.index')
                ->with('error', 'Error deleting seating plan: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for configuring the seating plan.
     *
     * @param  \App\Models\SeatingPlan  $seatingPlan
     * @return \Illuminate\Http\Response
     */
    public function configure(SeatingPlan $seatingPlan)
    {
        if (!$seatingPlan->isEditable()) {
            return redirect()->route('seating-plans.show', $seatingPlan)
                ->with('error', 'This seating plan cannot be configured.');
        }
        
        $blocks = Block::with('rooms')->active()->get();
        $courses = Course::with('students')->active()->get();
        $seatingRules = SeatingRule::active()->get();
        
        $seatingPlan->load('seatingPlanRules.seatingRule');
        
        return view('seat-plan.seating-plans.configure', compact('seatingPlan', 'blocks', 'courses', 'seatingRules'));
    }

    /**
     * Save the seating plan configuration.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SeatingPlan  $seatingPlan
     * @return \Illuminate\Http\Response
     */
    public function saveConfiguration(Request $request, SeatingPlan $seatingPlan)
    {
        if (!$seatingPlan->isEditable()) {
            return redirect()->route('seating-plans.show', $seatingPlan)
                ->with('error', 'This seating plan cannot be configured.');
        }
        
        $validator = Validator::make($request->all(), [
            'room_ids' => 'required|array',
            'room_ids.*' => 'exists:rooms,id',
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id',
            'years' => 'nullable|array',
            'sections' => 'nullable|array',
            'rule_ids' => 'nullable|array',
            'rule_ids.*' => 'exists:seating_rules,id',
            'rule_parameters' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        
        try {
            // Clear existing rules
            $seatingPlan->seatingPlanRules()->delete();
            
            // Save new rules
            if ($request->has('rule_ids')) {
                foreach ($request->rule_ids as $index => $ruleId) {
                    $parameters = $request->rule_parameters[$index] ?? null;
                    
                    if (is_string($parameters)) {
                        $parameters = json_decode($parameters, true);
                    }
                    
                    SeatingPlanRule::create([
                        'seating_plan_id' => $seatingPlan->id,
                        'seating_rule_id' => $ruleId,
                        'parameters' => $parameters,
                        'priority' => $index + 1,
                    ]);
                }
            }
            
            // Store configuration in session for the allocation step
            session([
                'seating_plan_config' => [
                    'seating_plan_id' => $seatingPlan->id,
                    'room_ids' => $request->room_ids,
                    'course_ids' => $request->course_ids,
                    'years' => $request->years ?? [],
                    'sections' => $request->sections ?? [],
                ]
            ]);
            
            DB::commit();
            
            return redirect()->route('seating-plans.allocate', $seatingPlan)
                ->with('success', 'Seating plan configuration saved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving seating plan configuration: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error saving configuration: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for allocating seats.
     *
     * @param  \App\Models\SeatingPlan  $seatingPlan
     * @return \Illuminate\Http\Response
     */
    public function allocate(SeatingPlan $seatingPlan)
    {
        if (!$seatingPlan->isEditable()) {
            return redirect()->route('seating-plans.show', $seatingPlan)
                ->with('error', 'This seating plan cannot be modified.');
        }
        
        // Get configuration from session
        $config = session('seating_plan_config');
        
        if (!$config || $config['seating_plan_id'] != $seatingPlan->id) {
            return redirect()->route('seating-plans.configure', $seatingPlan)
                ->with('error', 'Please configure the seating plan first.');
        }
        
        $rooms = Room::whereIn('id', $config['room_ids'])->with('block')->get();
        
        $studentQuery = Student::whereIn('course_id', $config['course_ids'])->active();
        
        if (!empty($config['years'])) {
            $studentQuery->whereIn('year', $config['years']);
        }
        
        if (!empty($config['sections'])) {
            $studentQuery->whereIn('section', $config['sections']);
        }
        
        $students = $studentQuery->with('course')->get();
        
        $seatingPlan->load('seatingPlanRules.seatingRule');
        
        return view('seat-plan.seating-plans.allocate', compact('seatingPlan', 'rooms', 'students'));
    }

    /**
     * Process the seat allocation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SeatingPlan  $seatingPlan
     * @return \Illuminate\Http\Response
     */
    public function processAllocation(Request $request, SeatingPlan $seatingPlan)
    {
        if (!$seatingPlan->isEditable()) {
            return redirect()->route('seating-plans.show', $seatingPlan)
                ->with('error', 'This seating plan cannot be modified.');
        }
        
        $validator = Validator::make($request->all(), [
            'allocation_method' => 'required|in:automatic,manual',
            'student_ids' => 'required_if:allocation_method,manual|array',
            'room_ids' => 'required_if:allocation_method,manual|array',
            'seat_numbers' => 'required_if:allocation_method,manual|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        
        try {
            // Clear existing assignments
            $seatingPlan->seatingAssignments()->delete();
            
            if ($request->allocation_method === 'automatic') {
                // Implement the automatic allocation logic here
                // This is a placeholder for the actual allocation algorithm
                $this->automaticAllocation($seatingPlan);
            } else {
                // Manual allocation
                foreach ($request->student_ids as $index => $studentId) {
                    SeatingAssignment::create([
                        'seating_plan_id' => $seatingPlan->id,
                        'room_id' => $request->room_ids[$index],
                        'student_id' => $studentId,
                        'seat_number' => $request->seat_numbers[$index],
                        'row_number' => $request->row_numbers[$index] ?? null,
                        'column_number' => $request->column_numbers[$index] ?? null,
                    ]);
                }
            }
            
            DB::commit();
            
            return redirect()->route('seating-plans.assign-invigilators', $seatingPlan)
                ->with('success', 'Seats allocated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error allocating seats: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error allocating seats: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Automatic seat allocation algorithm.
     *
     * @param  \App\Models\SeatingPlan  $seatingPlan
     * @return void
     */
    private function automaticAllocation(SeatingPlan $seatingPlan)
    {
        // Get configuration from session
        $config = session('seating_plan_config');
        
        if (!$config || $config['seating_plan_id'] != $seatingPlan->id) {
            throw new \Exception('Seating plan configuration not found.');
        }
        
        $rooms = Room::whereIn('id', $config['room_ids'])->with('block')->get();
        
        $studentQuery = Student::whereIn('course_id', $config['course_ids'])->active();
        
        if (!empty($config['years'])) {
            $studentQuery->whereIn('year', $config['years']);
        }
        
        if (!empty($config['sections'])) {
            $studentQuery->whereIn('section', $config['sections']);
        }
        
        $students = $studentQuery->with('course')->get();
        
        // Get seating rules
        $seatingPlan->load('seatingPlanRules.seatingRule');
        $rules = $seatingPlan->seatingPlanRules()->with('seatingRule')->orderByPriority()->get();
        
        // Apply rules to determine the seating arrangement
        // This is a simplified implementation
        
        // First, handle special needs students if that rule exists
        $specialNeedsRule = $rules->first(function ($rule) {
            return $rule->seatingRule->isSpecialNeeds();
        });
        
        if ($specialNeedsRule) {
            $specialNeedsStudents = $students->where('has_disability', true);
            
            // Assign special needs students to appropriate rooms
            // Implementation depends on the specific requirements
        }
        
        // Next, apply other rules
        // For simplicity, we'll just distribute students across rooms
        $totalCapacity = $rooms->sum('capacity');
        
        if ($students->count() > $totalCapacity) {
            throw new \Exception('Not enough seats for all students.');
        }
        
        $seatNumber = 1;
        $currentRoomIndex = 0;
        $currentRoom = $rooms[$currentRoomIndex];
        $roomCapacity = $currentRoom->capacity;
        $roomSeatCount = 0;
        
        // Shuffle students if mixed branches rule exists
        $mixedBranchesRule = $rules->first(function ($rule) {
            return $rule->seatingRule->isMixedBranches();
        });
        
        if ($mixedBranchesRule) {
            $students = $students->shuffle();
        }
        
        // Assign seats
        foreach ($students as $student) {
            // If current room is full, move to next room
            if ($roomSeatCount >= $roomCapacity) {
                $currentRoomIndex++;
                
                if ($currentRoomIndex >= $rooms->count()) {
                    throw new \Exception('Not enough seats for all students.');
                }
                
                $currentRoom = $rooms[$currentRoomIndex];
                $roomCapacity = $currentRoom->capacity;
                $roomSeatCount = 0;
                $seatNumber = 1;
            }
            
            // Calculate row and column if room has grid layout
            $rowNumber = null;
            $columnNumber = null;
            
            if ($currentRoom->hasGridLayout()) {
                $columnNumber = (($seatNumber - 1) % $currentRoom->columns) + 1;
                $rowNumber = floor(($seatNumber - 1) / $currentRoom->columns) + 1;
            }
            
            // Create seating assignment
            SeatingAssignment::create([
                'seating_plan_id' => $seatingPlan->id,
                'room_id' => $currentRoom->id,
                'student_id' => $student->id,
                'seat_number' => $seatNumber,
                'row_number' => $rowNumber,
                'column_number' => $columnNumber,
            ]);
            
            $seatNumber++;
            $roomSeatCount++;
        }
    }

    /**
     * Show the form for assigning invigilators.
     *
     * @param  \App\Models\SeatingPlan  $seatingPlan
     * @return \Illuminate\Http\Response
     */
    public function assignInvigilators(SeatingPlan $seatingPlan)
    {
        if (!$seatingPlan->isEditable()) {
            return redirect()->route('seating-plans.show', $seatingPlan)
                ->with('error', 'This seating plan cannot be modified.');
        }
        
        $rooms = Room::whereHas('seatingAssignments', function($query) use ($seatingPlan) {
            $query->where('seating_plan_id', $seatingPlan->id);
        })->with(['block', 'seatingAssignments' => function($query) use ($seatingPlan) {
            $query->where('seating_plan_id', $seatingPlan->id);
        }])->get();
        
        $invigilators = Invigilator::active()->get();
        
        $seatingPlan->load('invigilatorAssignments.invigilator', 'invigilatorAssignments.room');
        
        return view('seat-plan.seating-plans.assign-invigilators', compact('seatingPlan', 'rooms', 'invigilators'));
    }

    /**
     * Save invigilator assignments.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SeatingPlan  $seatingPlan
     * @return \Illuminate\Http\Response
     */
    public function saveInvigilatorAssignments(Request $request, SeatingPlan $seatingPlan)
    {
        if (!$seatingPlan->isEditable()) {
            return redirect()->route('seating-plans.show', $seatingPlan)
                ->with('error', 'This seating plan cannot be modified.');
        }
        
        $validator = Validator::make($request->all(), [
            'invigilator_ids' => 'required|array',
            'invigilator_ids.*' => 'exists:invigilators,id',
            'room_ids' => 'required|array',
            'room_ids.*' => 'exists:rooms,id',
            'roles' => 'required|array',
            'roles.*' => 'in:primary,assistant,relief',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        
        try {
            // Clear existing assignments
            $seatingPlan->invigilatorAssignments()->delete();
            
            // Save new assignments
            foreach ($request->invigilator_ids as $index => $invigilatorId) {
                InvigilatorAssignment::create([
                    'seating_plan_id' => $seatingPlan->id,
                    'room_id' => $request->room_ids[$index],
                    'invigilator_id' => $invigilatorId,
                    'role' => $request->roles[$index],
                    'notes' => $request->notes[$index] ?? null,
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('seating-plans.show', $seatingPlan)
                ->with('success', 'Invigilators assigned successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error assigning invigilators: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error assigning invigilators: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Generate PDF of the seating plan.
     *
     * @param  \App\Models\SeatingPlan  $seatingPlan
     * @return \Illuminate\Http\Response
     */
    public function generatePdf(SeatingPlan $seatingPlan)
    {
        $seatingPlan->load([
            'seatingAssignments.student.course', 
            'seatingAssignments.room.block',
            'invigilatorAssignments.invigilator',
            'invigilatorAssignments.room.block'
        ]);
        
        $roomsUsed = Room::whereHas('seatingAssignments', function($query) use ($seatingPlan) {
            $query->where('seating_plan_id', $seatingPlan->id);
        })->with(['block', 'seatingAssignments' => function($query) use ($seatingPlan) {
            $query->where('seating_plan_id', $seatingPlan->id)
                ->with('student.course');
        }])->get();
        
        // Generate PDF using a package like barryvdh/laravel-dompdf
        // This is a placeholder for the actual PDF generation
        
        return view('seat-plan.seating-plans.pdf', compact('seatingPlan', 'roomsUsed'));
    }

    /**
     * Export the seating plan to Excel.
     *
     * @param  \App\Models\SeatingPlan  $seatingPlan
     * @return \Illuminate\Http\Response
     */
    public function exportExcel(SeatingPlan $seatingPlan)
    {
        $seatingPlan->load([
            'seatingAssignments.student.course', 
            'seatingAssignments.room.block',
            'invigilatorAssignments.invigilator',
            'invigilatorAssignments.room.block'
        ]);
        
        // Export to Excel using a package like maatwebsite/excel
        // This is a placeholder for the actual Excel export
        
        return redirect()->route('seating-plans.show', $seatingPlan)
            ->with('error', 'Excel export functionality is not implemented yet.');
    }

    /**
     * Display the seating chart for a specific room.
     *
     * @param  \App\Models\SeatingPlan  $seatingPlan
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function roomChart(SeatingPlan $seatingPlan, Room $room)
    {
        $seatingPlan->load(['seatingAssignments' => function($query) use ($room) {
            $query->where('room_id', $room->id)
                ->with('student.course');
        }]);
        
        $room->load('block');
        
        $assignments = $seatingPlan->seatingAssignments->where('room_id', $room->id);
        
        return view('seat-plan.seating-plans.room-chart', compact('seatingPlan', 'room', 'assignments'));
    }

    /**
     * Display the student list for a specific room.
     *
     * @param  \App\Models\SeatingPlan  $seatingPlan
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function roomStudentList(SeatingPlan $seatingPlan, Room $room)
    {
        $seatingPlan->load(['seatingAssignments' => function($query) use ($room) {
            $query->where('room_id', $room->id)
                ->with('student.course');
        }]);
        
        $room->load('block');
        
        $assignments = $seatingPlan->seatingAssignments->where('room_id', $room->id)
            ->sortBy('seat_number');
        
        return view('seat-plan.seating-plans.room-student-list', compact('seatingPlan', 'room', 'assignments'));
    }

    /**
     * Display the student card for a specific student.
     *
     * @param  \App\Models\SeatingPlan  $seatingPlan
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function studentCard(SeatingPlan $seatingPlan, Student $student)
    {
        $assignment = SeatingAssignment::where('seating_plan_id', $seatingPlan->id)
            ->where('student_id', $student->id)
            ->with(['room.block', 'student.course'])
            ->first();
            
        if (!$assignment) {
            return redirect()->route('seating-plans.show', $seatingPlan)
                ->with('error', 'Student not assigned to this seating plan.');
        }
        
        return view('seat-plan.seating-plans.student-card', compact('seatingPlan', 'assignment'));
    }

    /**
     * Display the invigilator card for a specific invigilator.
     *
     * @param  \App\Models\SeatingPlan  $seatingPlan
     * @param  \App\Models\Invigilator  $invigilator
     * @return \Illuminate\Http\Response
     */
    public function invigilatorCard(SeatingPlan $seatingPlan, Invigilator $invigilator)
    {
        $assignment = InvigilatorAssignment::where('seating_plan_id', $seatingPlan->id)
            ->where('invigilator_id', $invigilator->id)
            ->with(['room.block', 'invigilator'])
            ->first();
            
        if (!$assignment) {
            return redirect()->route('seating-plans.show', $seatingPlan)
                ->with('error', 'Invigilator not assigned to this seating plan.');
        }
        
        $studentCount = SeatingAssignment::where('seating_plan_id', $seatingPlan->id)
            ->where('room_id', $assignment->room_id)
            ->count();
        
        return view('seat-plan.seating-plans.invigilator-card', compact('seatingPlan', 'assignment', 'studentCount'));
    }
}

