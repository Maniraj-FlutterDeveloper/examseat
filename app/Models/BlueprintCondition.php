<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlueprintCondition extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'blueprint_id',
        'condition_type',
        'reference_id',
        'question_count',
        'marks_per_question',
        'question_type_id',
        'blooms_taxonomy_id',
        'difficulty_level',
        'additional_criteria',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'question_count' => 'integer',
        'marks_per_question' => 'integer',
        'difficulty_level' => 'integer',
        'additional_criteria' => 'array',
    ];

    /**
     * Get the blueprint that owns the condition.
     */
    public function blueprint()
    {
        return $this->belongsTo(Blueprint::class);
    }

    /**
     * Get the question type associated with the condition.
     */
    public function questionType()
    {
        return $this->belongsTo(QuestionType::class);
    }

    /**
     * Get the Bloom's Taxonomy level associated with the condition.
     */
    public function bloomsTaxonomy()
    {
        return $this->belongsTo(BloomsTaxonomy::class, 'blooms_taxonomy_id');
    }

    /**
     * Get the referenced entity based on condition type.
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getReference()
    {
        if (!$this->reference_id) {
            return null;
        }

        switch ($this->condition_type) {
            case 'unit':
                return Unit::find($this->reference_id);
            case 'topic':
                return Topic::find($this->reference_id);
            default:
                return null;
        }
    }

    /**
     * Calculate the total marks for this condition.
     *
     * @return int
     */
    public function getTotalMarksAttribute()
    {
        return $this->question_count * $this->marks_per_question;
    }

    /**
     * Get questions that match this condition.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getMatchingQuestions()
    {
        $query = Question::query()->active();

        // Apply condition type filter
        switch ($this->condition_type) {
            case 'unit':
                $query->whereHas('topic', function ($q) {
                    $q->where('unit_id', $this->reference_id);
                });
                break;
            case 'topic':
                $query->where('topic_id', $this->reference_id);
                break;
            case 'subject':
                $subjectId = $this->blueprint->subject_id;
                $query->whereHas('topic.unit', function ($q) use ($subjectId) {
                    $q->where('subject_id', $subjectId);
                });
                break;
        }

        // Apply question type filter
        if ($this->question_type_id) {
            $query->where('question_type_id', $this->question_type_id);
        }

        // Apply Bloom's Taxonomy filter
        if ($this->blooms_taxonomy_id) {
            $query->where('blooms_taxonomy_id', $this->blooms_taxonomy_id);
        }

        // Apply difficulty level filter
        if ($this->difficulty_level) {
            $query->where('difficulty_level', $this->difficulty_level);
        }

        // Apply marks filter
        if ($this->marks_per_question) {
            $query->where('marks', $this->marks_per_question);
        }

        // Apply additional criteria
        if ($this->additional_criteria) {
            foreach ($this->additional_criteria as $key => $value) {
                if ($key === 'metadata') {
                    // Handle metadata JSON field
                    foreach ($value as $metaKey => $metaValue) {
                        $query->whereJsonContains("metadata->{$metaKey}", $metaValue);
                    }
                } else {
                    $query->where($key, $value);
                }
            }
        }

        return $query->get();
    }
}

