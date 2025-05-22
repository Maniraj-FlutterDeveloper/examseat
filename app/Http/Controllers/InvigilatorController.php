<?php

namespace App\Http\Controllers;

use App\Models\Invigilator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InvigilatorController extends Controller
{
    /**
     * Display a listing of the invigilators.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invigilators = Invigilator::withCount('invigilatorAssignments')->get();
        return view('seat-plan.invigilators.index', compact('invigilators'));
    }

    /**
     * Show the form for creating a new invigilator.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('seat-plan.invigilators.create');
    }

    /**
     * Store a newly created invigilator in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Invigilator::create($request->all());

        return redirect()->route('invigilators.index')
            ->with('success', 'Invigilator created successfully.');
    }

    /**
     * Display the specified invigilator.
     *
     * @param  \App\Models\Invigilator  $invigilator
     * @return \Illuminate\Http\Response
     */
    public function show(Invigilator $invigilator)
    {
        $invigilator->load('invigilatorAssignments.seatingPlan', 'invigilatorAssignments.room');
        $upcomingAssignments = $invigilator->getUpcomingAssignments();
        
        return view('seat-plan.invigilators.show', compact('invigilator', 'upcomingAssignments'));
    }

    /**
     * Show the form for editing the specified invigilator.
     *
     * @param  \App\Models\Invigilator  $invigilator
     * @return \Illuminate\Http\Response
     */
    public function edit(Invigilator $invigilator)
    {
        return view('seat-plan.invigilators.edit', compact('invigilator'));
    }

    /**
     * Update the specified invigilator in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invigilator  $invigilator
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invigilator $invigilator)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $invigilator->update($request->all());

        return redirect()->route('invigilators.index')
            ->with('success', 'Invigilator updated successfully.');
    }

    /**
     * Remove the specified invigilator from storage.
     *
     * @param  \App\Models\Invigilator  $invigilator
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invigilator $invigilator)
    {
        // Check if the invigilator has assignments
        if ($invigilator->invigilatorAssignments()->count() > 0) {
            return redirect()->route('invigilators.index')
                ->with('error', 'Cannot delete invigilator because they have associated assignments.');
        }

        $invigilator->delete();

        return redirect()->route('invigilators.index')
            ->with('success', 'Invigilator deleted successfully.');
    }

    /**
     * Toggle the active status of the specified invigilator.
     *
     * @param  \App\Models\Invigilator  $invigilator
     * @return \Illuminate\Http\Response
     */
    public function toggleActive(Invigilator $invigilator)
    {
        $invigilator->is_active = !$invigilator->is_active;
        $invigilator->save();

        return redirect()->route('invigilators.index')
            ->with('success', 'Invigilator status updated successfully.');
    }

    /**
     * Get invigilators by department.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getInvigilatorsByDepartment(Request $request)
    {
        $department = $request->input('department');
        
        $query = Invigilator::active();
        
        if ($department) {
            $query->where('department', $department);
        }
        
        $invigilators = $query->get();
        
        return response()->json($invigilators);
    }

    /**
     * Import invigilators from CSV/Excel file.
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

            return redirect()->route('invigilators.index')
                ->with('success', 'Invigilators imported successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error importing invigilators: ' . $e->getMessage());
        }
    }

    /**
     * Show the import form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showImportForm()
    {
        return view('seat-plan.invigilators.import');
    }
}

