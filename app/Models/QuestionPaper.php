<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionPaper extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'subject_id',
        'blueprint_id',
        'title',
        'description',
        'questions',
        'total_marks',
        'duration_minutes',
        'exam_date',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'questions' => 'json',
        'total_marks' => 'decimal:2',
        'duration_minutes' => 'integer',
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
     * Get the blueprint that owns the question paper.
     */
    public function blueprint()
    {
        return $this->belongsTo(Blueprint::class);
    }
}
