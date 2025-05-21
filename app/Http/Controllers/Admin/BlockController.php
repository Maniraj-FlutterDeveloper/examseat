<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Block;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BlockController extends Controller
{
    /**
     * Display a listing of the blocks.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $blocks = Block::orderBy('block_name')->paginate(10);
        return view('admin.blocks.index', compact('blocks'));
    }

    /**
     * Show the form for creating a new block.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.blocks.create');
    }

    /**
     * Store a newly created block in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'block_name' => 'required|string|max:50|unique:blocks',
            'description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Block::create([
            'block_name' => $request->block_name,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.blocks.index')
            ->with('success', 'Block created successfully.');
    }

    /**
     * Display the specified block.
     *
     * @param  \App\Models\Block  $block
     * @return \Illuminate\View\View
     */
    public function show(Block $block)
    {
        return view('admin.blocks.show', compact('block'));
    }

    /**
     * Show the form for editing the specified block.
     *
     * @param  \App\Models\Block  $block
     * @return \Illuminate\View\View
     */
    public function edit(Block $block)
    {
        return view('admin.blocks.edit', compact('block'));
    }

    /**
     * Update the specified block in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Block  $block
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Block $block)
    {
        $validator = Validator::make($request->all(), [
            'block_name' => 'required|string|max:50|unique:blocks,block_name,' . $block->id,
            'description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $block->update([
            'block_name' => $request->block_name,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.blocks.index')
            ->with('success', 'Block updated successfully.');
    }

    /**
     * Remove the specified block from storage.
     *
     * @param  \App\Models\Block  $block
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Block $block)
    {
        // Check if the block has any rooms
        if ($block->rooms()->count() > 0) {
            return redirect()->route('admin.blocks.index')
                ->with('error', 'Cannot delete block because it has rooms assigned to it.');
        }

        $block->delete();

        return redirect()->route('admin.blocks.index')
            ->with('success', 'Block deleted successfully.');
    }
}
