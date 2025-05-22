<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SeatingRule extends Model
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
        'configuration',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'configuration' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the seating plan rules for the seating rule.
     */
    public function seatingPlanRules(): HasMany
    {
        return $this->hasMany(SeatingPlanRule::class);
    }

    /**
     * Scope a query to only include active seating rules.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter seating rules by type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get the formatted type.
     */
    public function getFormattedTypeAttribute(): string
    {
        return match($this->type) {
            'alternate_seating' => 'Alternate Seating',
            'mixed_branches' => 'Mixed Branches',
            'special_needs' => 'Special Needs',
            'custom' => 'Custom',
            default => ucfirst($this->type),
        };
    }

    /**
     * Get the count of seating plans using this rule.
     */
    public function getSeatingPlansCountAttribute(): int
    {
        return $this->seatingPlanRules()->count();
    }

    /**
     * Check if the rule is an alternate seating rule.
     */
    public function isAlternateSeating(): bool
    {
        return $this->type === 'alternate_seating';
    }

    /**
     * Check if the rule is a mixed branches rule.
     */
    public function isMixedBranches(): bool
    {
        return $this->type === 'mixed_branches';
    }

    /**
     * Check if the rule is a special needs rule.
     */
    public function isSpecialNeeds(): bool
    {
        return $this->type === 'special_needs';
    }

    /**
     * Check if the rule is a custom rule.
     */
    public function isCustom(): bool
    {
        return $this->type === 'custom';
    }
}

