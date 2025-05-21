<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'module',
    ];

    /**
     * Get the roles that belong to the permission.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Get the users that have the permission through roles.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user', 'permission_id', 'user_id');
    }

    /**
     * Scope a query to only include permissions for a specific module.
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
     * Get all available modules.
     *
     * @return array
     */
    public static function getModules()
    {
        return [
            'users' => 'User Management',
            'roles' => 'Role Management',
            'blocks' => 'Block Management',
            'rooms' => 'Room Management',
            'courses' => 'Course Management',
            'students' => 'Student Management',
            'seating_plans' => 'Seating Plan Management',
            'subjects' => 'Subject Management',
            'units' => 'Unit Management',
            'topics' => 'Topic Management',
            'questions' => 'Question Management',
            'blooms_taxonomies' => 'Bloom\'s Taxonomy Management',
            'blueprints' => 'Blueprint Management',
            'question_papers' => 'Question Paper Management',
            'reports' => 'Report Management',
            'analytics' => 'Analytics Management',
            'settings' => 'System Settings',
        ];
    }

    /**
     * Get all available permissions grouped by module.
     *
     * @return array
     */
    public static function getAllGrouped()
    {
        $permissions = self::all();
        $grouped = [];

        foreach ($permissions as $permission) {
            $grouped[$permission->module][] = $permission;
        }

        return $grouped;
    }
}

