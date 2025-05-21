<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Apply filters
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('role') && $request->input('role')) {
            $query->where('role', $request->input('role'));
        }

        if ($request->has('status') && $request->input('status')) {
            $query->where('status', $request->input('status'));
        }

        // Sort
        $sortField = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $users = $query->paginate(10);

        return view('admin.users.index', [
            'users' => $users,
            'roles' => [
                'admin' => 'Admin',
                'user' => 'User',
                'invigilator' => 'Invigilator',
                'examiner' => 'Examiner',
            ],
            'statuses' => [
                'active' => 'Active',
                'inactive' => 'Inactive',
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::all();

        return view('admin.users.create', [
            'roles' => $roles,
            'userRoles' => [
                'admin' => 'Admin',
                'user' => 'User',
                'invigilator' => 'Invigilator',
                'examiner' => 'Examiner',
            ],
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
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', Rule::in(['admin', 'user', 'invigilator', 'examiner'])],
            'status' => ['required', 'string', Rule::in(['active', 'inactive'])],
            'avatar' => ['nullable', 'image', 'max:1024'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'job_title' => ['nullable', 'string', 'max:100'],
            'department' => ['nullable', 'string', 'max:100'],
            'social_links.linkedin' => ['nullable', 'url'],
            'social_links.twitter' => ['nullable', 'url'],
            'social_links.facebook' => ['nullable', 'url'],
            'social_links.instagram' => ['nullable', 'url'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,id'],
        ]);

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => $request->status,
        ]);

        // Create profile
        $profileData = $request->only([
            'phone', 'address', 'city', 'state', 'country', 'postal_code',
            'bio', 'job_title', 'department',
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $profileData['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        // Handle social links
        if ($request->has('social_links')) {
            $profileData['social_links'] = $request->social_links;
        }

        $user->profile()->create($profileData);

        // Assign roles
        if ($request->has('roles')) {
            foreach ($request->roles as $roleId) {
                $user->assignRole(Role::find($roleId));
            }
        }

        // Log activity
        $user->logActivity(
            'create',
            'users',
            "Created user: {$user->name}",
            ['user_id' => $user->id, 'email' => $user->email]
        );

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::with(['profile', 'roles', 'activities' => function ($query) {
            $query->latest()->limit(10);
        }])->findOrFail($id);

        return view('admin.users.show', [
            'user' => $user,
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
        $user = User::with(['profile', 'roles'])->findOrFail($id);
        $roles = Role::all();

        return view('admin.users.edit', [
            'user' => $user,
            'roles' => $roles,
            'userRoles' => [
                'admin' => 'Admin',
                'user' => 'User',
                'invigilator' => 'Invigilator',
                'examiner' => 'Examiner',
            ],
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
        $user = User::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', Rule::in(['admin', 'user', 'invigilator', 'examiner'])],
            'status' => ['required', 'string', Rule::in(['active', 'inactive'])],
            'avatar' => ['nullable', 'image', 'max:1024'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'job_title' => ['nullable', 'string', 'max:100'],
            'department' => ['nullable', 'string', 'max:100'],
            'social_links.linkedin' => ['nullable', 'url'],
            'social_links.twitter' => ['nullable', 'url'],
            'social_links.facebook' => ['nullable', 'url'],
            'social_links.instagram' => ['nullable', 'url'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,id'],
        ]);

        // Update user
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'status' => $request->status,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        // Update profile
        $profileData = $request->only([
            'phone', 'address', 'city', 'state', 'country', 'postal_code',
            'bio', 'job_title', 'department',
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->profile && $user->profile->avatar) {
                Storage::disk('public')->delete($user->profile->avatar);
            }
            $profileData['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        // Handle social links
        if ($request->has('social_links')) {
            $profileData['social_links'] = $request->social_links;
        }

        if ($user->profile) {
            $user->profile->update($profileData);
        } else {
            $user->profile()->create($profileData);
        }

        // Update roles
        if ($request->has('roles')) {
            $user->roles()->sync($request->roles);
        } else {
            $user->roles()->detach();
        }

        // Log activity
        $user->logActivity(
            'update',
            'users',
            "Updated user: {$user->name}",
            ['user_id' => $user->id, 'email' => $user->email]
        );

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $userName = $user->name;

        // Delete avatar if exists
        if ($user->profile && $user->profile->avatar) {
            Storage::disk('public')->delete($user->profile->avatar);
        }

        // Log activity before deleting the user
        auth()->user()->logActivity(
            'delete',
            'users',
            "Deleted user: {$userName}",
            ['user_id' => $user->id, 'email' => $user->email]
        );

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Display the user's activity log.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function activities($id)
    {
        $user = User::findOrFail($id);
        $activities = $user->activities()->latest()->paginate(20);

        return view('admin.users.activities', [
            'user' => $user,
            'activities' => $activities,
        ]);
    }

    /**
     * Update the user's profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'current_password' => ['nullable', 'required_with:password', 'string'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'avatar' => ['nullable', 'image', 'max:1024'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'job_title' => ['nullable', 'string', 'max:100'],
            'department' => ['nullable', 'string', 'max:100'],
            'social_links.linkedin' => ['nullable', 'url'],
            'social_links.twitter' => ['nullable', 'url'],
            'social_links.facebook' => ['nullable', 'url'],
            'social_links.instagram' => ['nullable', 'url'],
        ]);

        // Verify current password if changing password
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'The current password is incorrect.']);
            }
        }

        // Update user
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        // Update profile
        $profileData = $request->only([
            'phone', 'address', 'city', 'state', 'country', 'postal_code',
            'bio', 'job_title', 'department',
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->profile && $user->profile->avatar) {
                Storage::disk('public')->delete($user->profile->avatar);
            }
            $profileData['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        // Handle social links
        if ($request->has('social_links')) {
            $profileData['social_links'] = $request->social_links;
        }

        if ($user->profile) {
            $user->profile->update($profileData);
        } else {
            $user->profile()->create($profileData);
        }

        // Log activity
        $user->logActivity(
            'update',
            'users',
            'Updated profile information',
            ['user_id' => $user->id]
        );

        return redirect()->route('admin.profile.edit')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Show the user's profile edit form.
     *
     * @return \Illuminate\Http\Response
     */
    public function editProfile()
    {
        $user = auth()->user()->load('profile');

        return view('admin.users.profile', [
            'user' => $user,
        ]);
    }

    /**
     * Update the user's preferences.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePreferences(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'preferences.theme' => ['nullable', 'string', Rule::in(['light', 'dark', 'system'])],
            'preferences.sidebar_collapsed' => ['nullable', 'boolean'],
            'preferences.notifications_enabled' => ['nullable', 'boolean'],
            'preferences.email_notifications' => ['nullable', 'boolean'],
        ]);

        if (!$user->profile) {
            $user->profile()->create(['preferences' => $request->preferences]);
        } else {
            $user->profile->update(['preferences' => $request->preferences]);
        }

        // Log activity
        $user->logActivity(
            'update',
            'users',
            'Updated preferences',
            ['user_id' => $user->id]
        );

        return redirect()->route('admin.profile.preferences')
            ->with('success', 'Preferences updated successfully.');
    }

    /**
     * Show the user's preferences edit form.
     *
     * @return \Illuminate\Http\Response
     */
    public function editPreferences()
    {
        $user = auth()->user()->load('profile');

        return view('admin.users.preferences', [
            'user' => $user,
        ]);
    }
}

