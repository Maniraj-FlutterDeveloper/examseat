<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionType extends Model
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
        'structure' => 'array',
    ];

    /**
     * Get the questions of this type.
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Get the blueprint conditions that use this question type.
     */
    public function blueprintConditions()
    {
        return $this->hasMany(BlueprintCondition::class);
    }

    /**
     * Scope a query to only include active question types.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if this question type is Multiple Choice Question.
     *
     * @return bool
     */
    public function isMCQ()
    {
        return strtolower($this->name) === 'mcq' || 
               strtolower($this->name) === 'multiple choice' || 
               strtolower($this->name) === 'multiple choice question';
    }

    /**
     * Check if this question type is True/False.
     *
     * @return bool
     */
    public function isTrueFalse()
    {
        return strtolower($this->name) === 'true/false' || 
               strtolower($this->name) === 'true or false';
    }

    /**
     * Check if this question type is Short Answer.
     *
     * @return bool
     */
    public function isShortAnswer()
    {
        return strtolower($this->name) === 'short answer';
    }

    /**
     * Check if this question type is Long Answer.
     *
     * @return bool
     */
    public function isLongAnswer()
    {
        return strtolower($this->name) === 'long answer' || 
               strtolower($this->name) === 'essay';
    }
}

