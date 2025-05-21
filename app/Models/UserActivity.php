<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'action',
        'module',
        'description',
        'ip_address',
        'user_agent',
        'properties',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'properties' => 'array',
    ];

    /**
     * Get the user that owns the activity.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include activities for a specific user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include activities for a specific action.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $action
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope a query to only include activities for a specific module.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $module
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeModule($query, $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope a query to only include activities within a date range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $start
     * @param  string  $end
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDateRange($query, $start, $end)
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }

    /**
     * Get all available activity actions.
     *
     * @return array
     */
    public static function getActions()
    {
        return [
            'login' => 'Login',
            'logout' => 'Logout',
            'create' => 'Create',
            'update' => 'Update',
            'delete' => 'Delete',
            'view' => 'View',
            'export' => 'Export',
            'import' => 'Import',
            'generate' => 'Generate',
            'assign' => 'Assign',
            'revoke' => 'Revoke',
            'reset_password' => 'Reset Password',
            'change_password' => 'Change Password',
            'change_settings' => 'Change Settings',
            'other' => 'Other',
        ];
    }

    /**
     * Get all available modules.
     *
     * @return array
     */
    public static function getModules()
    {
        return Permission::getModules();
    }
}

