<?php

namespace App\Http\Controllers;

use App\Models\SeatingRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SeatingRuleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rules = SeatingRule::orderBy('priority', 'desc')->get();
        return view('seating.rules.index', compact('rules'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('seating.rules.create');
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
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:alternate_courses,distance,priority',
            'description' => 'nullable|string',
            'priority' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'parameters' => 'nullable|json',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Parse parameters JSON if provided
        $parameters = null;
        if ($request->has('parameters') && !empty($request->parameters)) {
            $parameters = json_decode($request->parameters, true);
            
            // Validate parameters based on rule type
            if ($request->type == 'alternate_courses' && (!isset($parameters['min_distance']) || !is_numeric($parameters['min_distance']))) {
                return redirect()->back()
                    ->withErrors(['parameters' => 'For alternate courses rule, min_distance parameter is required and must be numeric.'])
                    ->withInput();
            } elseif ($request->type == 'distance' && (!isset($parameters['distance']) || !is_numeric($parameters['distance']))) {
                return redirect()->back()
                    ->withErrors(['parameters' => 'For distance rule, distance parameter is required and must be numeric.'])
                    ->withInput();
            } elseif ($request->type == 'priority' && (!isset($parameters['seats_per_row']) || !is_numeric($parameters['seats_per_row']))) {
                return redirect()->back()
                    ->withErrors(['parameters' => 'For priority rule, seats_per_row parameter is required and must be numeric.'])
                    ->withInput();
            }
        }

        SeatingRule::create([
            'name' => $request->name,
            'type' => $request->type,
            'description' => $request->description,
            'parameters' => $parameters,
            'priority' => $request->priority,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('seating.rules.index')
            ->with('success', 'Seating rule created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SeatingRule  $rule
     * @return \Illuminate\Http\Response
     */
    public function show(SeatingRule $rule)
    {
        return view('seating.rules.show', compact('rule'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SeatingRule  $rule
     * @return \Illuminate\Http\Response
     */
    public function edit(SeatingRule $rule)
    {
        return view('seating.rules.edit', compact('rule'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SeatingRule  $rule
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SeatingRule $rule)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:alternate_courses,distance,priority',
            'description' => 'nullable|string',
            'priority' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'parameters' => 'nullable|json',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Parse parameters JSON if provided
        $parameters = null;
        if ($request->has('parameters') && !empty($request->parameters)) {
            $parameters = json_decode($request->parameters, true);
            
            // Validate parameters based on rule type
            if ($request->type == 'alternate_courses' && (!isset($parameters['min_distance']) || !is_numeric($parameters['min_distance']))) {
                return redirect()->back()
                    ->withErrors(['parameters' => 'For alternate courses rule, min_distance parameter is required and must be numeric.'])
                    ->withInput();
            } elseif ($request->type == 'distance' && (!isset($parameters['distance']) || !is_numeric($parameters['distance']))) {
                return redirect()->back()
                    ->withErrors(['parameters' => 'For distance rule, distance parameter is required and must be numeric.'])
                    ->withInput();
            } elseif ($request->type == 'priority' && (!isset($parameters['seats_per_row']) || !is_numeric($parameters['seats_per_row']))) {
                return redirect()->back()
                    ->withErrors(['parameters' => 'For priority rule, seats_per_row parameter is required and must be numeric.'])
                    ->withInput();
            }
        }

        $rule->update([
            'name' => $request->name,
            'type' => $request->type,
            'description' => $request->description,
            'parameters' => $parameters,
            'priority' => $request->priority,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('seating.rules.index')
            ->with('success', 'Seating rule updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SeatingRule  $rule
     * @return \Illuminate\Http\Response
     */
    public function destroy(SeatingRule $rule)
    {
        $rule->delete();

        return redirect()->route('seating.rules.index')
            ->with('success', 'Seating rule deleted successfully.');
    }
}

