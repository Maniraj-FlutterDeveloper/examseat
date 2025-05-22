<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Block;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    /**
     * Display a listing of the rooms.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rooms = Room::with('block')->get();
        return view('seat-plan.rooms.index', compact('rooms'));
    }

    /**
     * Show the form for creating a new room.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $blocks = Block::active()->get();
        return view('seat-plan.rooms.create', compact('blocks'));
    }

    /**
     * Store a newly created room in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'block_id' => 'required|exists:blocks,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:rooms',
            'capacity' => 'required|integer|min:1',
            'rows' => 'nullable|integer|min:1',
            'columns' => 'nullable|integer|min:1',
            'description' => 'nullable|string',
            'floor' => 'nullable|string|max:50',
            'has_projector' => 'boolean',
            'has_computer' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Room::create($request->all());

        return redirect()->route('rooms.index')
            ->with('success', 'Room created successfully.');
    }

    /**
     * Display the specified room.
     *
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function show(Room $room)
    {
        $room->load('block');
        return view('seat-plan.rooms.show', compact('room'));
    }

    /**
     * Show the form for editing the specified room.
     *
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function edit(Room $room)
    {
        $blocks = Block::active()->get();
        return view('seat-plan.rooms.edit', compact('room', 'blocks'));
    }

    /**
     * Update the specified room in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Room $room)
    {
        $validator = Validator::make($request->all(), [
            'block_id' => 'required|exists:blocks,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:rooms,code,' . $room->id,
            'capacity' => 'required|integer|min:1',
            'rows' => 'nullable|integer|min:1',
            'columns' => 'nullable|integer|min:1',
            'description' => 'nullable|string',
            'floor' => 'nullable|string|max:50',
            'has_projector' => 'boolean',
            'has_computer' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $room->update($request->all());

        return redirect()->route('rooms.index')
            ->with('success', 'Room updated successfully.');
    }

    /**
     * Remove the specified room from storage.
     *
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function destroy(Room $room)
    {
        // Check if the room has seating assignments
        if ($room->seatingAssignments()->count() > 0) {
            return redirect()->route('rooms.index')
                ->with('error', 'Cannot delete room because it has associated seating assignments.');
        }

        $room->delete();

        return redirect()->route('rooms.index')
            ->with('success', 'Room deleted successfully.');
    }

    /**
     * Toggle the active status of the specified room.
     *
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function toggleActive(Room $room)
    {
        $room->is_active = !$room->is_active;
        $room->save();

        return redirect()->route('rooms.index')
            ->with('success', 'Room status updated successfully.');
    }

    /**
     * Display rooms by block.
     *
     * @param  \App\Models\Block  $block
     * @return \Illuminate\Http\Response
     */
    public function roomsByBlock(Block $block)
    {
        $rooms = Room::where('block_id', $block->id)->get();
        return response()->json($rooms);
    }

    /**
     * Display the room layout.
     *
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function layout(Room $room)
    {
        return view('seat-plan.rooms.layout', compact('room'));
    }
}

