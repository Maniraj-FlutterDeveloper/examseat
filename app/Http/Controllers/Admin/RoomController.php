<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Block;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    /**
     * Display a listing of the rooms.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $rooms = Room::with('block')->orderBy('room_number')->paginate(10);
        return view('admin.rooms.index', compact('rooms'));
    }

    /**
     * Show the form for creating a new room.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $blocks = Block::orderBy('block_name')->pluck('block_name', 'id');
        return view('admin.rooms.create', compact('blocks'));
    }

    /**
     * Store a newly created room in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_number' => 'required|string|max:20',
            'block_id' => 'required|exists:blocks,id',
            'capacity' => 'required|integer|min:1',
            'rows' => 'required|integer|min:1',
            'columns' => 'required|integer|min:1',
            'description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if room number already exists in the same block
        $existingRoom = Room::where('room_number', $request->room_number)
            ->where('block_id', $request->block_id)
            ->first();
            
        if ($existingRoom) {
            return redirect()->back()
                ->withErrors(['room_number' => 'This room number already exists in the selected block.'])
                ->withInput();
        }

        Room::create([
            'room_number' => $request->room_number,
            'block_id' => $request->block_id,
            'capacity' => $request->capacity,
            'rows' => $request->rows,
            'columns' => $request->columns,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Room created successfully.');
    }

    /**
     * Display the specified room.
     *
     * @param  \App\Models\Room  $room
     * @return \Illuminate\View\View
     */
    public function show(Room $room)
    {
        return view('admin.rooms.show', compact('room'));
    }

    /**
     * Show the form for editing the specified room.
     *
     * @param  \App\Models\Room  $room
     * @return \Illuminate\View\View
     */
    public function edit(Room $room)
    {
        $blocks = Block::orderBy('block_name')->pluck('block_name', 'id');
        return view('admin.rooms.edit', compact('room', 'blocks'));
    }

    /**
     * Update the specified room in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Room $room)
    {
        $validator = Validator::make($request->all(), [
            'room_number' => 'required|string|max:20',
            'block_id' => 'required|exists:blocks,id',
            'capacity' => 'required|integer|min:1',
            'rows' => 'required|integer|min:1',
            'columns' => 'required|integer|min:1',
            'description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if room number already exists in the same block (excluding current room)
        $existingRoom = Room::where('room_number', $request->room_number)
            ->where('block_id', $request->block_id)
            ->where('id', '!=', $room->id)
            ->first();
            
        if ($existingRoom) {
            return redirect()->back()
                ->withErrors(['room_number' => 'This room number already exists in the selected block.'])
                ->withInput();
        }

        $room->update([
            'room_number' => $request->room_number,
            'block_id' => $request->block_id,
            'capacity' => $request->capacity,
            'rows' => $request->rows,
            'columns' => $request->columns,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Room updated successfully.');
    }

    /**
     * Remove the specified room from storage.
     *
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Room $room)
    {
        // Check if the room is used in any seating plans
        if ($room->seatingPlans()->count() > 0) {
            return redirect()->route('admin.rooms.index')
                ->with('error', 'Cannot delete room because it is used in seating plans.');
        }

        $room->delete();

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Room deleted successfully.');
    }
}
