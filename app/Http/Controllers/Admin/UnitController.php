<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Unit::with('subject');
        
        // Filter by subject if provided
        if ($request->has('subject_id') && $request->subject_id) {
            $query->where('subject_id', $request->subject_id);
        }
        
        $units = $query->orderBy('subject_id')->orderBy('order')->paginate(10);
        $subjects = Subject::orderBy('subject_name')->get();
        
        return view('admin.units.index', compact('units', 'subjects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $subjects = Subject::where('is_active', true)->orderBy('subject_name')->get();
        return view('admin.units.create', compact('subjects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject_id' => 'required|exists:subjects,id',
            'unit_name' => 'required|string|max:255',
            'unit_code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $unit = Unit::create([
            'subject_id' => $request->subject_id,
            'unit_name' => $request->unit_name,
            'unit_code' => $request->unit_code,
            'description' => $request->description,
            'order' => $request->order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.units.index')
            ->with('success', 'Unit created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Unit $unit)
    {
        $unit->load('subject', 'topics');
        return view('admin.units.show', compact('unit'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unit $unit)
    {
        $subjects = Subject::where('is_active', true)->orderBy('subject_name')->get();
        return view('admin.units.edit', compact('unit', 'subjects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit)
    {
        $validator = Validator::make($request->all(), [
            'subject_id' => 'required|exists:subjects,id',
            'unit_name' => 'required|string|max:255',
            'unit_code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $unit->update([
            'subject_id' => $request->subject_id,
            'unit_name' => $request->unit_name,
            'unit_code' => $request->unit_code,
            'description' => $request->description,
            'order' => $request->order ?? $unit->order,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.units.index')
            ->with('success', 'Unit updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        // Check if unit has topics
        if ($unit->topics()->count() > 0) {
            return redirect()->route('admin.units.index')
                ->with('error', 'Cannot delete unit because it has associated topics.');
        }

        $unit->delete();

        return redirect()->route('admin.units.index')
            ->with('success', 'Unit deleted successfully.');
    }
}
