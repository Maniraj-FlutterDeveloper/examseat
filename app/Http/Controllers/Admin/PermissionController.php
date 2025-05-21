<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Permission::query();

        // Apply filters
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('module') && $request->input('module')) {
            $query->where('module', $request->input('module'));
        }

        // Sort
        $sortField = $request->input('sort', 'name');
        $sortDirection = $request->input('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        $permissions = $query->withCount('roles')->paginate(10);
        $modules = Permission::getModules();

        return view('admin.permissions.index', [
            'permissions' => $permissions,
            'modules' => $modules,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $modules = Permission::getModules();

        return view('admin.permissions.create', [
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
            'slug' => ['nullable', 'string', 'max:255', 'unique:permissions'],
            'description' => ['nullable', 'string', 'max:1000'],
            'module' => ['required', 'string', Rule::in(array_keys(Permission::getModules()))],
        ]);

        // Generate slug if not provided
        $slug = $request->slug ?? Str::slug($request->name);

        // Create permission
        $permission = Permission::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'module' => $request->module,
        ]);

        // Log activity
        auth()->user()->logActivity(
            'create',
            'permissions',
            "Created permission: {$permission->name}",
            ['permission_id' => $permission->id]
        );

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $permission = Permission::with('roles')->findOrFail($id);
        $modules = Permission::getModules();

        return view('admin.permissions.show', [
            'permission' => $permission,
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
        $permission = Permission::findOrFail($id);
        $modules = Permission::getModules();

        return view('admin.permissions.edit', [
            'permission' => $permission,
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
        $permission = Permission::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('permissions')->ignore($permission->id)],
            'description' => ['nullable', 'string', 'max:1000'],
            'module' => ['required', 'string', Rule::in(array_keys(Permission::getModules()))],
        ]);

        // Generate slug if not provided
        $slug = $request->slug ?? Str::slug($request->name);

        // Update permission
        $permission->update([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'module' => $request->module,
        ]);

        // Log activity
        auth()->user()->logActivity(
            'update',
            'permissions',
            "Updated permission: {$permission->name}",
            ['permission_id' => $permission->id]
        );

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        $permissionName = $permission->name;

        // Log activity before deleting the permission
        auth()->user()->logActivity(
            'delete',
            'permissions',
            "Deleted permission: {$permissionName}",
            ['permission_id' => $permission->id]
        );

        $permission->delete();

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission deleted successfully.');
    }

    /**
     * Display the roles with the specified permission.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function roles($id)
    {
        $permission = Permission::findOrFail($id);
        $roles = $permission->roles()->paginate(10);

        return view('admin.permissions.roles', [
            'permission' => $permission,
            'roles' => $roles,
        ]);
    }

    /**
     * Assign roles to the specified permission.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function assignRoles(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);

        $request->validate([
            'roles' => ['required', 'array'],
            'roles.*' => ['exists:roles,id'],
        ]);

        $permission->roles()->sync($request->roles);

        // Log activity
        auth()->user()->logActivity(
            'assign',
            'permissions',
            "Assigned roles to permission: {$permission->name}",
            ['permission_id' => $permission->id, 'role_ids' => $request->roles]
        );

        return redirect()->route('admin.permissions.roles', $permission->id)
            ->with('success', 'Roles assigned to permission successfully.');
    }
}

