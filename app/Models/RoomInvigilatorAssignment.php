<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomInvigilatorAssignment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'room_id',
        'invigilator_id',
        'exam_name',
        'exam_date',
        'start_time',
        'end_time',
        'status',
        'remarks',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'exam_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    /**
     * Get the room that owns the assignment.
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the invigilator that owns the assignment.
     */
    public function invigilator()
    {
        return $this->belongsTo(Invigilator::class);
    }
}
