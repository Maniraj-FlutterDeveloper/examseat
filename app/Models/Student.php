<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

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
        'year',
        'section',
        'has_disability',
        'disability_details',
        'is_active',
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
    ];

    /**
     * Get the course that owns the student.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the seating plans for the student.
     */
    public function seatingPlans()
    {
        return $this->hasMany(SeatingPlan::class);
    }
}
