<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
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
     * Get the units for the subject.
     */
    public function units()
    {
        return $this->hasMany(Unit::class);
    }

    /**
     * Get all topics for the subject through units.
     */
    public function topics()
    {
        return $this->hasManyThrough(Topic::class, Unit::class);
    }

    /**
     * Get all questions for the subject through units and topics.
     */
    public function questions()
    {
        return $this->hasManyThrough(
            Question::class,
            Unit::class,
            'subject_id', // Foreign key on units table...
            'topic_id', // Foreign key on questions table...
            'id', // Local key on subjects table...
            'id' // Local key on units table...
        );
    }

    /**
     * Get the blueprints for the subject.
     */
    public function blueprints()
    {
        return $this->hasMany(Blueprint::class);
    }

    /**
     * Get the question papers for the subject.
     */
    public function questionPapers()
    {
        return $this->hasMany(QuestionPaper::class);
    }

    /**
     * Scope a query to only include active subjects.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

