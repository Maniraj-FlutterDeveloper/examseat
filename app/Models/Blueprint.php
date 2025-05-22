<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blueprint extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'subject_id',
        'total_marks',
        'duration',
        'structure',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'total_marks' => 'integer',
        'duration' => 'integer',
        'structure' => 'array',
    ];

    /**
     * Get the subject that owns the blueprint.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the conditions for the blueprint.
     */
    public function conditions()
    {
        return $this->hasMany(BlueprintCondition::class);
    }

    /**
     * Get the question papers that use this blueprint.
     */
    public function questionPapers()
    {
        return $this->hasMany(QuestionPaper::class);
    }

    /**
     * Scope a query to only include active blueprints.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Calculate the total number of questions in the blueprint.
     *
     * @return int
     */
    public function getTotalQuestionsAttribute()
    {
        return $this->conditions->sum('question_count');
    }

    /**
     * Calculate the total marks in the blueprint.
     *
     * @return int
     */
    public function calculateTotalMarks()
    {
        return $this->conditions->sum(function ($condition) {
            return $condition->question_count * $condition->marks_per_question;
        });
    }

    /**
     * Update the total marks based on conditions.
     *
     * @return void
     */
    public function updateTotalMarks()
    {
        $this->total_marks = $this->calculateTotalMarks();
        $this->save();
    }
}

