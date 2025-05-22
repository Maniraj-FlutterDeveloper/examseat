<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'room_id',
        'student_id',
        'seat_number',
        'row_number',
        'column_number',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'seat_number' => 'integer',
        'row_number' => 'integer',
        'column_number' => 'integer',
    ];

    /**
     * Get the seating plan that owns the assignment.
     */
    public function seatingPlan(): BelongsTo
    {
        return $this->belongsTo(SeatingPlan::class);
    }

    /**
     * Get the room that owns the assignment.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the student that owns the assignment.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Scope a query to filter assignments by seating plan.
     */
    public function scopeInSeatingPlan($query, $seatingPlanId)
    {
        return $query->where('seating_plan_id', $seatingPlanId);
    }

    /**
     * Scope a query to filter assignments by room.
     */
    public function scopeInRoom($query, $roomId)
    {
        return $query->where('room_id', $roomId);
    }

    /**
     * Get the seat position as a string (e.g., "Row 3, Seat 5" or "A5").
     */
    public function getSeatPositionAttribute(): string
    {
        if ($this->row_number && $this->column_number) {
            // If we have row and column numbers, use a grid-based position
            $rowLabel = chr(64 + $this->row_number); // Convert to letter (1=A, 2=B, etc.)
            return "{$rowLabel}{$this->column_number}";
        }
        
        // Otherwise, just use the seat number
        return "Seat {$this->seat_number}";
    }

    /**
     * Get the student name with roll number.
     */
    public function getStudentNameWithRollAttribute(): string
    {
        return $this->student->name . ' (' . $this->student->roll_number . ')';
    }

    /**
     * Get the room name with block.
     */
    public function getRoomNameWithBlockAttribute(): string
    {
        return $this->room->block->name . ' - ' . $this->room->name;
    }
}

