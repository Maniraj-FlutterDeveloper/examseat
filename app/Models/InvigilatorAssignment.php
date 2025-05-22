<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvigilatorAssignment extends Model
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
        'invigilator_id',
        'role',
        'notes',
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
     * Get the invigilator that owns the assignment.
     */
    public function invigilator(): BelongsTo
    {
        return $this->belongsTo(Invigilator::class);
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
     * Scope a query to filter assignments by role.
     */
    public function scopeWithRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Get the invigilator name.
     */
    public function getInvigilatorNameAttribute(): string
    {
        return $this->invigilator->name;
    }

    /**
     * Get the room name with block.
     */
    public function getRoomNameWithBlockAttribute(): string
    {
        return $this->room->block->name . ' - ' . $this->room->name;
    }

    /**
     * Get the formatted role.
     */
    public function getFormattedRoleAttribute(): string
    {
        return ucfirst($this->role);
    }

    /**
     * Get the count of students in the assigned room for this seating plan.
     */
    public function getStudentsCountAttribute(): int
    {
        return SeatingAssignment::where('seating_plan_id', $this->seating_plan_id)
            ->where('room_id', $this->room_id)
            ->count();
    }
}

