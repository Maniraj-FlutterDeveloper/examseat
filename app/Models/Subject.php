<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'subject_name',
        'subject_code',
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
}
