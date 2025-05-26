<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionPaper extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'subject_id',
        'blueprint_id',
        'total_marks',
        'duration',
        'exam_date',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_marks' => 'integer',
        'duration' => 'integer',
        'exam_date' => 'date',
    ];

    /**
     * Get the subject that owns the question paper.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the blueprint that was used to generate the question paper.
     */
    public function blueprint()
    {
        return $this->belongsTo(Blueprint::class);
    }

    /**
     * Get the questions included in the question paper.
     */
    public function questions()
    {
        return $this->belongsToMany(Question::class, 'question_paper_questions')
                    ->withPivot('section_number', 'question_number', 'marks', 'is_optional')
                    ->withTimestamps();
    }

    /**
     * Scope a query to only include draft question papers.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope a query to only include published question papers.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope a query to only include archived question papers.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    /**
     * Get the questions grouped by section.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getQuestionsBySection()
    {
        return $this->questions()
                    ->orderBy('pivot_section_number')
                    ->orderBy('pivot_question_number')
                    ->get()
                    ->groupBy('pivot.section_number');
    }

    /**
     * Calculate the total marks of the question paper.
     *
     * @return int
     */
    public function calculateTotalMarks()
    {
        return $this->questions()->sum('pivot_marks');
    }

    /**
     * Update the total marks based on the questions.
     *
     * @return void
     */
    public function updateTotalMarks()
    {
        $this->total_marks = $this->calculateTotalMarks();
        $this->save();
    }

    /**
     * Check if the question paper is in draft status.
     *
     * @return bool
     */
    public function isDraft()
    {
        return $this->status === 'draft';
    }

    /**
     * Check if the question paper is published.
     *
     * @return bool
     */
    public function isPublished()
    {
        return $this->status === 'published';
    }

    /**
     * Check if the question paper is archived.
     *
     * @return bool
     */
    public function isArchived()
    {
        return $this->status === 'archived';
    }

    /**
     * Publish the question paper.
     *
     * @return bool
     */
    public function publish()
    {
        $this->status = 'published';
        return $this->save();
    }

    /**
     * Archive the question paper.
     *
     * @return bool
     */
    public function archive()
    {
        $this->status = 'archived';
        return $this->save();
    }
}

