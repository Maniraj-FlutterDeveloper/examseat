<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SeatingPlan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'exam_date',
        'start_time',
        'end_time',
        'status',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'exam_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    /**
     * Get the seating assignments for the seating plan.
     */
    public function seatingAssignments(): HasMany
    {
        return $this->hasMany(SeatingAssignment::class);
    }

    /**
     * Get the invigilator assignments for the seating plan.
     */
    public function invigilatorAssignments(): HasMany
    {
        return $this->hasMany(InvigilatorAssignment::class);
    }

    /**
     * Get the rules applied to this seating plan.
     */
    public function seatingPlanRules(): HasMany
    {
        return $this->hasMany(SeatingPlanRule::class);
    }

    /**
     * Scope a query to filter seating plans by status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter seating plans by date range.
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('exam_date', [$startDate, $endDate]);
    }

    /**
     * Get the formatted exam date and time.
     */
    public function getFormattedExamDateTimeAttribute(): string
    {
        return $this->exam_date->format('M d, Y') . ' (' . 
               $this->start_time->format('h:i A') . ' - ' . 
               $this->end_time->format('h:i A') . ')';
    }

    /**
     * Get the duration of the exam in minutes.
     */
    public function getDurationInMinutesAttribute(): int
    {
        return $this->start_time->diffInMinutes($this->end_time);
    }

    /**
     * Get the count of students assigned to this seating plan.
     */
    public function getStudentsCountAttribute(): int
    {
        return $this->seatingAssignments()->count();
    }

    /**
     * Get the count of rooms used in this seating plan.
     */
    public function getRoomsCountAttribute(): int
    {
        return $this->seatingAssignments()->distinct('room_id')->count('room_id');
    }

    /**
     * Get the count of invigilators assigned to this seating plan.
     */
    public function getInvigilatorsCountAttribute(): int
    {
        return $this->invigilatorAssignments()->count();
    }

    /**
     * Check if the seating plan is editable.
     */
    public function isEditable(): bool
    {
        return in_array($this->status, ['draft', 'published']);
    }

    /**
     * Check if the seating plan is published.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Check if the seating plan is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the seating plan is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }
}

