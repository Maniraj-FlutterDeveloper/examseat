<?php

namespace App\Http\Controllers;

use App\Models\SeatingRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SeatingRuleController extends Controller
{
    /**
     * Display a listing of the seating rules.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $seatingRules = SeatingRule::withCount('seatingPlanRules')->get();
        return view('seat-plan.seating-rules.index', compact('seatingRules'));
    }

    /**
     * Show the form for creating a new seating rule.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $ruleTypes = [
            'alternate_seating' => 'Alternate Seating',
            'mixed_branches' => 'Mixed Branches',
            'special_needs' => 'Special Needs',
            'custom' => 'Custom',
        ];
        
        return view('seat-plan.seating-rules.create', compact('ruleTypes'));
    }

    /**
     * Store a newly created seating rule in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:alternate_seating,mixed_branches,special_needs,custom',
            'configuration' => 'nullable|json',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        
        // Parse JSON configuration if provided
        if (isset($data['configuration']) && is_string($data['configuration'])) {
            $data['configuration'] = json_decode($data['configuration'], true);
        }

        SeatingRule::create($data);

        return redirect()->route('seating-rules.index')
            ->with('success', 'Seating rule created successfully.');
    }

    /**
     * Display the specified seating rule.
     *
     * @param  \App\Models\SeatingRule  $seatingRule
     * @return \Illuminate\Http\Response
     */
    public function show(SeatingRule $seatingRule)
    {
        $seatingRule->load('seatingPlanRules.seatingPlan');
        return view('seat-plan.seating-rules.show', compact('seatingRule'));
    }

    /**
     * Show the form for editing the specified seating rule.
     *
     * @param  \App\Models\SeatingRule  $seatingRule
     * @return \Illuminate\Http\Response
     */
    public function edit(SeatingRule $seatingRule)
    {
        $ruleTypes = [
            'alternate_seating' => 'Alternate Seating',
            'mixed_branches' => 'Mixed Branches',
            'special_needs' => 'Special Needs',
            'custom' => 'Custom',
        ];
        
        return view('seat-plan.seating-rules.edit', compact('seatingRule', 'ruleTypes'));
    }

    /**
     * Update the specified seating rule in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SeatingRule  $seatingRule
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SeatingRule $seatingRule)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:alternate_seating,mixed_branches,special_needs,custom',
            'configuration' => 'nullable|json',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        
        // Parse JSON configuration if provided
        if (isset($data['configuration']) && is_string($data['configuration'])) {
            $data['configuration'] = json_decode($data['configuration'], true);
        }

        $seatingRule->update($data);

        return redirect()->route('seating-rules.index')
            ->with('success', 'Seating rule updated successfully.');
    }

    /**
     * Remove the specified seating rule from storage.
     *
     * @param  \App\Models\SeatingRule  $seatingRule
     * @return \Illuminate\Http\Response
     */
    public function destroy(SeatingRule $seatingRule)
    {
        // Check if the seating rule is used in any seating plans
        if ($seatingRule->seatingPlanRules()->count() > 0) {
            return redirect()->route('seating-rules.index')
                ->with('error', 'Cannot delete seating rule because it is used in seating plans.');
        }

        $seatingRule->delete();

        return redirect()->route('seating-rules.index')
            ->with('success', 'Seating rule deleted successfully.');
    }

    /**
     * Toggle the active status of the specified seating rule.
     *
     * @param  \App\Models\SeatingRule  $seatingRule
     * @return \Illuminate\Http\Response
     */
    public function toggleActive(SeatingRule $seatingRule)
    {
        $seatingRule->is_active = !$seatingRule->is_active;
        $seatingRule->save();

        return redirect()->route('seating-rules.index')
            ->with('success', 'Seating rule status updated successfully.');
    }

    /**
     * Get configuration template for a specific rule type.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getConfigurationTemplate(Request $request)
    {
        $type = $request->input('type');
        
        $template = [];
        
        switch ($type) {
            case 'alternate_seating':
                $template = [
                    'gap' => 1,
                    'pattern' => 'zigzag',
                    'apply_to_rows' => true,
                    'apply_to_columns' => true,
                ];
                break;
                
            case 'mixed_branches':
                $template = [
                    'mix_courses' => true,
                    'mix_years' => true,
                    'mix_sections' => true,
                    'max_same_course_adjacent' => 0,
                ];
                break;
                
            case 'special_needs':
                $template = [
                    'priority_seating' => true,
                    'near_exit' => false,
                    'ground_floor' => true,
                    'extra_space' => false,
                ];
                break;
                
            case 'custom':
                $template = [
                    'custom_logic' => '',
                    'parameters' => [],
                ];
                break;
        }
        
        return response()->json($template);
    }
}

