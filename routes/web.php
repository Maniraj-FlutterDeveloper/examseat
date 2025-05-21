<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SeatingPlanController;
use App\Http\Controllers\SeatingRuleController;
use App\Http\Controllers\StudentPriorityController;
use App\Http\Controllers\SeatingOverrideController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Seating Plan Routes
Route::prefix('seating')->name('seating.')->group(function () {
    // Seating Plans
    Route::resource('plans', SeatingPlanController::class);
    Route::get('plans/{seatingPlan}/generate', [SeatingPlanController::class, 'generateAssignments'])->name('plans.generate');
    Route::post('plans/{seatingPlan}/save', [SeatingPlanController::class, 'saveAssignments'])->name('plans.save');
    
    // Seating Rules
    Route::resource('rules', SeatingRuleController::class);
    
    // Student Priorities
    Route::resource('priorities', StudentPriorityController::class);
    
    // Seating Overrides
    Route::resource('overrides', SeatingOverrideController::class);
});

