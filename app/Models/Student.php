<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'roll_number',
        'email',
        'phone',
        'course_id',
        'year',
        'section',
        'password',
        'profile_picture',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the course that the student belongs to.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the seating plans for the student.
     */
    public function seatingPlans()
    {
        return $this->belongsToMany(SeatingPlan::class, 'seating_plan_student')
            ->withPivot('room_id', 'seat_number')
            ->withTimestamps();
    }

    /**
     * Get the notifications for the student.
     */
    public function notifications()
    {
        return $this->morphMany(Notification::class, 'recipient');
    }

    /**
     * Get the unread notifications for the student.
     */
    public function unreadNotifications()
    {
        return $this->notifications()->whereNull('read_at');
    }

    /**
     * Get the profile picture URL.
     *
     * @return string
     */
    public function getProfilePictureUrlAttribute()
    {
        if ($this->profile_picture) {
            return asset('storage/' . $this->profile_picture);
        }

        return asset('images/default-profile.png');
    }

    /**
     * Check if the student is active.
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->status === 'active';
    }
}

