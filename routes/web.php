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
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Authentication routes
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Seating Plan Module Routes
Route::middleware(['auth'])->group(function () {
    // Seating Plans
    Route::prefix('seating/plans')->name('seating.plans.')->group(function () {
        Route::get('/', [SeatingPlanController::class, 'index'])->name('index');
        Route::get('/create', [SeatingPlanController::class, 'create'])->name('create');
        Route::post('/', [SeatingPlanController::class, 'store'])->name('store');
        Route::get('/{seatingPlan}', [SeatingPlanController::class, 'show'])->name('show');
        Route::get('/{seatingPlan}/edit', [SeatingPlanController::class, 'edit'])->name('edit');
        Route::put('/{seatingPlan}', [SeatingPlanController::class, 'update'])->name('update');
        Route::delete('/{seatingPlan}', [SeatingPlanController::class, 'destroy'])->name('destroy');
        
        // Generate and save assignments
        Route::get('/{seatingPlan}/generate', [SeatingPlanController::class, 'generateAssignments'])->name('generate');
        Route::post('/{seatingPlan}/save', [SeatingPlanController::class, 'saveAssignments'])->name('save');
    });
    
    // Seating Rules
    Route::prefix('seating/rules')->name('seating.rules.')->group(function () {
        Route::get('/', [SeatingRuleController::class, 'index'])->name('index');
        Route::get('/create', [SeatingRuleController::class, 'create'])->name('create');
        Route::post('/', [SeatingRuleController::class, 'store'])->name('store');
        Route::get('/{seatingRule}', [SeatingRuleController::class, 'show'])->name('show');
        Route::get('/{seatingRule}/edit', [SeatingRuleController::class, 'edit'])->name('edit');
        Route::put('/{seatingRule}', [SeatingRuleController::class, 'update'])->name('update');
        Route::delete('/{seatingRule}', [SeatingRuleController::class, 'destroy'])->name('destroy');
    });
    
    // Student Priorities
    Route::prefix('seating/priorities')->name('seating.priorities.')->group(function () {
        Route::get('/', [StudentPriorityController::class, 'index'])->name('index');
        Route::get('/create', [StudentPriorityController::class, 'create'])->name('create');
        Route::post('/', [StudentPriorityController::class, 'store'])->name('store');
        Route::get('/{studentPriority}', [StudentPriorityController::class, 'show'])->name('show');
        Route::get('/{studentPriority}/edit', [StudentPriorityController::class, 'edit'])->name('edit');
        Route::put('/{studentPriority}', [StudentPriorityController::class, 'update'])->name('update');
        Route::delete('/{studentPriority}', [StudentPriorityController::class, 'destroy'])->name('destroy');
    });
    
    // Seating Overrides
    Route::prefix('seating/overrides')->name('seating.overrides.')->group(function () {
        Route::get('/', [SeatingOverrideController::class, 'index'])->name('index');
        Route::get('/create', [SeatingOverrideController::class, 'create'])->name('create');
        Route::post('/', [SeatingOverrideController::class, 'store'])->name('store');
        Route::get('/{seatingOverride}', [SeatingOverrideController::class, 'show'])->name('show');
        Route::get('/{seatingOverride}/edit', [SeatingOverrideController::class, 'edit'])->name('edit');
        Route::put('/{seatingOverride}', [SeatingOverrideController::class, 'update'])->name('update');
        Route::delete('/{seatingOverride}', [SeatingOverrideController::class, 'destroy'])->name('destroy');
    });
});

// API Routes for AJAX requests
Route::middleware(['auth'])->prefix('api')->group(function () {
    Route::get('/rooms/{room}/capacity', function (App\Models\Room $room) {
        return response()->json([
            'capacity' => $room->capacity,
            'layout' => $room->layout,
        ]);
    });
    
    Route::get('/students/search', function (Illuminate\Http\Request $request) {
        $query = $request->input('query');
        $students = App\Models\Student::where('name', 'like', "%{$query}%")
            ->orWhere('roll_number', 'like', "%{$query}%")
            ->with('course')
            ->limit(10)
            ->get();
        
        return response()->json($students);
    });
});

