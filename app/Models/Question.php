<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'topic_id',
        'bloom_id',
        'question_text',
        'question_type',
        'options',
        'correct_answer',
        'marks',
        'difficulty_level',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'options' => 'json',
        'marks' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the topic that owns the question.
     */
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * Get the Bloom's taxonomy level that owns the question.
     */
    public function bloomsTaxonomy()
    {
        return $this->belongsTo(BloomsTaxonomy::class, 'bloom_id');
    }

    /**
     * Get the unit through the topic.
     */
    public function unit()
    {
        return $this->hasOneThrough(Unit::class, Topic::class, 'id', 'id', 'topic_id', 'unit_id');
    }

    /**
     * Get the subject through the topic and unit.
     */
    public function subject()
    {
        return $this->topic->unit->subject();
    }
}
