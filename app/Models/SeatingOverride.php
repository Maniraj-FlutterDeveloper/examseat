<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeatingOverride extends Model
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
        'reason',
        'created_by',
    ];

    /**
     * Get the seating plan that owns the override.
     */
    public function seatingPlan()
    {
        return $this->belongsTo(SeatingPlan::class);
    }

    /**
     * Get the student that owns the override.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the room that owns the override.
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the user that created the override.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

