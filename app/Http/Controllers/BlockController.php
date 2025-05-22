<?php

namespace App\Http\Controllers;

use App\Models\Block;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BlockController extends Controller
{
    /**
     * Display a listing of the blocks.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $blocks = Block::with('rooms')->get();
        return view('seat-plan.blocks.index', compact('blocks'));
    }

    /**
     * Show the form for creating a new block.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('seat-plan.blocks.create');
    }

    /**
     * Store a newly created block in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:blocks',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Block::create($request->all());

        return redirect()->route('blocks.index')
            ->with('success', 'Block created successfully.');
    }

    /**
     * Display the specified block.
     *
     * @param  \App\Models\Block  $block
     * @return \Illuminate\Http\Response
     */
    public function show(Block $block)
    {
        $block->load('rooms');
        return view('seat-plan.blocks.show', compact('block'));
    }

    /**
     * Show the form for editing the specified block.
     *
     * @param  \App\Models\Block  $block
     * @return \Illuminate\Http\Response
     */
    public function edit(Block $block)
    {
        return view('seat-plan.blocks.edit', compact('block'));
    }

    /**
     * Update the specified block in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Block  $block
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Block $block)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:blocks,code,' . $block->id,
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $block->update($request->all());

        return redirect()->route('blocks.index')
            ->with('success', 'Block updated successfully.');
    }

    /**
     * Remove the specified block from storage.
     *
     * @param  \App\Models\Block  $block
     * @return \Illuminate\Http\Response
     */
    public function destroy(Block $block)
    {
        // Check if the block has rooms
        if ($block->rooms()->count() > 0) {
            return redirect()->route('blocks.index')
                ->with('error', 'Cannot delete block because it has associated rooms.');
        }

        $block->delete();

        return redirect()->route('blocks.index')
            ->with('success', 'Block deleted successfully.');
    }

    /**
     * Toggle the active status of the specified block.
     *
     * @param  \App\Models\Block  $block
     * @return \Illuminate\Http\Response
     */
    public function toggleActive(Block $block)
    {
        $block->is_active = !$block->is_active;
        $block->save();

        return redirect()->route('blocks.index')
            ->with('success', 'Block status updated successfully.');
    }
}

