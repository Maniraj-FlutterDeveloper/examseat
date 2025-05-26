<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'topic_id',
        'question_type_id',
        'blooms_taxonomy_id',
        'question_text',
        'options',
        'answer',
        'solution',
        'difficulty_level',
        'marks',
        'estimated_time',
        'metadata',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'options' => 'array',
        'metadata' => 'array',
        'difficulty_level' => 'integer',
        'marks' => 'integer',
        'estimated_time' => 'integer',
    ];

    /**
     * Get the topic that owns the question.
     */
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * Get the question type that owns the question.
     */
    public function questionType()
    {
        return $this->belongsTo(QuestionType::class);
    }

    /**
     * Get the Bloom's Taxonomy level that owns the question.
     */
    public function bloomsTaxonomy()
    {
        return $this->belongsTo(BloomsTaxonomy::class, 'blooms_taxonomy_id');
    }

    /**
     * Get the unit through the topic.
     */
    public function unit()
    {
        return $this->hasOneThrough(
            Unit::class,
            Topic::class,
            'id', // Foreign key on topics table...
            'id', // Foreign key on units table...
            'topic_id', // Local key on questions table...
            'unit_id' // Local key on topics table...
        );
    }

    /**
     * Get the subject through the topic and unit.
     */
    public function subject()
    {
        return $this->topic->unit->subject();
    }

    /**
     * Get the question papers that include this question.
     */
    public function questionPapers()
    {
        return $this->belongsToMany(QuestionPaper::class, 'question_paper_questions')
                    ->withPivot('section_number', 'question_number', 'marks', 'is_optional')
                    ->withTimestamps();
    }

    /**
     * Scope a query to only include active questions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter questions by difficulty level.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $level
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDifficulty($query, $level)
    {
        return $query->where('difficulty_level', $level);
    }

    /**
     * Scope a query to filter questions by Bloom's Taxonomy level.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $bloomsId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBloomsTaxonomy($query, $bloomsId)
    {
        return $query->where('blooms_taxonomy_id', $bloomsId);
    }

    /**
     * Scope a query to filter questions by marks.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $marks
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMarks($query, $marks)
    {
        return $query->where('marks', $marks);
    }

    /**
     * Check if this question is a Multiple Choice Question.
     *
     * @return bool
     */
    public function isMCQ()
    {
        return $this->questionType && $this->questionType->isMCQ();
    }

    /**
     * Check if this question is a True/False question.
     *
     * @return bool
     */
    public function isTrueFalse()
    {
        return $this->questionType && $this->questionType->isTrueFalse();
    }

    /**
     * Check if this question is a Short Answer question.
     *
     * @return bool
     */
    public function isShortAnswer()
    {
        return $this->questionType && $this->questionType->isShortAnswer();
    }

    /**
     * Check if this question is a Long Answer question.
     *
     * @return bool
     */
    public function isLongAnswer()
    {
        return $this->questionType && $this->questionType->isLongAnswer();
    }
}

