<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SeatingPlanController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\BloomsTaxonomyController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\BlueprintController;
use App\Http\Controllers\QuestionPaperController;
use App\Http\Controllers\InvigilatorController;
use App\Http\Controllers\RoomInvigilatorAssignmentController;

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Guest routes
Route::middleware(['guest'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });
});

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Blocks
    Route::resource('blocks', BlockController::class);
    
    // Rooms
    Route::resource('rooms', RoomController::class);
    
    // Courses
    Route::resource('courses', CourseController::class);
    
    // Students
    Route::resource('students', StudentController::class);
    Route::post('/students/import', [StudentController::class, 'import'])->name('students.import');
    Route::get('/students/export', [StudentController::class, 'export'])->name('students.export');
    
    // Seating Plans
    Route::resource('seating-plans', SeatingPlanController::class);
    Route::get('/seating-plans/{seatingPlan}/print', [SeatingPlanController::class, 'print'])->name('seating-plans.print');
    Route::post('/seating-plans/generate', [SeatingPlanController::class, 'generate'])->name('seating-plans.generate');
    
    // Subjects
    Route::resource('subjects', SubjectController::class);
    
    // Units
    Route::resource('units', UnitController::class);
    
    // Topics
    Route::resource('topics', TopicController::class);
    
    // Blooms Taxonomy
    Route::resource('blooms-taxonomy', BloomsTaxonomyController::class);
    
    // Questions
    Route::resource('questions', QuestionController::class);
    
    // Blueprints
    Route::resource('blueprints', BlueprintController::class);
    
    // Question Papers
    Route::resource('question-papers', QuestionPaperController::class);
    Route::get('/question-papers/{questionPaper}/print', [QuestionPaperController::class, 'print'])->name('question-papers.print');
    Route::post('/question-papers/generate', [QuestionPaperController::class, 'generate'])->name('question-papers.generate');
    
    // Invigilators
    Route::resource('invigilators', InvigilatorController::class);
    
    // Room Invigilator Assignments
    Route::resource('room-invigilator-assignments', RoomInvigilatorAssignmentController::class);
});

