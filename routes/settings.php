<?php

use App\Http\Controllers\Admin\SettingsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Settings Routes
|--------------------------------------------------------------------------
|
| Here is where you can register settings routes for your application.
|
*/

// Settings
Route::prefix('admin/settings')->name('admin.settings.')->middleware(['auth', 'admin'])->group(function () {
    // General settings
    Route::get('/{group?}', [SettingsController::class, 'index'])->name('index');
    Route::post('/{group}', [SettingsController::class, 'update'])->name('update');
    
    // Setting management
    Route::get('/setting/create', [SettingsController::class, 'create'])->name('create');
    Route::post('/setting', [SettingsController::class, 'store'])->name('store');
    Route::get('/setting/{id}/edit', [SettingsController::class, 'edit'])->name('edit');
    Route::put('/setting/{id}', [SettingsController::class, 'updateSetting'])->name('update_setting');
    Route::delete('/setting/{id}', [SettingsController::class, 'destroy'])->name('destroy');
    
    // Backup
    Route::get('/backup/index', [SettingsController::class, 'backup'])->name('backup');
    Route::post('/backup/create', [SettingsController::class, 'createBackup'])->name('create_backup');
    Route::get('/backup/download/{filename}', [SettingsController::class, 'downloadBackup'])->name('download_backup');
    Route::delete('/backup/{filename}', [SettingsController::class, 'deleteBackup'])->name('delete_backup');
    
    // System information
    Route::get('/system-info', [SettingsController::class, 'systemInfo'])->name('system_info');
    Route::post('/clear-cache', [SettingsController::class, 'clearCache'])->name('clear_cache');
    
    // Email test
    Route::get('/email-test', [SettingsController::class, 'emailTest'])->name('email_test');
    Route::post('/send-test-email', [SettingsController::class, 'sendTestEmail'])->name('send_test_email');
    
    // Theme
    Route::get('/theme', [SettingsController::class, 'theme'])->name('theme');
    Route::post('/theme', [SettingsController::class, 'updateTheme'])->name('update_theme');
});

