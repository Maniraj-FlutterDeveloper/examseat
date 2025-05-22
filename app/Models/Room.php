<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'block_id',
        'name',
        'code',
        'capacity',
        'rows',
        'columns',
        'description',
        'floor',
        'has_projector',
        'has_computer',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'capacity' => 'integer',
        'rows' => 'integer',
        'columns' => 'integer',
        'has_projector' => 'boolean',
        'has_computer' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the block that owns the room.
     */
    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class);
    }

    /**
     * Get the seating assignments for the room.
     */
    public function seatingAssignments(): HasMany
    {
        return $this->hasMany(SeatingAssignment::class);
    }

    /**
     * Get the invigilator assignments for the room.
     */
    public function invigilatorAssignments(): HasMany
    {
        return $this->hasMany(InvigilatorAssignment::class);
    }

    /**
     * Scope a query to only include active rooms.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter rooms by block.
     */
    public function scopeInBlock($query, $blockId)
    {
        return $query->where('block_id', $blockId);
    }

    /**
     * Scope a query to filter rooms by minimum capacity.
     */
    public function scopeWithMinCapacity($query, $capacity)
    {
        return $query->where('capacity', '>=', $capacity);
    }

    /**
     * Get the full location of the room (Block + Room).
     */
    public function getFullLocationAttribute(): string
    {
        return "{$this->block->name} - {$this->name}";
    }

    /**
     * Check if the room has a grid layout defined.
     */
    public function hasGridLayout(): bool
    {
        return $this->rows > 0 && $this->columns > 0;
    }

    /**
     * Get the grid capacity (rows * columns) if a grid layout is defined.
     */
    public function getGridCapacityAttribute(): ?int
    {
        return $this->hasGridLayout() ? $this->rows * $this->columns : null;
    }
}

