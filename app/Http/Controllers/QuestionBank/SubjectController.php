<?php

namespace App\Http\Controllers\QuestionBank;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    /**
     * Display a listing of the subjects.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $subjects = Subject::withCount(['units', 'topics', 'questions'])
            ->orderBy('name')
            ->paginate(10);
            
        return view('question-bank.subjects.index', compact('subjects'));
    }

    /**
     * Show the form for creating a new subject.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('question-bank.subjects.create');
    }

    /**
     * Store a newly created subject in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:subjects',
            'code' => 'nullable|string|max:50|unique:subjects',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('question-bank.subjects.create')
                ->withErrors($validator)
                ->withInput();
        }

        $subject = Subject::create($validator->validated());

        return redirect()->route('question-bank.subjects.show', $subject)
            ->with('success', 'Subject created successfully.');
    }

    /**
     * Display the specified subject.
     *
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function show(Subject $subject)
    {
        $subject->load(['units' => function ($query) {
            $query->withCount('topics')->orderBy('order');
        }]);
        
        $subject->loadCount(['units', 'topics', 'questions']);
        
        return view('question-bank.subjects.show', compact('subject'));
    }

    /**
     * Show the form for editing the specified subject.
     *
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function edit(Subject $subject)
    {
        return view('question-bank.subjects.edit', compact('subject'));
    }

    /**
     * Update the specified subject in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Subject $subject)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('subjects')->ignore($subject->id),
            ],
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('subjects')->ignore($subject->id),
            ],
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('question-bank.subjects.edit', $subject)
                ->withErrors($validator)
                ->withInput();
        }

        $subject->update($validator->validated());

        return redirect()->route('question-bank.subjects.show', $subject)
            ->with('success', 'Subject updated successfully.');
    }

    /**
     * Remove the specified subject from storage.
     *
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subject $subject)
    {
        // Check if subject has units
        if ($subject->units()->count() > 0) {
            return redirect()->route('question-bank.subjects.show', $subject)
                ->with('error', 'Cannot delete subject with associated units. Please delete the units first.');
        }

        $subject->delete();

        return redirect()->route('question-bank.subjects.index')
            ->with('success', 'Subject deleted successfully.');
    }

    /**
     * Toggle the active status of the specified subject.
     *
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function toggleActive(Subject $subject)
    {
        $subject->is_active = !$subject->is_active;
        $subject->save();

        $status = $subject->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "Subject {$status} successfully.");
    }

    /**
     * Search for subjects.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        
        $subjects = Subject::where('name', 'like', "%{$query}%")
            ->orWhere('code', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->withCount(['units', 'topics', 'questions'])
            ->orderBy('name')
            ->paginate(10);
            
        return view('question-bank.subjects.index', compact('subjects', 'query'));
    }
}

