<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Student;
use App\Models\SeatingPlan;
use App\Models\SeatingOverride;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SeatingOverrideController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $overrides = SeatingOverride::with(['seatingPlan', 'student', 'room', 'creator'])->get();
        return view('seating.overrides.index', compact('overrides'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $seatingPlans = SeatingPlan::all();
        $students = Student::all();
        $rooms = Room::all();
        return view('seating.overrides.create', compact('seatingPlans', 'students', 'rooms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'seating_plan_id' => 'required|exists:seating_plans,id',
            'student_id' => 'required|exists:students,id',
            'room_id' => 'required|exists:rooms,id',
            'seat_number' => 'required|string|max:10',
            'reason' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if the seat is already assigned
        $existingOverride = SeatingOverride::where('seating_plan_id', $request->seating_plan_id)
            ->where('room_id', $request->room_id)
            ->where('seat_number', $request->seat_number)
            ->first();

        if ($existingOverride) {
            return redirect()->back()
                ->withErrors(['seat_number' => 'This seat is already assigned to another student.'])
                ->withInput();
        }

        // Check if the student is already assigned
        $existingStudentOverride = SeatingOverride::where('seating_plan_id', $request->seating_plan_id)
            ->where('student_id', $request->student_id)
            ->first();

        if ($existingStudentOverride) {
            return redirect()->back()
                ->withErrors(['student_id' => 'This student is already assigned to another seat.'])
                ->withInput();
        }

        $override = SeatingOverride::create([
            'seating_plan_id' => $request->seating_plan_id,
            'student_id' => $request->student_id,
            'room_id' => $request->room_id,
            'seat_number' => $request->seat_number,
            'reason' => $request->reason,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('seating.overrides.index')
            ->with('success', 'Seating override created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SeatingOverride $override)
    {
        return view('seating.overrides.show', compact('override'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SeatingOverride $override)
    {
        $seatingPlans = SeatingPlan::all();
        $students = Student::all();
        $rooms = Room::all();
        return view('seating.overrides.edit', compact('override', 'seatingPlans', 'students', 'rooms'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SeatingOverride $override)
    {
        $validator = Validator::make($request->all(), [
            'seating_plan_id' => 'required|exists:seating_plans,id',
            'student_id' => 'required|exists:students,id',
            'room_id' => 'required|exists:rooms,id',
            'seat_number' => 'required|string|max:10',
            'reason' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if the seat is already assigned (excluding this override)
        $existingOverride = SeatingOverride::where('seating_plan_id', $request->seating_plan_id)
            ->where('room_id', $request->room_id)
            ->where('seat_number', $request->seat_number)
            ->where('id', '!=', $override->id)
            ->first();

        if ($existingOverride) {
            return redirect()->back()
                ->withErrors(['seat_number' => 'This seat is already assigned to another student.'])
                ->withInput();
        }

        // Check if the student is already assigned (excluding this override)
        $existingStudentOverride = SeatingOverride::where('seating_plan_id', $request->seating_plan_id)
            ->where('student_id', $request->student_id)
            ->where('id', '!=', $override->id)
            ->first();

        if ($existingStudentOverride) {
            return redirect()->back()
                ->withErrors(['student_id' => 'This student is already assigned to another seat.'])
                ->withInput();
        }

        $override->update([
            'seating_plan_id' => $request->seating_plan_id,
            'student_id' => $request->student_id,
            'room_id' => $request->room_id,
            'seat_number' => $request->seat_number,
            'reason' => $request->reason,
        ]);

        return redirect()->route('seating.overrides.index')
            ->with('success', 'Seating override updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SeatingOverride $override)
    {
        $override->delete();

        return redirect()->route('seating.overrides.index')
            ->with('success', 'Seating override deleted successfully.');
    }
}

