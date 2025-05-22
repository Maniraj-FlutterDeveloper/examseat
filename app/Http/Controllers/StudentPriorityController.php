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
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $priorities = StudentPriority::with('student')->get();
        return view('seating.priorities.index', compact('priorities'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $students = Student::all();
        return view('seating.priorities.create', compact('students'));
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
            'student_id' => 'required|exists:students,id',
            'priority_type' => 'required|string|in:disability,medical,other',
            'priority_level' => 'required|integer|min:1|max:10',
            'description' => 'nullable|string',
            'requirements' => 'nullable|string',
            'valid_until' => 'nullable|date|after:today',
            'is_verified' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if student already has a priority of the same type
        $existingPriority = StudentPriority::where('student_id', $request->student_id)
            ->where('priority_type', $request->priority_type)
            ->first();

        if ($existingPriority) {
            return redirect()->back()
                ->withErrors(['student_id' => 'This student already has a priority of this type.'])
                ->withInput();
        }

        StudentPriority::create([
            'student_id' => $request->student_id,
            'priority_type' => $request->priority_type,
            'priority_level' => $request->priority_level,
            'description' => $request->description,
            'requirements' => $request->requirements,
            'valid_until' => $request->valid_until,
            'is_verified' => $request->has('is_verified'),
        ]);

        return redirect()->route('seating.priorities.index')
            ->with('success', 'Student priority created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\StudentPriority  $priority
     * @return \Illuminate\Http\Response
     */
    public function show(StudentPriority $priority)
    {
        return view('seating.priorities.show', compact('priority'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\StudentPriority  $priority
     * @return \Illuminate\Http\Response
     */
    public function edit(StudentPriority $priority)
    {
        $students = Student::all();
        return view('seating.priorities.edit', compact('priority', 'students'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\StudentPriority  $priority
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StudentPriority $priority)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'priority_type' => 'required|string|in:disability,medical,other',
            'priority_level' => 'required|integer|min:1|max:10',
            'description' => 'nullable|string',
            'requirements' => 'nullable|string',
            'valid_until' => 'nullable|date|after:today',
            'is_verified' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if student already has a priority of the same type (excluding this one)
        $existingPriority = StudentPriority::where('student_id', $request->student_id)
            ->where('priority_type', $request->priority_type)
            ->where('id', '!=', $priority->id)
            ->first();

        if ($existingPriority) {
            return redirect()->back()
                ->withErrors(['student_id' => 'This student already has another priority of this type.'])
                ->withInput();
        }

        $priority->update([
            'student_id' => $request->student_id,
            'priority_type' => $request->priority_type,
            'priority_level' => $request->priority_level,
            'description' => $request->description,
            'requirements' => $request->requirements,
            'valid_until' => $request->valid_until,
            'is_verified' => $request->has('is_verified'),
        ]);

        return redirect()->route('seating.priorities.index')
            ->with('success', 'Student priority updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\StudentPriority  $priority
     * @return \Illuminate\Http\Response
     */
    public function destroy(StudentPriority $priority)
    {
        $priority->delete();

        return redirect()->route('seating.priorities.index')
            ->with('success', 'Student priority deleted successfully.');
    }
}

