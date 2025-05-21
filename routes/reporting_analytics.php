<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Reporting and Analytics Routes
|--------------------------------------------------------------------------
|
| Here is where you can register reporting and analytics routes for your application.
|
*/

// Reports
Route::prefix('admin/reports')->name('admin.reports.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('/create', [ReportController::class, 'create'])->name('create');
    Route::post('/', [ReportController::class, 'store'])->name('store');
    Route::get('/{id}', [ReportController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [ReportController::class, 'edit'])->name('edit');
    Route::put('/{id}', [ReportController::class, 'update'])->name('update');
    Route::delete('/{id}', [ReportController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/generate', [ReportController::class, 'generate'])->name('generate');
    Route::get('/result/{id}', [ReportController::class, 'result'])->name('result');
    Route::get('/result/{id}/download', [ReportController::class, 'download'])->name('download');
    Route::post('/{id}/toggle-favorite', [ReportController::class, 'toggleFavorite'])->name('toggle_favorite');
});

// Analytics
Route::prefix('admin/analytics')->name('admin.analytics.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'analytics'])->name('index');
    Route::get('/dashboard', [DashboardController::class, 'analytics'])->name('dashboard');
    Route::get('/dashboard/{dashboard_id}', [DashboardController::class, 'analytics'])->name('dashboard');
    
    // Dashboard management
    Route::get('/dashboards/create', [DashboardController::class, 'createDashboard'])->name('create_dashboard');
    Route::post('/dashboards', [DashboardController::class, 'storeDashboard'])->name('store_dashboard');
    Route::get('/dashboards/{id}/edit', [DashboardController::class, 'editDashboard'])->name('edit_dashboard');
    Route::put('/dashboards/{id}', [DashboardController::class, 'updateDashboard'])->name('update_dashboard');
    Route::delete('/dashboards/{id}', [DashboardController::class, 'destroyDashboard'])->name('destroy_dashboard');
    
    // Widget management
    Route::get('/dashboards/{dashboard_id}/widgets/create', [DashboardController::class, 'createWidget'])->name('create_widget');
    Route::post('/dashboards/{dashboard_id}/widgets', [DashboardController::class, 'storeWidget'])->name('store_widget');
    Route::get('/widgets/{id}/edit', [DashboardController::class, 'editWidget'])->name('edit_widget');
    Route::put('/widgets/{id}', [DashboardController::class, 'updateWidget'])->name('update_widget');
    Route::delete('/widgets/{id}', [DashboardController::class, 'destroyWidget'])->name('destroy_widget');
    Route::post('/dashboards/{dashboard_id}/widgets/positions', [DashboardController::class, 'updateWidgetPositions'])->name('update_widget_positions');
    Route::get('/widgets/{id}/data', [DashboardController::class, 'getWidgetData'])->name('widget_data');
});

