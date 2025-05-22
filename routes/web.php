<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SeatingPlanController;
use App\Http\Controllers\SeatingRuleController;
use App\Http\Controllers\StudentPriorityController;
use App\Http\Controllers\SeatingOverrideController;
use App\Http\Controllers\SeatingPlanReportController;

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

// Seating Plan Module Routes
Route::prefix('seating')->name('seating.')->group(function () {
    // Seating Plans
    Route::resource('plans', SeatingPlanController::class);
    Route::get('plans/{seatingPlan}/assignments', [SeatingPlanController::class, 'showAssignments'])->name('plans.assignments');
    Route::post('plans/{seatingPlan}/generate', [SeatingPlanController::class, 'generateAssignments'])->name('plans.generate');
    Route::post('plans/{seatingPlan}/save-assignments', [SeatingPlanController::class, 'saveAssignments'])->name('plans.save-assignments');
    
    // Seating Rules
    Route::resource('rules', SeatingRuleController::class);
    
    // Student Priorities
    Route::resource('priorities', StudentPriorityController::class);
    
    // Seating Overrides
    Route::resource('overrides', SeatingOverrideController::class);
    
    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('plans/{seatingPlan}', [SeatingPlanReportController::class, 'index'])->name('index');
        
        // PDF Downloads
        Route::get('plans/{seatingPlan}/pdf', [SeatingPlanReportController::class, 'downloadSeatingPlanPdf'])->name('seating-plan-pdf');
        Route::get('plans/{seatingPlan}/view-pdf', [SeatingPlanReportController::class, 'viewSeatingPlanPdf'])->name('view-seating-plan-pdf');
        Route::get('plans/{seatingPlan}/room/{room}/pdf', [SeatingPlanReportController::class, 'downloadRoomPdf'])->name('room-pdf');
        Route::get('plans/{seatingPlan}/student-cards', [SeatingPlanReportController::class, 'downloadStudentCardsPdf'])->name('student-cards-pdf');
        Route::get('plans/{seatingPlan}/invigilator', [SeatingPlanReportController::class, 'downloadInvigilatorReportPdf'])->name('invigilator-pdf');
        Route::get('plans/{seatingPlan}/attendance', [SeatingPlanReportController::class, 'downloadAttendanceSheetPdf'])->name('attendance-pdf');
        Route::get('plans/{seatingPlan}/room/{room}/attendance', [SeatingPlanReportController::class, 'downloadAttendanceSheetPdf'])->name('room-attendance-pdf');
        
        // Notifications
        Route::post('plans/{seatingPlan}/notify', [SeatingPlanReportController::class, 'notifyAllStudents'])->name('notify-students');
        Route::post('plans/{seatingPlan}/remind', [SeatingPlanReportController::class, 'sendReminders'])->name('send-reminders');
        
        // Incidents
        Route::post('plans/{seatingPlan}/incident', [SeatingPlanReportController::class, 'logIncident'])->name('log-incident');
    });
});

// API Routes for AJAX
Route::prefix('api')->name('api.')->group(function () {
    Route::get('rooms/{room}/capacity', function ($room) {
        $room = \App\Models\Room::findOrFail($room);
        return response()->json([
            'capacity' => $room->capacity,
            'layout' => $room->layout,
        ]);
    })->name('room-capacity');
    
    Route::get('students/search', function (\Illuminate\Http\Request $request) {
        $query = $request->input('query');
        $students = \App\Models\Student::where('name', 'like', "%{$query}%")
            ->orWhere('roll_number', 'like', "%{$query}%")
            ->limit(20)
            ->get();
        return response()->json($students);
    })->name('student-search');
    
    Route::get('seating-plans/{seatingPlan}/assignments', function ($seatingPlan) {
        $seatingPlan = \App\Models\SeatingPlan::findOrFail($seatingPlan);
        $assignments = $seatingPlan->assignments()->with(['student', 'room'])->get();
        return response()->json($assignments);
    })->name('seating-plan-assignments');
    
    Route::post('seating-plans/{seatingPlan}/assignments', function (\Illuminate\Http\Request $request, $seatingPlan) {
        $seatingPlan = \App\Models\SeatingPlan::findOrFail($seatingPlan);
        
        $assignment = $seatingPlan->assignments()->updateOrCreate(
            [
                'room_id' => $request->input('room_id'),
                'seat_number' => $request->input('seat_number'),
            ],
            [
                'student_id' => $request->input('student_id'),
                'is_override' => $request->input('is_override', false),
            ]
        );
        
        return response()->json($assignment);
    })->name('save-assignment');
    
    Route::delete('seating-plans/{seatingPlan}/assignments', function (\Illuminate\Http\Request $request, $seatingPlan) {
        $seatingPlan = \App\Models\SeatingPlan::findOrFail($seatingPlan);
        
        $deleted = $seatingPlan->assignments()
            ->where('room_id', $request->input('room_id'))
            ->where('seat_number', $request->input('seat_number'))
            ->delete();
        
        return response()->json(['success' => $deleted > 0]);
    })->name('delete-assignment');
    
    Route::post('seating-plans/{seatingPlan}/generate', function ($seatingPlan) {
        $seatingPlan = \App\Models\SeatingPlan::findOrFail($seatingPlan);
        
        // In a real implementation, this would call a service to generate assignments
        // For now, we'll just return a success message
        
        return response()->json(['success' => true]);
    })->name('generate-assignments');
    
    Route::post('seating-overrides', function (\Illuminate\Http\Request $request) {
        $override = \App\Models\SeatingOverride::create($request->all());
        return response()->json($override);
    })->name('create-override');
});

// Question Bank Module Routes (to be implemented)
Route::prefix('question-bank')->name('question-bank.')->group(function () {
    // Placeholder routes for future implementation
});

// Auth routes
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

