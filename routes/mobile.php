<?php

use App\Http\Controllers\Mobile\StudentPortalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Mobile Student Portal Routes
|--------------------------------------------------------------------------
|
| Here is where you can register mobile student portal routes for your application.
|
*/

// Authentication routes
Route::get('/login', [StudentPortalController::class, 'showLoginForm'])->name('mobile.login');
Route::post('/login', [StudentPortalController::class, 'login'])->name('mobile.login.submit');
Route::post('/logout', [StudentPortalController::class, 'logout'])->name('mobile.logout');

// Protected routes
Route::middleware(['student.auth'])->group(function () {
    // Dashboard
    Route::get('/', [StudentPortalController::class, 'dashboard'])->name('mobile.dashboard');
    
    // Profile
    Route::get('/profile', [StudentPortalController::class, 'profile'])->name('mobile.profile');
    Route::post('/profile', [StudentPortalController::class, 'updateProfile'])->name('mobile.profile.update');
    
    // Seating Plans
    Route::get('/seating-plans', [StudentPortalController::class, 'seatingPlans'])->name('mobile.seating_plans');
    Route::get('/seating-plans/{id}', [StudentPortalController::class, 'viewSeatingPlan'])->name('mobile.seating_plans.view');
    
    // Exam Schedule
    Route::get('/exam-schedule', [StudentPortalController::class, 'examSchedule'])->name('mobile.exam_schedule');
    
    // Notifications
    Route::get('/notifications', [StudentPortalController::class, 'notifications'])->name('mobile.notifications');
    Route::get('/notifications/{id}', [StudentPortalController::class, 'viewNotification'])->name('mobile.notifications.view');
    
    // Question Papers
    Route::get('/question-papers', [StudentPortalController::class, 'questionPapers'])->name('mobile.question_papers');
    Route::get('/question-papers/{id}', [StudentPortalController::class, 'viewQuestionPaper'])->name('mobile.question_papers.view');
});

