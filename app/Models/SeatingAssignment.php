<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeatingAssignment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'seating_plan_id',
        'student_id',
        'room_id',
        'seat_number',
        'is_override',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_override' => 'boolean',
    ];

    /**
     * Get the seating plan that owns the assignment.
     */
    public function seatingPlan()
    {
        return $this->belongsTo(SeatingPlan::class);
    }

    /**
     * Get the student that owns the assignment.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the room that owns the assignment.
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}

