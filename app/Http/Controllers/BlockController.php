<?php

namespace App\Http\Controllers;

use App\Models\Block;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BlockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $blocks = Block::withCount('rooms')->paginate(10);
        return view('blocks.index', compact('blocks'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('blocks.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'block_name' => 'required|string|max:255|unique:blocks',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->route('blocks.create')
                ->withErrors($validator)
                ->withInput();
        }

        Block::create($request->all());

        return redirect()->route('blocks.index')
            ->with('success', 'Block created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Block  $block
     * @return \Illuminate\View\View
     */
    public function show(Block $block)
    {
        $block->load('rooms');
        return view('blocks.show', compact('block'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Block  $block
     * @return \Illuminate\View\View
     */
    public function edit(Block $block)
    {
        return view('blocks.edit', compact('block'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Block  $block
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Block $block)
    {
        $validator = Validator::make($request->all(), [
            'block_name' => 'required|string|max:255|unique:blocks,block_name,' . $block->id,
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->route('blocks.edit', $block->id)
                ->withErrors($validator)
                ->withInput();
        }

        $block->update($request->all());

        return redirect()->route('blocks.index')
            ->with('success', 'Block updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Block  $block
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Block $block)
    {
        // Check if block has rooms
        if ($block->rooms()->count() > 0) {
            return redirect()->route('blocks.index')
                ->with('error', 'Block cannot be deleted because it has rooms associated with it.');
        }

        $block->delete();

        return redirect()->route('blocks.index')
            ->with('success', 'Block deleted successfully.');
    }
}
