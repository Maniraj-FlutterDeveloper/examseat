<?php

use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserActivityController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User Management Routes
|--------------------------------------------------------------------------
|
| Here is where you can register user management routes for your application.
|
*/

// User Management
Route::prefix('admin/users')->name('admin.users.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/create', [UserController::class, 'create'])->name('create');
    Route::post('/', [UserController::class, 'store'])->name('store');
    Route::get('/{id}', [UserController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');
    Route::put('/{id}', [UserController::class, 'update'])->name('update');
    Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
    Route::get('/{id}/activities', [UserController::class, 'activities'])->name('activities');
});

// Role Management
Route::prefix('admin/roles')->name('admin.roles.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [RoleController::class, 'index'])->name('index');
    Route::get('/create', [RoleController::class, 'create'])->name('create');
    Route::post('/', [RoleController::class, 'store'])->name('store');
    Route::get('/{id}', [RoleController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [RoleController::class, 'edit'])->name('edit');
    Route::put('/{id}', [RoleController::class, 'update'])->name('update');
    Route::delete('/{id}', [RoleController::class, 'destroy'])->name('destroy');
    Route::get('/{id}/users', [RoleController::class, 'users'])->name('users');
    Route::post('/{id}/users', [RoleController::class, 'assignUsers'])->name('assign_users');
});

// Permission Management
Route::prefix('admin/permissions')->name('admin.permissions.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [PermissionController::class, 'index'])->name('index');
    Route::get('/create', [PermissionController::class, 'create'])->name('create');
    Route::post('/', [PermissionController::class, 'store'])->name('store');
    Route::get('/{id}', [PermissionController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [PermissionController::class, 'edit'])->name('edit');
    Route::put('/{id}', [PermissionController::class, 'update'])->name('update');
    Route::delete('/{id}', [PermissionController::class, 'destroy'])->name('destroy');
    Route::get('/{id}/roles', [PermissionController::class, 'roles'])->name('roles');
    Route::post('/{id}/roles', [PermissionController::class, 'assignRoles'])->name('assign_roles');
});

// User Activity Logs
Route::prefix('admin/activities')->name('admin.activities.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [UserActivityController::class, 'index'])->name('index');
    Route::get('/{id}', [UserActivityController::class, 'show'])->name('show');
    Route::delete('/{id}', [UserActivityController::class, 'destroy'])->name('destroy');
    Route::post('/clear', [UserActivityController::class, 'clearAll'])->name('clear');
    Route::get('/export', [UserActivityController::class, 'export'])->name('export');
});

// User Profile
Route::prefix('admin/profile')->name('admin.profile.')->middleware(['auth'])->group(function () {
    Route::get('/', [UserController::class, 'editProfile'])->name('edit');
    Route::put('/', [UserController::class, 'updateProfile'])->name('update');
    Route::get('/preferences', [UserController::class, 'editPreferences'])->name('preferences');
    Route::put('/preferences', [UserController::class, 'updatePreferences'])->name('update_preferences');
});

