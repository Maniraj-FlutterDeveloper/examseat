<?php

namespace App\Http\Controllers\QuestionBank;

use App\Http\Controllers\Controller;
use App\Models\BloomsTaxonomy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BloomsTaxonomyController extends Controller
{
    /**
     * Display a listing of the Bloom's Taxonomy levels.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bloomsLevels = BloomsTaxonomy::withCount('questions')
            ->orderBy('level')
            ->get();
            
        return view('question-bank.blooms-taxonomy.index', compact('bloomsLevels'));
    }

    /**
     * Show the form for creating a new Bloom's Taxonomy level.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Get the highest level value to set the default for the new level
        $maxLevel = BloomsTaxonomy::max('level') ?? 0;
        
        return view('question-bank.blooms-taxonomy.create', compact('maxLevel'));
    }

    /**
     * Store a newly created Bloom's Taxonomy level in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:blooms_taxonomy',
            'description' => 'nullable|string',
            'level' => 'required|integer|min:1|unique:blooms_taxonomy',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('question-bank.blooms-taxonomy.create')
                ->withErrors($validator)
                ->withInput();
        }

        $bloomsLevel = BloomsTaxonomy::create($validator->validated());

        return redirect()->route('question-bank.blooms-taxonomy.index')
            ->with('success', 'Bloom\'s Taxonomy level created successfully.');
    }

    /**
     * Display the specified Bloom's Taxonomy level.
     *
     * @param  \App\Models\BloomsTaxonomy  $bloomsTaxonomy
     * @return \Illuminate\Http\Response
     */
    public function show(BloomsTaxonomy $bloomsTaxonomy)
    {
        $bloomsTaxonomy->loadCount('questions');
        
        $questions = $bloomsTaxonomy->questions()
            ->with(['topic.unit.subject', 'questionType'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('question-bank.blooms-taxonomy.show', [
            'bloomsLevel' => $bloomsTaxonomy,
            'questions' => $questions,
        ]);
    }

    /**
     * Show the form for editing the specified Bloom's Taxonomy level.
     *
     * @param  \App\Models\BloomsTaxonomy  $bloomsTaxonomy
     * @return \Illuminate\Http\Response
     */
    public function edit(BloomsTaxonomy $bloomsTaxonomy)
    {
        return view('question-bank.blooms-taxonomy.edit', [
            'bloomsLevel' => $bloomsTaxonomy,
        ]);
    }

    /**
     * Update the specified Bloom's Taxonomy level in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BloomsTaxonomy  $bloomsTaxonomy
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BloomsTaxonomy $bloomsTaxonomy)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('blooms_taxonomy')->ignore($bloomsTaxonomy->id),
            ],
            'description' => 'nullable|string',
            'level' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('blooms_taxonomy')->ignore($bloomsTaxonomy->id),
            ],
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('question-bank.blooms-taxonomy.edit', $bloomsTaxonomy)
                ->withErrors($validator)
                ->withInput();
        }

        $bloomsTaxonomy->update($validator->validated());

        return redirect()->route('question-bank.blooms-taxonomy.index')
            ->with('success', 'Bloom\'s Taxonomy level updated successfully.');
    }

    /**
     * Remove the specified Bloom's Taxonomy level from storage.
     *
     * @param  \App\Models\BloomsTaxonomy  $bloomsTaxonomy
     * @return \Illuminate\Http\Response
     */
    public function destroy(BloomsTaxonomy $bloomsTaxonomy)
    {
        // Check if the Bloom's Taxonomy level has associated questions
        if ($bloomsTaxonomy->questions()->count() > 0) {
            return redirect()->route('question-bank.blooms-taxonomy.index')
                ->with('error', 'Cannot delete Bloom\'s Taxonomy level with associated questions.');
        }

        $bloomsTaxonomy->delete();

        return redirect()->route('question-bank.blooms-taxonomy.index')
            ->with('success', 'Bloom\'s Taxonomy level deleted successfully.');
    }

    /**
     * Toggle the active status of the specified Bloom's Taxonomy level.
     *
     * @param  \App\Models\BloomsTaxonomy  $bloomsTaxonomy
     * @return \Illuminate\Http\Response
     */
    public function toggleActive(BloomsTaxonomy $bloomsTaxonomy)
    {
        $bloomsTaxonomy->is_active = !$bloomsTaxonomy->is_active;
        $bloomsTaxonomy->save();

        $status = $bloomsTaxonomy->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "Bloom's Taxonomy level {$status} successfully.");
    }
}

