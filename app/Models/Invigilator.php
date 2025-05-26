<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invigilator extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'department',
        'designation',
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
     * Get the invigilator assignments for the invigilator.
     */
    public function invigilatorAssignments(): HasMany
    {
        return $this->hasMany(InvigilatorAssignment::class);
    }

    /**
     * Scope a query to only include active invigilators.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter invigilators by department.
     */
    public function scopeInDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    /**
     * Get the full name with designation.
     */
    public function getFullNameWithDesignationAttribute(): string
    {
        return $this->name . ($this->designation ? " ({$this->designation})" : "");
    }

    /**
     * Get the count of assignments for this invigilator.
     */
    public function getAssignmentsCountAttribute(): int
    {
        return $this->invigilatorAssignments()->count();
    }

    /**
     * Get upcoming assignments for this invigilator.
     */
    public function getUpcomingAssignments($limit = 5)
    {
        return $this->invigilatorAssignments()
            ->with(['seatingPlan', 'room'])
            ->whereHas('seatingPlan', function ($query) {
                $query->where('exam_date', '>=', now()->format('Y-m-d'))
                      ->whereIn('status', ['draft', 'published']);
            })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}

