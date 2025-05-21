<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeatingPlan;
use App\Models\Room;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use PDF;

class SeatingPlanController extends Controller
{
    /**
     * Display a listing of the seating plans.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $seatingPlans = SeatingPlan::select('exam_name', 'exam_date', DB::raw('COUNT(*) as total_students'))
            ->groupBy('exam_name', 'exam_date')
            ->orderBy('exam_date', 'desc')
            ->paginate(10);
            
        return view('admin.seating_plans.index', compact('seatingPlans'));
    }

    /**
     * Show the form for creating a new seating plan.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $rooms = Room::with('block')->get();
        $courses = Course::orderBy('course_name')->get();
        return view('admin.seating_plans.create', compact('rooms', 'courses'));
    }

    /**
     * Store a newly created seating plan in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'exam_name' => 'required|string|max:100',
            'exam_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room_ids' => 'required|array',
            'room_ids.*' => 'exists:rooms,id',
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id',
            'years' => 'required|array',
            'years.*' => 'integer|min:1',
            'sections' => 'nullable|array',
            'allocation_strategy' => 'required|in:random,sequential,alternate_course,mixed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Get selected rooms with their capacities
        $rooms = Room::whereIn('id', $request->room_ids)->get();
        $totalCapacity = $rooms->sum('capacity');
        
        // Get students based on selected courses, years, and sections
        $studentsQuery = Student::whereIn('course_id', $request->course_ids)
            ->whereIn('year', $request->years);
            
        if (!empty($request->sections)) {
            $studentsQuery->whereIn('section', $request->sections);
        }
        
        $students = $studentsQuery->get();
        
        // Check if we have enough capacity
        if ($students->count() > $totalCapacity) {
            return redirect()->back()
                ->with('error', "Not enough capacity. Selected rooms can accommodate {$totalCapacity} students, but {$students->count()} students need to be seated.")
                ->withInput();
        }
        
        // Begin transaction
        DB::beginTransaction();
        
        try {
            // Allocate seats based on selected strategy
            switch ($request->allocation_strategy) {
                case 'random':
                    $this->allocateRandomly($students, $rooms, $request);
                    break;
                    
                case 'sequential':
                    $this->allocateSequentially($students, $rooms, $request);
                    break;
                    
                case 'alternate_course':
                    $this->allocateAlternateCourse($students, $rooms, $request);
                    break;
                    
                case 'mixed':
                    $this->allocateMixed($students, $rooms, $request);
                    break;
            }
            
            DB::commit();
            
            return redirect()->route('admin.seating_plans.show', ['exam_name' => $request->exam_name, 'exam_date' => $request->exam_date])
                ->with('success', 'Seating plan created successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'An error occurred: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified seating plan.
     *
     * @param  string  $exam_name
     * @param  string  $exam_date
     * @return \Illuminate\View\View
     */
    public function show($exam_name, $exam_date)
    {
        $seatingPlan = SeatingPlan::where('exam_name', $exam_name)
            ->where('exam_date', $exam_date)
            ->first();
            
        if (!$seatingPlan) {
            return redirect()->route('admin.seating_plans.index')
                ->with('error', 'Seating plan not found.');
        }
        
        $roomAllocations = SeatingPlan::where('exam_name', $exam_name)
            ->where('exam_date', $exam_date)
            ->select('room_id', DB::raw('COUNT(*) as student_count'))
            ->groupBy('room_id')
            ->with('room.block')
            ->get();
            
        $courseAllocations = SeatingPlan::where('exam_name', $exam_name)
            ->where('exam_date', $exam_date)
            ->join('students', 'seating_plans.student_id', '=', 'students.id')
            ->join('courses', 'students.course_id', '=', 'courses.id')
            ->select('courses.course_name', DB::raw('COUNT(*) as student_count'))
            ->groupBy('courses.course_name')
            ->get();
            
        $students = SeatingPlan::where('exam_name', $exam_name)
            ->where('exam_date', $exam_date)
            ->with(['student.course', 'room.block'])
            ->orderBy('room_id')
            ->orderBy('seat_number')
            ->paginate(20);
            
        return view('admin.seating_plans.show', compact(
            'seatingPlan', 
            'roomAllocations', 
            'courseAllocations', 
            'students',
            'exam_name',
            'exam_date'
        ));
    }

    /**
     * Show the room layout for a specific room in the seating plan.
     *
     * @param  string  $exam_name
     * @param  string  $exam_date
     * @param  int  $room_id
     * @return \Illuminate\View\View
     */
    public function showRoom($exam_name, $exam_date, $room_id)
    {
        $room = Room::findOrFail($room_id);
        
        $seatingPlan = SeatingPlan::where('exam_name', $exam_name)
            ->where('exam_date', $exam_date)
            ->where('room_id', $room_id)
            ->with('student.course')
            ->get();
            
        if ($seatingPlan->isEmpty()) {
            return redirect()->route('admin.seating_plans.show', ['exam_name' => $exam_name, 'exam_date' => $exam_date])
                ->with('error', 'No seating plan found for this room.');
        }
        
        // Create a 2D array representing the room layout
        $layout = [];
        for ($row = 1; $row <= $room->rows; $row++) {
            $layout[$row] = [];
            for ($col = 1; $col <= $room->columns; $col++) {
                $layout[$row][$col] = null;
            }
        }
        
        // Fill the layout with students
        foreach ($seatingPlan as $seat) {
            $rowCol = $this->getSeatRowCol($seat->seat_number, $room->columns);
            $layout[$rowCol['row']][$rowCol['col']] = $seat;
        }
        
        return view('admin.seating_plans.room_layout', compact(
            'room', 
            'layout', 
            'exam_name', 
            'exam_date'
        ));
    }

    /**
     * Generate PDF of the seating plan.
     *
     * @param  string  $exam_name
     * @param  string  $exam_date
     * @return \Illuminate\Http\Response
     */
    public function generatePdf($exam_name, $exam_date)
    {
        $seatingPlan = SeatingPlan::where('exam_name', $exam_name)
            ->where('exam_date', $exam_date)
            ->first();
            
        if (!$seatingPlan) {
            return redirect()->route('admin.seating_plans.index')
                ->with('error', 'Seating plan not found.');
        }
        
        $roomAllocations = SeatingPlan::where('exam_name', $exam_name)
            ->where('exam_date', $exam_date)
            ->select('room_id', DB::raw('COUNT(*) as student_count'))
            ->groupBy('room_id')
            ->with('room.block')
            ->get();
            
        $students = SeatingPlan::where('exam_name', $exam_name)
            ->where('exam_date', $exam_date)
            ->with(['student.course', 'room.block'])
            ->orderBy('room_id')
            ->orderBy('seat_number')
            ->get();
            
        $pdf = PDF::loadView('admin.seating_plans.pdf', compact(
            'seatingPlan', 
            'roomAllocations', 
            'students',
            'exam_name',
            'exam_date'
        ));
        
        return $pdf->download("seating_plan_{$exam_name}_{$exam_date}.pdf");
    }

    /**
     * Delete the specified seating plan.
     *
     * @param  string  $exam_name
     * @param  string  $exam_date
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($exam_name, $exam_date)
    {
        $deleted = SeatingPlan::where('exam_name', $exam_name)
            ->where('exam_date', $exam_date)
            ->delete();
            
        if ($deleted) {
            return redirect()->route('admin.seating_plans.index')
                ->with('success', 'Seating plan deleted successfully.');
        } else {
            return redirect()->route('admin.seating_plans.index')
                ->with('error', 'Seating plan not found.');
        }
    }

    /**
     * Allocate students randomly to rooms.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $students
     * @param  \Illuminate\Database\Eloquent\Collection  $rooms
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    private function allocateRandomly($students, $rooms, $request)
    {
        // Shuffle students
        $students = $students->shuffle();
        
        // Prepare room data with capacity
        $roomData = [];
        foreach ($rooms as $room) {
            $roomData[$room->id] = [
                'room' => $room,
                'capacity' => $room->capacity,
                'allocated' => 0
            ];
        }
        
        // Allocate students to rooms
        foreach ($students as $student) {
            // Find a room with available capacity
            foreach ($roomData as &$data) {
                if ($data['allocated'] < $data['capacity']) {
                    // Allocate student to this room
                    $seatNumber = $data['allocated'] + 1;
                    
                    SeatingPlan::create([
                        'exam_name' => $request->exam_name,
                        'exam_date' => $request->exam_date,
                        'start_time' => $request->start_time,
                        'end_time' => $request->end_time,
                        'room_id' => $data['room']->id,
                        'student_id' => $student->id,
                        'seat_number' => $seatNumber
                    ]);
                    
                    $data['allocated']++;
                    break;
                }
            }
        }
    }

    /**
     * Allocate students sequentially to rooms.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $students
     * @param  \Illuminate\Database\Eloquent\Collection  $rooms
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    private function allocateSequentially($students, $rooms, $request)
    {
        // Sort students by roll number
        $students = $students->sortBy('roll_number');
        
        // Prepare room data with capacity
        $roomData = [];
        foreach ($rooms as $room) {
            $roomData[$room->id] = [
                'room' => $room,
                'capacity' => $room->capacity,
                'allocated' => 0
            ];
        }
        
        // Allocate students to rooms
        foreach ($students as $student) {
            // Find a room with available capacity
            foreach ($roomData as &$data) {
                if ($data['allocated'] < $data['capacity']) {
                    // Allocate student to this room
                    $seatNumber = $data['allocated'] + 1;
                    
                    SeatingPlan::create([
                        'exam_name' => $request->exam_name,
                        'exam_date' => $request->exam_date,
                        'start_time' => $request->start_time,
                        'end_time' => $request->end_time,
                        'room_id' => $data['room']->id,
                        'student_id' => $student->id,
                        'seat_number' => $seatNumber
                    ]);
                    
                    $data['allocated']++;
                    break;
                }
            }
        }
    }

    /**
     * Allocate students with alternating courses.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $students
     * @param  \Illuminate\Database\Eloquent\Collection  $rooms
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    private function allocateAlternateCourse($students, $rooms, $request)
    {
        // Group students by course
        $courseGroups = $students->groupBy('course_id');
        
        // Prepare room data with capacity
        $roomData = [];
        foreach ($rooms as $room) {
            $roomData[$room->id] = [
                'room' => $room,
                'capacity' => $room->capacity,
                'allocated' => 0,
                'layout' => []
            ];
            
            // Initialize layout
            for ($row = 1; $row <= $room->rows; $row++) {
                for ($col = 1; $col <= $room->columns; $col++) {
                    if (($row - 1) * $room->columns + $col <= $room->capacity) {
                        $roomData[$room->id]['layout'][$row][$col] = null;
                    }
                }
            }
        }
        
        // Prepare course queues
        $courseQueues = [];
        foreach ($courseGroups as $courseId => $students) {
            $courseQueues[$courseId] = $students->values();
        }
        
        // Allocate students to rooms
        $currentRoom = null;
        $currentRoomId = null;
        $row = 1;
        $col = 1;
        $courseIds = array_keys($courseQueues);
        $courseIndex = 0;
        
        while (array_sum(array_map(function($queue) { return $queue->count(); }, $courseQueues)) > 0) {
            // Get next course with students
            $originalCourseIndex = $courseIndex;
            $courseId = null;
            
            do {
                $courseId = $courseIds[$courseIndex];
                $courseIndex = ($courseIndex + 1) % count($courseIds);
                
                if ($courseQueues[$courseId]->count() > 0) {
                    break;
                }
            } while ($courseIndex !== $originalCourseIndex);
            
            // If no course has students left, break
            if ($courseQueues[$courseId]->count() === 0) {
                break;
            }
            
            // Get student from course queue
            $student = $courseQueues[$courseId]->shift();
            
            // Find a room with available capacity
            if ($currentRoom === null || $roomData[$currentRoomId]['allocated'] >= $roomData[$currentRoomId]['capacity']) {
                foreach ($roomData as $roomId => $data) {
                    if ($data['allocated'] < $data['capacity']) {
                        $currentRoomId = $roomId;
                        $currentRoom = $data['room'];
                        $row = 1;
                        $col = 1;
                        break;
                    }
                }
            }
            
            // Find next available seat
            while (isset($roomData[$currentRoomId]['layout'][$row][$col]) && 
                   $roomData[$currentRoomId]['layout'][$row][$col] !== null) {
                $col++;
                if ($col > $currentRoom->columns) {
                    $col = 1;
                    $row++;
                }
                
                // If we've gone beyond the room's rows, move to next room
                if ($row > $currentRoom->rows) {
                    foreach ($roomData as $roomId => $data) {
                        if ($data['allocated'] < $data['capacity']) {
                            $currentRoomId = $roomId;
                            $currentRoom = $data['room'];
                            $row = 1;
                            $col = 1;
                            break;
                        }
                    }
                    
                    // If no room has capacity, break
                    if ($row > $currentRoom->rows) {
                        break;
                    }
                }
            }
            
            // Calculate seat number
            $seatNumber = ($row - 1) * $currentRoom->columns + $col;
            
            // Allocate student to this seat
            SeatingPlan::create([
                'exam_name' => $request->exam_name,
                'exam_date' => $request->exam_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'room_id' => $currentRoomId,
                'student_id' => $student->id,
                'seat_number' => $seatNumber
            ]);
            
            // Mark seat as allocated
            $roomData[$currentRoomId]['layout'][$row][$col] = $student->id;
            $roomData[$currentRoomId]['allocated']++;
            
            // Move to next seat
            $col++;
            if ($col > $currentRoom->columns) {
                $col = 1;
                $row++;
            }
        }
    }

    /**
     * Allocate students with mixed strategy (random within room, sequential rooms).
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $students
     * @param  \Illuminate\Database\Eloquent\Collection  $rooms
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    private function allocateMixed($students, $rooms, $request)
    {
        // Sort rooms by block and room number
        $rooms = $rooms->sortBy(function($room) {
            return $room->block->block_name . '-' . $room->room_number;
        });
        
        // Prepare student groups by course and year
        $studentGroups = $students->groupBy(function($student) {
            return $student->course_id . '-' . $student->year;
        });
        
        // Allocate each group to a room or set of rooms
        $roomIndex = 0;
        foreach ($studentGroups as $groupKey => $groupStudents) {
            // Shuffle students within the group
            $groupStudents = $groupStudents->shuffle();
            
            $studentsToAllocate = $groupStudents->count();
            $studentsAllocated = 0;
            
            while ($studentsAllocated < $studentsToAllocate && $roomIndex < $rooms->count()) {
                $room = $rooms[$roomIndex];
                $roomCapacity = $room->capacity;
                
                // Get existing allocations for this room
                $existingAllocations = SeatingPlan::where('exam_name', $request->exam_name)
                    ->where('exam_date', $request->exam_date)
                    ->where('room_id', $room->id)
                    ->count();
                
                $availableCapacity = $roomCapacity - $existingAllocations;
                
                // Calculate how many students to allocate to this room
                $studentsForThisRoom = min($availableCapacity, $studentsToAllocate - $studentsAllocated);
                
                // Allocate students to this room
                for ($i = 0; $i < $studentsForThisRoom; $i++) {
                    $student = $groupStudents[$studentsAllocated + $i];
                    $seatNumber = $existingAllocations + $i + 1;
                    
                    SeatingPlan::create([
                        'exam_name' => $request->exam_name,
                        'exam_date' => $request->exam_date,
                        'start_time' => $request->start_time,
                        'end_time' => $request->end_time,
                        'room_id' => $room->id,
                        'student_id' => $student->id,
                        'seat_number' => $seatNumber
                    ]);
                }
                
                $studentsAllocated += $studentsForThisRoom;
                
                // If room is full or we've allocated all students, move to next room
                if ($existingAllocations + $studentsForThisRoom >= $roomCapacity || $studentsAllocated >= $studentsToAllocate) {
                    $roomIndex++;
                }
            }
        }
    }

    /**
     * Convert seat number to row and column.
     *
     * @param  int  $seatNumber
     * @param  int  $columns
     * @return array
     */
    private function getSeatRowCol($seatNumber, $columns)
    {
        $row = ceil($seatNumber / $columns);
        $col = $seatNumber % $columns;
        if ($col === 0) {
            $col = $columns;
        }
        
        return [
            'row' => $row,
            'col' => $col
        ];
    }
}
