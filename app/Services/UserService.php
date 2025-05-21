<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserService
{
    /**
     * Create a new user with profile and roles.
     *
     * @param  array  $userData
     * @param  array  $profileData
     * @param  array  $roleIds
     * @return \App\Models\User
     */
    public function createUser(array $userData, array $profileData = [], array $roleIds = [])
    {
        // Create user
        $user = User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => Hash::make($userData['password']),
            'role' => $userData['role'] ?? 'user',
            'status' => $userData['status'] ?? 'active',
        ]);

        // Create profile
        if (!empty($profileData)) {
            // Handle avatar upload
            if (isset($profileData['avatar']) && $profileData['avatar']) {
                $profileData['avatar'] = $profileData['avatar']->store('avatars', 'public');
            }

            $user->profile()->create($profileData);
        } else {
            $user->profile()->create();
        }

        // Assign roles
        if (!empty($roleIds)) {
            foreach ($roleIds as $roleId) {
                $user->assignRole(Role::find($roleId));
            }
        }

        // Log activity
        if (auth()->check()) {
            auth()->user()->logActivity(
                'create',
                'users',
                "Created user: {$user->name}",
                ['user_id' => $user->id, 'email' => $user->email]
            );
        }

        return $user;
    }

    /**
     * Update a user with profile and roles.
     *
     * @param  \App\Models\User  $user
     * @param  array  $userData
     * @param  array  $profileData
     * @param  array  $roleIds
     * @return \App\Models\User
     */
    public function updateUser(User $user, array $userData, array $profileData = [], array $roleIds = [])
    {
        // Update user
        $updateData = [
            'name' => $userData['name'],
            'email' => $userData['email'],
            'role' => $userData['role'] ?? $user->role,
            'status' => $userData['status'] ?? $user->status,
        ];

        if (isset($userData['password']) && $userData['password']) {
            $updateData['password'] = Hash::make($userData['password']);
        }

        $user->update($updateData);

        // Update profile
        if (!empty($profileData)) {
            // Handle avatar upload
            if (isset($profileData['avatar']) && $profileData['avatar']) {
                // Delete old avatar if exists
                if ($user->profile && $user->profile->avatar) {
                    Storage::disk('public')->delete($user->profile->avatar);
                }
                $profileData['avatar'] = $profileData['avatar']->store('avatars', 'public');
            }

            if ($user->profile) {
                $user->profile->update($profileData);
            } else {
                $user->profile()->create($profileData);
            }
        }

        // Update roles
        if (!empty($roleIds)) {
            $user->roles()->sync($roleIds);
        }

        // Log activity
        if (auth()->check()) {
            auth()->user()->logActivity(
                'update',
                'users',
                "Updated user: {$user->name}",
                ['user_id' => $user->id, 'email' => $user->email]
            );
        }

        return $user;
    }

    /**
     * Delete a user.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function deleteUser(User $user)
    {
        $userName = $user->name;

        // Delete avatar if exists
        if ($user->profile && $user->profile->avatar) {
            Storage::disk('public')->delete($user->profile->avatar);
        }

        // Log activity before deleting the user
        if (auth()->check()) {
            auth()->user()->logActivity(
                'delete',
                'users',
                "Deleted user: {$userName}",
                ['user_id' => $user->id, 'email' => $user->email]
            );
        }

        return $user->delete();
    }

    /**
     * Create a new role with permissions.
     *
     * @param  array  $roleData
     * @param  array  $permissionIds
     * @return \App\Models\Role
     */
    public function createRole(array $roleData, array $permissionIds = [])
    {
        // Create role
        $role = Role::create([
            'name' => $roleData['name'],
            'slug' => $roleData['slug'] ?? \Str::slug($roleData['name']),
            'description' => $roleData['description'] ?? null,
            'is_system' => $roleData['is_system'] ?? false,
        ]);

        // Assign permissions
        if (!empty($permissionIds)) {
            $role->permissions()->sync($permissionIds);
        }

        // Log activity
        if (auth()->check()) {
            auth()->user()->logActivity(
                'create',
                'roles',
                "Created role: {$role->name}",
                ['role_id' => $role->id]
            );
        }

        return $role;
    }

    /**
     * Update a role with permissions.
     *
     * @param  \App\Models\Role  $role
     * @param  array  $roleData
     * @param  array  $permissionIds
     * @return \App\Models\Role
     */
    public function updateRole(Role $role, array $roleData, array $permissionIds = [])
    {
        // Update role
        $role->update([
            'name' => $roleData['name'],
            'slug' => $roleData['slug'] ?? \Str::slug($roleData['name']),
            'description' => $roleData['description'] ?? $role->description,
        ]);

        // Update permissions
        if (!empty($permissionIds)) {
            $role->permissions()->sync($permissionIds);
        }

        // Log activity
        if (auth()->check()) {
            auth()->user()->logActivity(
                'update',
                'roles',
                "Updated role: {$role->name}",
                ['role_id' => $role->id]
            );
        }

        return $role;
    }

    /**
     * Delete a role.
     *
     * @param  \App\Models\Role  $role
     * @return bool
     */
    public function deleteRole(Role $role)
    {
        $roleName = $role->name;

        // Prevent deletion of system roles
        if ($role->is_system) {
            return false;
        }

        // Log activity before deleting the role
        if (auth()->check()) {
            auth()->user()->logActivity(
                'delete',
                'roles',
                "Deleted role: {$roleName}",
                ['role_id' => $role->id]
            );
        }

        return $role->delete();
    }

    /**
     * Create a new permission.
     *
     * @param  array  $permissionData
     * @return \App\Models\Permission
     */
    public function createPermission(array $permissionData)
    {
        // Create permission
        $permission = Permission::create([
            'name' => $permissionData['name'],
            'slug' => $permissionData['slug'] ?? \Str::slug($permissionData['name']),
            'description' => $permissionData['description'] ?? null,
            'module' => $permissionData['module'],
        ]);

        // Log activity
        if (auth()->check()) {
            auth()->user()->logActivity(
                'create',
                'permissions',
                "Created permission: {$permission->name}",
                ['permission_id' => $permission->id]
            );
        }

        return $permission;
    }

    /**
     * Update a permission.
     *
     * @param  \App\Models\Permission  $permission
     * @param  array  $permissionData
     * @return \App\Models\Permission
     */
    public function updatePermission(Permission $permission, array $permissionData)
    {
        // Update permission
        $permission->update([
            'name' => $permissionData['name'],
            'slug' => $permissionData['slug'] ?? \Str::slug($permissionData['name']),
            'description' => $permissionData['description'] ?? $permission->description,
            'module' => $permissionData['module'],
        ]);

        // Log activity
        if (auth()->check()) {
            auth()->user()->logActivity(
                'update',
                'permissions',
                "Updated permission: {$permission->name}",
                ['permission_id' => $permission->id]
            );
        }

        return $permission;
    }

    /**
     * Delete a permission.
     *
     * @param  \App\Models\Permission  $permission
     * @return bool
     */
    public function deletePermission(Permission $permission)
    {
        $permissionName = $permission->name;

        // Log activity before deleting the permission
        if (auth()->check()) {
            auth()->user()->logActivity(
                'delete',
                'permissions',
                "Deleted permission: {$permissionName}",
                ['permission_id' => $permission->id]
            );
        }

        return $permission->delete();
    }

    /**
     * Get user statistics.
     *
     * @return array
     */
    public function getUserStatistics()
    {
        return [
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'inactive_users' => User::where('status', 'inactive')->count(),
            'admins' => User::where('role', 'admin')->count(),
            'users' => User::where('role', 'user')->count(),
            'invigilators' => User::where('role', 'invigilator')->count(),
            'examiners' => User::where('role', 'examiner')->count(),
            'total_roles' => Role::count(),
            'total_permissions' => Permission::count(),
            'total_activities' => UserActivity::count(),
            'recent_activities' => UserActivity::with('user')->latest()->limit(5)->get(),
            'recent_users' => User::with('profile')->latest()->limit(5)->get(),
        ];
    }
}

