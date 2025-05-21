<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dashboard extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'layout',
        'is_default',
        'is_public',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'layout' => 'array',
        'is_default' => 'boolean',
        'is_public' => 'boolean',
    ];

    /**
     * Get the user that owns the dashboard.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the widgets for the dashboard.
     */
    public function widgets()
    {
        return $this->hasMany(DashboardWidget::class);
    }

    /**
     * Scope a query to only include default dashboards.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope a query to only include public dashboards.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope a query to only include dashboards created by a specific user.
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
     * Get the available dashboard layouts.
     *
     * @return array
     */
    public static function getLayouts()
    {
        return [
            'grid' => 'Grid Layout',
            'columns' => 'Columns Layout',
            'tabs' => 'Tabs Layout',
        ];
    }
}

