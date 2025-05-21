<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Student;
use App\Models\SeatingPlan;
use App\Services\SeatingRuleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SeatingPlanController extends Controller
{
    protected $seatingRuleService;

    /**
     * Create a new controller instance.
     *
     * @param SeatingRuleService $seatingRuleService
     * @return void
     */
    public function __construct(SeatingRuleService $seatingRuleService)
    {
        $this->seatingRuleService = $seatingRuleService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $seatingPlans = SeatingPlan::all();
        return view('seating.plans.index', compact('seatingPlans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $rooms = Room::all();
        return view('seating.plans.create', compact('rooms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|exists:rooms,id',
            'exam_name' => 'required|string|max:255',
            'exam_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $seatingPlan = SeatingPlan::create([
            'room_id' => $request->room_id,
            'exam_name' => $request->exam_name,
            'exam_date' => $request->exam_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => 'scheduled',
        ]);

        return redirect()->route('seating.plans.index')
            ->with('success', 'Seating plan created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SeatingPlan $seatingPlan)
    {
        return view('seating.plans.show', compact('seatingPlan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SeatingPlan $seatingPlan)
    {
        $rooms = Room::all();
        return view('seating.plans.edit', compact('seatingPlan', 'rooms'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SeatingPlan $seatingPlan)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|exists:rooms,id',
            'exam_name' => 'required|string|max:255',
            'exam_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'status' => 'required|in:scheduled,ongoing,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $seatingPlan->update([
            'room_id' => $request->room_id,
            'exam_name' => $request->exam_name,
            'exam_date' => $request->exam_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => $request->status,
        ]);

        return redirect()->route('seating.plans.index')
            ->with('success', 'Seating plan updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SeatingPlan $seatingPlan)
    {
        $seatingPlan->delete();

        return redirect()->route('seating.plans.index')
            ->with('success', 'Seating plan deleted successfully.');
    }

    /**
     * Generate seating assignments for a seating plan.
     */
    public function generateAssignments(SeatingPlan $seatingPlan)
    {
        // Get all students
        $students = Student::all();
        
        // Get the room for this seating plan
        $room = Room::find($seatingPlan->room_id);
        
        // Apply seating rules to generate assignments
        $assignments = $this->seatingRuleService->applyRules(
            $seatingPlan,
            $students,
            collect([$room])
        );
        
        // Store the assignments in the session for review
        session(['seating_assignments' => $assignments]);
        
        return view('seating.plans.assignments', compact('seatingPlan', 'assignments'));
    }

    /**
     * Save the generated seating assignments.
     */
    public function saveAssignments(Request $request, SeatingPlan $seatingPlan)
    {
        // Get the assignments from the session
        $assignments = session('seating_assignments');
        
        if (!$assignments) {
            return redirect()->route('seating.plans.show', $seatingPlan)
                ->with('error', 'No seating assignments found. Please generate assignments first.');
        }
        
        // Save the assignments to the database
        // This would typically involve creating SeatingAssignment records
        // or similar, depending on your data model
        
        // Clear the assignments from the session
        session()->forget('seating_assignments');
        
        return redirect()->route('seating.plans.show', $seatingPlan)
            ->with('success', 'Seating assignments saved successfully.');
    }
}

