<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\InvigilatorController;
use App\Http\Controllers\SeatingPlanController;
use App\Http\Controllers\SeatingRuleController;
use App\Http\Controllers\StudentPriorityController;
use App\Http\Controllers\SeatingOverrideController;
use App\Http\Controllers\SeatingPlanReportController;
use App\Http\Controllers\QuestionBank\SubjectController;
use App\Http\Controllers\QuestionBank\UnitController;
use App\Http\Controllers\QuestionBank\TopicController;
use App\Http\Controllers\QuestionBank\BloomsTaxonomyController;
use App\Http\Controllers\QuestionBank\QuestionTypeController;
use App\Http\Controllers\QuestionBank\QuestionController;
use App\Http\Controllers\QuestionBank\BlueprintController;
use App\Http\Controllers\QuestionBank\QuestionPaperController;

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
    // Blocks
    Route::resource('blocks', BlockController::class);
    Route::post('blocks/{block}/toggle-active', [BlockController::class, 'toggleActive'])->name('blocks.toggle-active');
    
    // Rooms
    Route::resource('rooms', RoomController::class);
    Route::post('rooms/{room}/toggle-active', [RoomController::class, 'toggleActive'])->name('rooms.toggle-active');
    
    // Courses
    Route::resource('courses', CourseController::class);
    
    // Students
    Route::resource('students', StudentController::class);
    Route::post('students/import', [StudentController::class, 'import'])->name('students.import');
    Route::get('students/export', [StudentController::class, 'export'])->name('students.export');
    
    // Invigilators
    Route::resource('invigilators', InvigilatorController::class);
    
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

// Question Bank Module Routes
Route::prefix('question-bank')->name('question-bank.')->group(function () {
    // Subjects
    Route::resource('subjects', SubjectController::class);
    
    // Units
    Route::resource('units', UnitController::class);
    
    // Topics
    Route::resource('topics', TopicController::class);
    
    // Bloom's Taxonomy
    Route::resource('blooms-taxonomy', BloomsTaxonomyController::class);
    
    // Question Types
    Route::resource('question-types', QuestionTypeController::class);
    
    // Questions
    Route::resource('questions', QuestionController::class);
    Route::post('questions/import', [QuestionController::class, 'import'])->name('questions.import');
    Route::get('questions/export', [QuestionController::class, 'export'])->name('questions.export');
    
    // Blueprints
    Route::resource('blueprints', BlueprintController::class);
    
    // Question Papers
    Route::resource('question-papers', QuestionPaperController::class);
    Route::post('question-papers/{blueprint}/generate', [QuestionPaperController::class, 'generate'])->name('question-papers.generate');
    Route::get('question-papers/{questionPaper}/preview', [QuestionPaperController::class, 'preview'])->name('question-papers.preview');
    Route::get('question-papers/{questionPaper}/download', [QuestionPaperController::class, 'download'])->name('question-papers.download');
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

// Auth routes
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

