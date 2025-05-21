<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentPriority;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentPriorityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $priorities = StudentPriority::with('student')->get();
        return view('seating.priorities.index', compact('priorities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $students = Student::all();
        return view('seating.priorities.create', compact('students'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'priority_level' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'valid_until' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $priority = StudentPriority::create([
            'student_id' => $request->student_id,
            'priority_level' => $request->priority_level,
            'reason' => $request->reason,
            'notes' => $request->notes,
            'valid_until' => $request->valid_until,
        ]);

        return redirect()->route('seating.priorities.index')
            ->with('success', 'Student priority created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(StudentPriority $priority)
    {
        return view('seating.priorities.show', compact('priority'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StudentPriority $priority)
    {
        $students = Student::all();
        return view('seating.priorities.edit', compact('priority', 'students'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StudentPriority $priority)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'priority_level' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'valid_until' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $priority->update([
            'student_id' => $request->student_id,
            'priority_level' => $request->priority_level,
            'reason' => $request->reason,
            'notes' => $request->notes,
            'valid_until' => $request->valid_until,
        ]);

        return redirect()->route('seating.priorities.index')
            ->with('success', 'Student priority updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudentPriority $priority)
    {
        $priority->delete();

        return redirect()->route('seating.priorities.index')
            ->with('success', 'Student priority deleted successfully.');
    }
}

