<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeatingPlanRule extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'seating_plan_id',
        'seating_rule_id',
        'parameters',
        'priority',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'parameters' => 'array',
        'priority' => 'integer',
    ];

    /**
     * Get the seating plan that owns the rule.
     */
    public function seatingPlan(): BelongsTo
    {
        return $this->belongsTo(SeatingPlan::class);
    }

    /**
     * Get the seating rule that owns the plan rule.
     */
    public function seatingRule(): BelongsTo
    {
        return $this->belongsTo(SeatingRule::class);
    }

    /**
     * Scope a query to filter plan rules by seating plan.
     */
    public function scopeInSeatingPlan($query, $seatingPlanId)
    {
        return $query->where('seating_plan_id', $seatingPlanId);
    }

    /**
     * Scope a query to order plan rules by priority.
     */
    public function scopeOrderByPriority($query, $direction = 'desc')
    {
        return $query->orderBy('priority', $direction);
    }

    /**
     * Get the rule name.
     */
    public function getRuleNameAttribute(): string
    {
        return $this->seatingRule->name;
    }

    /**
     * Get the rule type.
     */
    public function getRuleTypeAttribute(): string
    {
        return $this->seatingRule->type;
    }

    /**
     * Get the formatted rule type.
     */
    public function getFormattedRuleTypeAttribute(): string
    {
        return $this->seatingRule->formatted_type;
    }

    /**
     * Get the merged configuration (base rule + specific parameters).
     */
    public function getMergedConfigurationAttribute(): array
    {
        $baseConfig = $this->seatingRule->configuration ?? [];
        $specificParams = $this->parameters ?? [];
        
        return array_merge($baseConfig, $specificParams);
    }
}

