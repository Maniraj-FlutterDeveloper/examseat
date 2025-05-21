<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SeatingPlan extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'room_id',
        'exam_name',
        'exam_date',
        'start_time',
        'end_time',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'exam_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    /**
     * Get the room that owns the seating plan.
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the assignments for the seating plan.
     */
    public function assignments()
    {
        return $this->hasMany(SeatingAssignment::class);
    }

    /**
     * Get the overrides for the seating plan.
     */
    public function overrides()
    {
        return $this->hasMany(SeatingOverride::class);
    }
}

