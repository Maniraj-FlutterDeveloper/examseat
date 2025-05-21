<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    /**
     * Display a listing of the students.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $students = Student::with('course')->orderBy('roll_number')->paginate(10);
        return view('admin.students.index', compact('students'));
    }

    /**
     * Show the form for creating a new student.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $courses = Course::orderBy('course_name')->pluck('course_name', 'id');
        return view('admin.students.create', compact('courses'));
    }

    /**
     * Store a newly created student in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'roll_number' => 'required|string|max:20|unique:students',
            'course_id' => 'required|exists:courses,id',
            'year' => 'required|integer|min:1',
            'section' => 'nullable|string|max:10',
            'email' => 'nullable|email|max:100|unique:students',
            'phone' => 'nullable|string|max:20',
            'has_disability' => 'boolean',
            'disability_details' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Student::create([
            'name' => $request->name,
            'roll_number' => $request->roll_number,
            'course_id' => $request->course_id,
            'year' => $request->year,
            'section' => $request->section,
            'email' => $request->email,
            'phone' => $request->phone,
            'has_disability' => $request->has_disability ?? 0,
            'disability_details' => $request->disability_details,
        ]);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student created successfully.');
    }

    /**
     * Display the specified student.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\View\View
     */
    public function show(Student $student)
    {
        $seatingPlans = $student->seatingPlans()->with('room.block')->paginate(10);
        return view('admin.students.show', compact('student', 'seatingPlans'));
    }

    /**
     * Show the form for editing the specified student.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\View\View
     */
    public function edit(Student $student)
    {
        $courses = Course::orderBy('course_name')->pluck('course_name', 'id');
        return view('admin.students.edit', compact('student', 'courses'));
    }

    /**
     * Update the specified student in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Student $student)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'roll_number' => 'required|string|max:20|unique:students,roll_number,' . $student->id,
            'course_id' => 'required|exists:courses,id',
            'year' => 'required|integer|min:1',
            'section' => 'nullable|string|max:10',
            'email' => 'nullable|email|max:100|unique:students,email,' . $student->id,
            'phone' => 'nullable|string|max:20',
            'has_disability' => 'boolean',
            'disability_details' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $student->update([
            'name' => $request->name,
            'roll_number' => $request->roll_number,
            'course_id' => $request->course_id,
            'year' => $request->year,
            'section' => $request->section,
            'email' => $request->email,
            'phone' => $request->phone,
            'has_disability' => $request->has_disability ?? 0,
            'disability_details' => $request->disability_details,
        ]);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student updated successfully.');
    }

    /**
     * Remove the specified student from storage.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Student $student)
    {
        // Check if the student is assigned to any seating plans
        if ($student->seatingPlans()->count() > 0) {
            return redirect()->route('admin.students.index')
                ->with('error', 'Cannot delete student because they are assigned to seating plans.');
        }

        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'Student deleted successfully.');
    }

    /**
     * Show the form for importing students.
     *
     * @return \Illuminate\View\View
     */
    public function importForm()
    {
        $courses = Course::orderBy('course_name')->pluck('course_name', 'id');
        return view('admin.students.import', compact('courses'));
    }

    /**
     * Import students from CSV file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,txt|max:10240',
            'course_id' => 'required|exists:courses,id',
            'year' => 'required|integer|min:1',
            'section' => 'nullable|string|max:10',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $file = $request->file('file');
        $path = $file->getRealPath();
        
        $data = array_map('str_getcsv', file($path));
        
        // Check if file is empty
        if (count($data) <= 1) {
            return redirect()->back()
                ->with('error', 'The uploaded file is empty or contains only headers.');
        }
        
        // Get headers from first row
        $headers = array_shift($data);
        
        // Validate headers
        $requiredHeaders = ['name', 'roll_number'];
        $missingHeaders = array_diff($requiredHeaders, array_map('strtolower', $headers));
        
        if (!empty($missingHeaders)) {
            return redirect()->back()
                ->with('error', 'The uploaded file is missing required headers: ' . implode(', ', $missingHeaders));
        }
        
        // Map headers to lowercase
        $headers = array_map('strtolower', $headers);
        
        // Begin transaction
        DB::beginTransaction();
        
        try {
            $imported = 0;
            $errors = [];
            
            foreach ($data as $index => $row) {
                // Skip empty rows
                if (count(array_filter($row)) === 0) {
                    continue;
                }
                
                // Combine headers with row data
                $rowData = array_combine($headers, $row);
                
                // Add course_id, year, and section from form
                $rowData['course_id'] = $request->course_id;
                $rowData['year'] = $request->year;
                $rowData['section'] = $request->section;
                
                // Validate row data
                $rowValidator = Validator::make($rowData, [
                    'name' => 'required|string|max:100',
                    'roll_number' => 'required|string|max:20|unique:students',
                    'email' => 'nullable|email|max:100|unique:students',
                    'phone' => 'nullable|string|max:20',
                    'has_disability' => 'nullable|boolean',
                    'disability_details' => 'nullable|string|max:255',
                ]);
                
                if ($rowValidator->fails()) {
                    $errors[] = "Row " . ($index + 2) . " (Roll Number: {$rowData['roll_number']}): " . 
                                implode(', ', $rowValidator->errors()->all());
                    continue;
                }
                
                // Create student
                Student::create([
                    'name' => $rowData['name'],
                    'roll_number' => $rowData['roll_number'],
                    'course_id' => $rowData['course_id'],
                    'year' => $rowData['year'],
                    'section' => $rowData['section'],
                    'email' => $rowData['email'] ?? null,
                    'phone' => $rowData['phone'] ?? null,
                    'has_disability' => $rowData['has_disability'] ?? 0,
                    'disability_details' => $rowData['disability_details'] ?? null,
                ]);
                
                $imported++;
            }
            
            // If there are errors, rollback and show errors
            if (!empty($errors)) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'Import failed with the following errors:<br>' . implode('<br>', $errors))
                    ->withInput();
            }
            
            // Commit transaction
            DB::commit();
            
            return redirect()->route('admin.students.index')
                ->with('success', "Successfully imported {$imported} students.");
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'An error occurred during import: ' . $e->getMessage())
                ->withInput();
        }
    }
}
