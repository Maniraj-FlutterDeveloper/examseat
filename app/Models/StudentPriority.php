<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentPriority extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'priority_level',
        'reason',
        'notes',
        'valid_until',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'valid_until' => 'date',
    ];

    /**
     * Get the student that owns the priority.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Check if the priority is still valid.
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->valid_until === null || $this->valid_until->isFuture();
    }
}

