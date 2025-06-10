<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BloomsTaxonomy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BloomsTaxonomyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bloomsLevels = BloomsTaxonomy::orderBy('order')->paginate(10);
        return view('admin.blooms_taxonomy.index', compact('bloomsLevels'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.blooms_taxonomy.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'level_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $bloomsLevel = BloomsTaxonomy::create([
            'level_name' => $request->level_name,
            'description' => $request->description,
            'order' => $request->order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.blooms-taxonomy.index')
            ->with('success', 'Bloom\'s Taxonomy level created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(BloomsTaxonomy $bloomsTaxonomy)
    {
        $bloomsTaxonomy->load('questions');
        return view('admin.blooms_taxonomy.show', compact('bloomsTaxonomy'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BloomsTaxonomy $bloomsTaxonomy)
    {
        return view('admin.blooms_taxonomy.edit', compact('bloomsTaxonomy'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BloomsTaxonomy $bloomsTaxonomy)
    {
        $validator = Validator::make($request->all(), [
            'level_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $bloomsTaxonomy->update([
            'level_name' => $request->level_name,
            'description' => $request->description,
            'order' => $request->order ?? $bloomsTaxonomy->order,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.blooms-taxonomy.index')
            ->with('success', 'Bloom\'s Taxonomy level updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BloomsTaxonomy $bloomsTaxonomy)
    {
        // Check if the Bloom's Taxonomy level has questions
        if ($bloomsTaxonomy->questions()->count() > 0) {
            return redirect()->route('admin.blooms-taxonomy.index')
                ->with('error', 'Cannot delete Bloom\'s Taxonomy level because it has associated questions.');
        }

        $bloomsTaxonomy->delete();

        return redirect()->route('admin.blooms-taxonomy.index')
            ->with('success', 'Bloom\'s Taxonomy level deleted successfully.');
    }
}
