<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DashboardWidget extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'dashboard_id',
        'type',
        'title',
        'size',
        'position',
        'config',
        'refresh_interval',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'position' => 'array',
        'config' => 'array',
        'refresh_interval' => 'integer',
    ];

    /**
     * Get the dashboard that owns the widget.
     */
    public function dashboard()
    {
        return $this->belongsTo(Dashboard::class);
    }

    /**
     * Scope a query to only include widgets of a specific type.
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
     * Get the available widget types.
     *
     * @return array
     */
    public static function getTypes()
    {
        return [
            'chart' => 'Chart',
            'table' => 'Table',
            'metric' => 'Metric',
            'list' => 'List',
            'map' => 'Map',
            'custom' => 'Custom',
        ];
    }

    /**
     * Get the available widget sizes.
     *
     * @return array
     */
    public static function getSizes()
    {
        return [
            'small' => 'Small',
            'medium' => 'Medium',
            'large' => 'Large',
            'full' => 'Full Width',
        ];
    }

    /**
     * Get the available chart types.
     *
     * @return array
     */
    public static function getChartTypes()
    {
        return [
            'bar' => 'Bar Chart',
            'line' => 'Line Chart',
            'pie' => 'Pie Chart',
            'doughnut' => 'Doughnut Chart',
            'radar' => 'Radar Chart',
            'polar' => 'Polar Area Chart',
            'bubble' => 'Bubble Chart',
            'scatter' => 'Scatter Chart',
        ];
    }
}

