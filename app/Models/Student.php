<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'roll_number',
        'name',
        'course_id',
        'year',
        'section',
        'has_disability',
        'disability_details',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'has_disability' => 'boolean',
    ];

    /**
     * Get the course that owns the student.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the priorities for the student.
     */
    public function priorities()
    {
        return $this->hasMany(StudentPriority::class);
    }

    /**
     * Get the seating assignments for the student.
     */
    public function seatingAssignments()
    {
        return $this->hasMany(SeatingAssignment::class);
    }

    /**
     * Get the seating overrides for the student.
     */
    public function seatingOverrides()
    {
        return $this->hasMany(SeatingOverride::class);
    }
}

