<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'department',
        'duration',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'duration' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the students for the course.
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Scope a query to only include active courses.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter courses by department.
     */
    public function scopeInDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    /**
     * Get the count of students in this course.
     */
    public function getStudentsCountAttribute(): int
    {
        return $this->students()->count();
    }

    /**
     * Get the count of active students in this course.
     */
    public function getActiveStudentsCountAttribute(): int
    {
        return $this->students()->active()->count();
    }

    /**
     * Get students grouped by year.
     */
    public function getStudentsByYear(): array
    {
        return $this->students()
            ->select('year')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('year')
            ->orderBy('year')
            ->pluck('count', 'year')
            ->toArray();
    }

    /**
     * Get students grouped by section.
     */
    public function getStudentsBySection(): array
    {
        return $this->students()
            ->select('section')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('section')
            ->orderBy('section')
            ->pluck('count', 'section')
            ->toArray();
    }
}

