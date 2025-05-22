<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentController extends Controller
{
    /**
     * Display a listing of the students.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $students = Student::with('course')->get();
        return view('seat-plan.students.index', compact('students'));
    }

    /**
     * Show the form for creating a new student.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $courses = Course::active()->get();
        return view('seat-plan.students.create', compact('courses'));
    }

    /**
     * Store a newly created student in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
            'roll_number' => 'required|string|max:50|unique:students',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'year' => 'required|integer|min:1',
            'section' => 'nullable|string|max:10',
            'has_disability' => 'boolean',
            'disability_details' => 'nullable|string|required_if:has_disability,1',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Student::create($request->all());

        return redirect()->route('students.index')
            ->with('success', 'Student created successfully.');
    }

    /**
     * Display the specified student.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function show(Student $student)
    {
        $student->load('course', 'seatingAssignments.seatingPlan', 'seatingAssignments.room');
        return view('seat-plan.students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified student.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function edit(Student $student)
    {
        $courses = Course::active()->get();
        return view('seat-plan.students.edit', compact('student', 'courses'));
    }

    /**
     * Update the specified student in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Student $student)
    {
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
            'roll_number' => 'required|string|max:50|unique:students,roll_number,' . $student->id,
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'year' => 'required|integer|min:1',
            'section' => 'nullable|string|max:10',
            'has_disability' => 'boolean',
            'disability_details' => 'nullable|string|required_if:has_disability,1',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $student->update($request->all());

        return redirect()->route('students.index')
            ->with('success', 'Student updated successfully.');
    }

    /**
     * Remove the specified student from storage.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function destroy(Student $student)
    {
        // Check if the student has seating assignments
        if ($student->seatingAssignments()->count() > 0) {
            return redirect()->route('students.index')
                ->with('error', 'Cannot delete student because they have associated seating assignments.');
        }

        $student->delete();

        return redirect()->route('students.index')
            ->with('success', 'Student deleted successfully.');
    }

    /**
     * Toggle the active status of the specified student.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function toggleActive(Student $student)
    {
        $student->is_active = !$student->is_active;
        $student->save();

        return redirect()->route('students.index')
            ->with('success', 'Student status updated successfully.');
    }

    /**
     * Import students from CSV/Excel file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,txt,xlsx,xls',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Process the file import logic here
            // This is a placeholder for the actual import logic
            // You would typically use a package like maatwebsite/excel for this

            return redirect()->route('students.index')
                ->with('success', 'Students imported successfully.');
        } catch (\Exception $e) {
            Log::error('Student import error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error importing students: ' . $e->getMessage());
        }
    }

    /**
     * Show the import form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showImportForm()
    {
        $courses = Course::active()->get();
        return view('seat-plan.students.import', compact('courses'));
    }

    /**
     * Get students by course, year, and section.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getStudentsByFilters(Request $request)
    {
        $query = Student::query();
        
        if ($request->has('course_id') && $request->course_id) {
            $query->where('course_id', $request->course_id);
        }
        
        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }
        
        if ($request->has('section') && $request->section) {
            $query->where('section', $request->section);
        }
        
        $students = $query->with('course')->get();
        
        return response()->json($students);
    }
}

