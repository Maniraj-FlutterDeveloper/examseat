<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BloomsTaxonomy extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'blooms_taxonomy';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'level',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'level' => 'integer',
    ];

    /**
     * Get the questions that use this Bloom's Taxonomy level.
     */
    public function questions()
    {
        return $this->hasMany(Question::class, 'blooms_taxonomy_id');
    }

    /**
     * Get the blueprint conditions that use this Bloom's Taxonomy level.
     */
    public function blueprintConditions()
    {
        return $this->hasMany(BlueprintCondition::class, 'blooms_taxonomy_id');
    }

    /**
     * Scope a query to only include active Bloom's Taxonomy levels.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order Bloom's Taxonomy levels by their level field.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('level');
    }
}

