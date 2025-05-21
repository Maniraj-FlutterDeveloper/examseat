<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Role::query();

        // Apply filters
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('type') && $request->input('type')) {
            if ($request->input('type') === 'system') {
                $query->where('is_system', true);
            } elseif ($request->input('type') === 'custom') {
                $query->where('is_system', false);
            }
        }

        // Sort
        $sortField = $request->input('sort', 'name');
        $sortDirection = $request->input('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        $roles = $query->withCount('users')->paginate(10);

        return view('admin.roles.index', [
            'roles' => $roles,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissions = Permission::all()->groupBy('module');
        $modules = Permission::getModules();

        return view('admin.roles.create', [
            'permissions' => $permissions,
            'modules' => $modules,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:roles'],
            'description' => ['nullable', 'string', 'max:1000'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        // Generate slug if not provided
        $slug = $request->slug ?? Str::slug($request->name);

        // Create role
        $role = Role::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'is_system' => false, // Custom roles are not system roles
        ]);

        // Assign permissions
        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        // Log activity
        auth()->user()->logActivity(
            'create',
            'roles',
            "Created role: {$role->name}",
            ['role_id' => $role->id]
        );

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::with(['permissions', 'users'])->findOrFail($id);
        $permissionsByModule = $role->permissions->groupBy('module');
        $modules = Permission::getModules();

        return view('admin.roles.show', [
            'role' => $role,
            'permissionsByModule' => $permissionsByModule,
            'modules' => $modules,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        $permissions = Permission::all()->groupBy('module');
        $modules = Permission::getModules();

        return view('admin.roles.edit', [
            'role' => $role,
            'permissions' => $permissions,
            'modules' => $modules,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'description' => ['nullable', 'string', 'max:1000'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        // Generate slug if not provided
        $slug = $request->slug ?? Str::slug($request->name);

        // Update role
        $role->update([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
        ]);

        // Update permissions
        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        } else {
            $role->permissions()->detach();
        }

        // Log activity
        auth()->user()->logActivity(
            'update',
            'roles',
            "Updated role: {$role->name}",
            ['role_id' => $role->id]
        );

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        // Prevent deletion of system roles
        if ($role->is_system) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'System roles cannot be deleted.');
        }

        $roleName = $role->name;

        // Log activity before deleting the role
        auth()->user()->logActivity(
            'delete',
            'roles',
            "Deleted role: {$roleName}",
            ['role_id' => $role->id]
        );

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    /**
     * Display the users with the specified role.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function users($id)
    {
        $role = Role::findOrFail($id);
        $users = $role->users()->paginate(10);

        return view('admin.roles.users', [
            'role' => $role,
            'users' => $users,
        ]);
    }

    /**
     * Assign users to the specified role.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function assignUsers(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'users' => ['required', 'array'],
            'users.*' => ['exists:users,id'],
        ]);

        $role->users()->sync($request->users);

        // Log activity
        auth()->user()->logActivity(
            'assign',
            'roles',
            "Assigned users to role: {$role->name}",
            ['role_id' => $role->id, 'user_ids' => $request->users]
        );

        return redirect()->route('admin.roles.users', $role->id)
            ->with('success', 'Users assigned to role successfully.');
    }
}

