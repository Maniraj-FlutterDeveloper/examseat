<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Student;
use App\Models\SeatingPlan;
use App\Models\SeatingOverride;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class SeatingOverrideController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $overrides = SeatingOverride::with(['seatingPlan', 'student', 'room'])->get();
        return view('seating.overrides.index', compact('overrides'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
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
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'seating_plan_id' => 'required|exists:seating_plans,id',
            'student_id' => 'required|exists:students,id',
            'room_id' => 'required|exists:rooms,id',
            'seat_number' => 'required|integer|min:1',
            'reason' => 'required|string',
            'created_by' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate that the seat number is within the room's capacity
        $room = Room::find($request->room_id);
        if ($request->seat_number > $room->capacity) {
            return redirect()->back()
                ->withErrors(['seat_number' => 'The seat number exceeds the room capacity.'])
                ->withInput();
        }

        // Check if the seat is already assigned in an override
        $existingOverride = SeatingOverride::where('seating_plan_id', $request->seating_plan_id)
            ->where('room_id', $request->room_id)
            ->where('seat_number', $request->seat_number)
            ->first();

        if ($existingOverride) {
            return redirect()->back()
                ->withErrors(['seat_number' => 'This seat is already assigned in an override.'])
                ->withInput();
        }

        // Check if the student is already assigned in an override for this seating plan
        $studentOverride = SeatingOverride::where('seating_plan_id', $request->seating_plan_id)
            ->where('student_id', $request->student_id)
            ->first();

        if ($studentOverride) {
            return redirect()->back()
                ->withErrors(['student_id' => 'This student already has an override for this seating plan.'])
                ->withInput();
        }

        SeatingOverride::create([
            'seating_plan_id' => $request->seating_plan_id,
            'student_id' => $request->student_id,
            'room_id' => $request->room_id,
            'seat_number' => $request->seat_number,
            'reason' => $request->reason,
            'created_by' => $request->created_by,
        ]);

        return redirect()->route('seating.overrides.index')
            ->with('success', 'Seating override created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SeatingOverride  $override
     * @return \Illuminate\Http\Response
     */
    public function show(SeatingOverride $override)
    {
        return view('seating.overrides.show', compact('override'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SeatingOverride  $override
     * @return \Illuminate\Http\Response
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
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SeatingOverride  $override
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SeatingOverride $override)
    {
        $validator = Validator::make($request->all(), [
            'seating_plan_id' => 'required|exists:seating_plans,id',
            'student_id' => 'required|exists:students,id',
            'room_id' => 'required|exists:rooms,id',
            'seat_number' => 'required|integer|min:1',
            'reason' => 'required|string',
            'created_by' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate that the seat number is within the room's capacity
        $room = Room::find($request->room_id);
        if ($request->seat_number > $room->capacity) {
            return redirect()->back()
                ->withErrors(['seat_number' => 'The seat number exceeds the room capacity.'])
                ->withInput();
        }

        // Check if the seat is already assigned in an override (excluding this one)
        $existingOverride = SeatingOverride::where('seating_plan_id', $request->seating_plan_id)
            ->where('room_id', $request->room_id)
            ->where('seat_number', $request->seat_number)
            ->where('id', '!=', $override->id)
            ->first();

        if ($existingOverride) {
            return redirect()->back()
                ->withErrors(['seat_number' => 'This seat is already assigned in another override.'])
                ->withInput();
        }

        // Check if the student is already assigned in an override for this seating plan (excluding this one)
        $studentOverride = SeatingOverride::where('seating_plan_id', $request->seating_plan_id)
            ->where('student_id', $request->student_id)
            ->where('id', '!=', $override->id)
            ->first();

        if ($studentOverride) {
            return redirect()->back()
                ->withErrors(['student_id' => 'This student already has another override for this seating plan.'])
                ->withInput();
        }

        $override->update([
            'seating_plan_id' => $request->seating_plan_id,
            'student_id' => $request->student_id,
            'room_id' => $request->room_id,
            'seat_number' => $request->seat_number,
            'reason' => $request->reason,
            'created_by' => $request->created_by,
        ]);

        return redirect()->route('seating.overrides.index')
            ->with('success', 'Seating override updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SeatingOverride  $override
     * @return \Illuminate\Http\Response
     */
    public function destroy(SeatingOverride $override)
    {
        $override->delete();

        return redirect()->route('seating.overrides.index')
            ->with('success', 'Seating override deleted successfully.');
    }
}

