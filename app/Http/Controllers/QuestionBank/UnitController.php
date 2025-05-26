<?php

namespace App\Http\Controllers\QuestionBank;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UnitController extends Controller
{
    /**
     * Display a listing of the units.
     *
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function index(Subject $subject)
    {
        $units = $subject->units()
            ->withCount(['topics', 'questions'])
            ->orderBy('order')
            ->paginate(10);
            
        return view('question-bank.units.index', compact('subject', 'units'));
    }

    /**
     * Show the form for creating a new unit.
     *
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function create(Subject $subject)
    {
        // Get the highest order value to set the default for the new unit
        $maxOrder = $subject->units()->max('order') ?? 0;
        
        return view('question-bank.units.create', compact('subject', 'maxOrder'));
    }

    /**
     * Store a newly created unit in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Subject $subject)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('units')->where(function ($query) use ($subject) {
                    return $query->where('subject_id', $subject->id);
                }),
            ],
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('units')->where(function ($query) use ($subject) {
                    return $query->where('subject_id', $subject->id);
                }),
            ],
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('question-bank.subjects.units.create', $subject)
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        
        // If order is not provided, set it to the highest order + 1
        if (!isset($data['order'])) {
            $data['order'] = $subject->units()->max('order') + 1;
        }
        
        $unit = $subject->units()->create($data);

        return redirect()->route('question-bank.subjects.units.show', [$subject, $unit])
            ->with('success', 'Unit created successfully.');
    }

    /**
     * Display the specified unit.
     *
     * @param  \App\Models\Subject  $subject
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function show(Subject $subject, Unit $unit)
    {
        // Ensure the unit belongs to the subject
        if ($unit->subject_id !== $subject->id) {
            abort(404);
        }
        
        $unit->load(['topics' => function ($query) {
            $query->withCount('questions')->orderBy('order');
        }]);
        
        $unit->loadCount(['topics', 'questions']);
        
        return view('question-bank.units.show', compact('subject', 'unit'));
    }

    /**
     * Show the form for editing the specified unit.
     *
     * @param  \App\Models\Subject  $subject
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function edit(Subject $subject, Unit $unit)
    {
        // Ensure the unit belongs to the subject
        if ($unit->subject_id !== $subject->id) {
            abort(404);
        }
        
        return view('question-bank.units.edit', compact('subject', 'unit'));
    }

    /**
     * Update the specified unit in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Subject  $subject
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Subject $subject, Unit $unit)
    {
        // Ensure the unit belongs to the subject
        if ($unit->subject_id !== $subject->id) {
            abort(404);
        }
        
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('units')->where(function ($query) use ($subject) {
                    return $query->where('subject_id', $subject->id);
                })->ignore($unit->id),
            ],
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('units')->where(function ($query) use ($subject) {
                    return $query->where('subject_id', $subject->id);
                })->ignore($unit->id),
            ],
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('question-bank.subjects.units.edit', [$subject, $unit])
                ->withErrors($validator)
                ->withInput();
        }

        $unit->update($validator->validated());

        return redirect()->route('question-bank.subjects.units.show', [$subject, $unit])
            ->with('success', 'Unit updated successfully.');
    }

    /**
     * Remove the specified unit from storage.
     *
     * @param  \App\Models\Subject  $subject
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subject $subject, Unit $unit)
    {
        // Ensure the unit belongs to the subject
        if ($unit->subject_id !== $subject->id) {
            abort(404);
        }
        
        // Check if unit has topics
        if ($unit->topics()->count() > 0) {
            return redirect()->route('question-bank.subjects.units.show', [$subject, $unit])
                ->with('error', 'Cannot delete unit with associated topics. Please delete the topics first.');
        }

        $unit->delete();

        return redirect()->route('question-bank.subjects.units.index', $subject)
            ->with('success', 'Unit deleted successfully.');
    }

    /**
     * Toggle the active status of the specified unit.
     *
     * @param  \App\Models\Subject  $subject
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function toggleActive(Subject $subject, Unit $unit)
    {
        // Ensure the unit belongs to the subject
        if ($unit->subject_id !== $subject->id) {
            abort(404);
        }
        
        $unit->is_active = !$unit->is_active;
        $unit->save();

        $status = $unit->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "Unit {$status} successfully.");
    }

    /**
     * Reorder units.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function reorder(Request $request, Subject $subject)
    {
        $validator = Validator::make($request->all(), [
            'units' => 'required|array',
            'units.*' => 'required|integer|exists:units,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $unitIds = $request->input('units');
        
        // Update the order of each unit
        foreach ($unitIds as $index => $unitId) {
            $unit = Unit::find($unitId);
            
            // Ensure the unit belongs to the subject
            if ($unit && $unit->subject_id === $subject->id) {
                $unit->order = $index + 1;
                $unit->save();
            }
        }

        return response()->json(['success' => true]);
    }
}

