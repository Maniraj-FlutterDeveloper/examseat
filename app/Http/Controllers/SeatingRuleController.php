<?php

namespace App\Http\Controllers;

use App\Models\SeatingRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SeatingRuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rules = SeatingRule::orderBy('priority', 'desc')->get();
        return view('seating.rules.index', compact('rules'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('seating.rules.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:alternate_courses,distance,priority',
            'description' => 'nullable|string',
            'parameters' => 'nullable|json',
            'is_active' => 'boolean',
            'priority' => 'integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $rule = SeatingRule::create([
            'name' => $request->name,
            'type' => $request->type,
            'description' => $request->description,
            'parameters' => $request->parameters ? json_decode($request->parameters, true) : null,
            'is_active' => $request->has('is_active'),
            'priority' => $request->priority ?? 0,
        ]);

        return redirect()->route('seating.rules.index')
            ->with('success', 'Seating rule created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SeatingRule $rule)
    {
        return view('seating.rules.show', compact('rule'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SeatingRule $rule)
    {
        return view('seating.rules.edit', compact('rule'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SeatingRule $rule)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:alternate_courses,distance,priority',
            'description' => 'nullable|string',
            'parameters' => 'nullable|json',
            'is_active' => 'boolean',
            'priority' => 'integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $rule->update([
            'name' => $request->name,
            'type' => $request->type,
            'description' => $request->description,
            'parameters' => $request->parameters ? json_decode($request->parameters, true) : null,
            'is_active' => $request->has('is_active'),
            'priority' => $request->priority ?? 0,
        ]);

        return redirect()->route('seating.rules.index')
            ->with('success', 'Seating rule updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SeatingRule $rule)
    {
        $rule->delete();

        return redirect()->route('seating.rules.index')
            ->with('success', 'Seating rule deleted successfully.');
    }
}

