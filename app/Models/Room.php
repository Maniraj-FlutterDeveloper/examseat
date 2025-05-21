<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'block_id',
        'room_number',
        'capacity',
        'is_accessible',
        'layout',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'capacity' => 'integer',
        'is_accessible' => 'boolean',
        'layout' => 'json',
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
     * Get the seating assignments for the room.
     */
    public function seatingAssignments()
    {
        return $this->hasMany(SeatingAssignment::class);
    }

    /**
     * Get the seating overrides for the room.
     */
    public function seatingOverrides()
    {
        return $this->hasMany(SeatingOverride::class);
    }
}

