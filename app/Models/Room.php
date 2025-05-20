<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'block_id',
        'room_number',
        'capacity',
        'description',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the block that owns the room.
     */
    public function block()
    {
        return $this->belongsTo(Block::class);
    }

    /**
     * Get the seating plans for the room.
     */
    public function seatingPlans()
    {
        return $this->hasMany(SeatingPlan::class);
    }

    /**
     * Get the invigilator assignments for the room.
     */
    public function invigilatorAssignments()
    {
        return $this->hasMany(RoomInvigilatorAssignment::class);
    }
}
