<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'course_id',
        'roll_number',
        'name',
        'email',
        'phone',
        'gender',
        'year',
        'section',
        'has_disability',
        'disability_details',
        'is_active',
        'password',
        'role_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'year' => 'integer',
        'has_disability' => 'boolean',
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the course that owns the student.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the role that owns the student.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the seating assignments for the student.
     */
    public function seatingAssignments(): HasMany
    {
        return $this->hasMany(SeatingAssignment::class);
    }

    /**
     * Scope a query to only include active students.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter students by course.
     */
    public function scopeInCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    /**
     * Scope a query to filter students by year.
     */
    public function scopeInYear($query, $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Scope a query to filter students by section.
     */
    public function scopeInSection($query, $section)
    {
        return $query->where('section', $section);
    }

    /**
     * Scope a query to filter students with disabilities.
     */
    public function scopeWithDisability($query)
    {
        return $query->where('has_disability', true);
    }

    /**
     * Get the full name with roll number.
     */
    public function getFullNameWithRollAttribute(): string
    {
        return "{$this->name} ({$this->roll_number})";
    }

    /**
     * Get the course name.
     */
    public function getCourseNameAttribute(): string
    {
        return $this->course->name;
    }

    /**
     * Get the year and section combined.
     */
    public function getYearSectionAttribute(): string
    {
        return "Year {$this->year}" . ($this->section ? " - Section {$this->section}" : "");
    }

    /**
     * Determine if the student has the given permission.
     *
     * @param string|Permission $permission
     * @return bool
     */
    public function hasPermission($permission): bool
    {
        if (!$this->role) {
            return false;
        }
        
        return $this->role->hasPermission($permission);
    }

    /**
     * Determine if the student has all of the given permissions.
     *
     * @param array $permissions
     * @return bool
     */
    public function hasAllPermissions(array $permissions): bool
    {
        if (!$this->role) {
            return false;
        }
        
        return $this->role->hasAllPermissions($permissions);
    }

    /**
     * Determine if the student has any of the given permissions.
     *
     * @param array $permissions
     * @return bool
     */
    public function hasAnyPermissions(array $permissions): bool
    {
        if (!$this->role) {
            return false;
        }
        
        return $this->role->hasAnyPermissions($permissions);
    }

    /**
     * Check if the student is active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }
}
