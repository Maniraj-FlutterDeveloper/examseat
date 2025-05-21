<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
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
        'type',
        'user_id',
        'parameters',
        'last_generated_at',
        'schedule',
        'is_favorite',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'parameters' => 'array',
        'last_generated_at' => 'datetime',
        'schedule' => 'array',
        'is_favorite' => 'boolean',
    ];

    /**
     * Get the user that owns the report.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the report results for the report.
     */
    public function results()
    {
        return $this->hasMany(ReportResult::class);
    }

    /**
     * Get the latest result for the report.
     */
    public function latestResult()
    {
        return $this->hasOne(ReportResult::class)->latest();
    }

    /**
     * Scope a query to only include reports of a specific type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include favorite reports.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFavorites($query)
    {
        return $query->where('is_favorite', true);
    }

    /**
     * Scope a query to only include reports created by a specific user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get the available report types.
     *
     * @return array
     */
    public static function getTypes()
    {
        return [
            'exam_statistics' => 'Exam Statistics',
            'seating_plan' => 'Seating Plan Analytics',
            'question_paper' => 'Question Paper Usage',
            'student_performance' => 'Student Performance',
            'custom' => 'Custom Report',
        ];
    }
}

