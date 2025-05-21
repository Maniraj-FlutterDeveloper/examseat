<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    /**
     * Display a listing of the courses.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $courses = Course::orderBy('course_name')->paginate(10);
        return view('admin.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new course.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.courses.create');
    }

    /**
     * Store a newly created course in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_name' => 'required|string|max:100|unique:courses',
            'course_code' => 'required|string|max:20|unique:courses',
            'duration' => 'required|integer|min:1',
            'description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Course::create([
            'course_name' => $request->course_name,
            'course_code' => $request->course_code,
            'duration' => $request->duration,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course created successfully.');
    }

    /**
     * Display the specified course.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\View\View
     */
    public function show(Course $course)
    {
        $students = $course->students()->paginate(10);
        return view('admin.courses.show', compact('course', 'students'));
    }

    /**
     * Show the form for editing the specified course.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\View\View
     */
    public function edit(Course $course)
    {
        return view('admin.courses.edit', compact('course'));
    }

    /**
     * Update the specified course in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Course $course)
    {
        $validator = Validator::make($request->all(), [
            'course_name' => 'required|string|max:100|unique:courses,course_name,' . $course->id,
            'course_code' => 'required|string|max:20|unique:courses,course_code,' . $course->id,
            'duration' => 'required|integer|min:1',
            'description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $course->update([
            'course_name' => $request->course_name,
            'course_code' => $request->course_code,
            'duration' => $request->duration,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course updated successfully.');
    }

    /**
     * Remove the specified course from storage.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Course $course)
    {
        // Check if the course has any students
        if ($course->students()->count() > 0) {
            return redirect()->route('admin.courses.index')
                ->with('error', 'Cannot delete course because it has students assigned to it.');
        }

        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course deleted successfully.');
    }
}
