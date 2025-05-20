<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\BlockController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\SeatingPlanController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\TopicController;
use App\Http\Controllers\Admin\BloomsTaxonomyController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\BlueprintController;
use App\Http\Controllers\Admin\QuestionPaperController;

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Public Routes
Route::get('/', function () {
    return view('welcome');
});

// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    
    // Block Management
    Route::resource('blocks', BlockController::class);
    
    // Room Management
    Route::resource('rooms', RoomController::class);
    
    // Course Management
    Route::resource('courses', CourseController::class);
    
    // Student Management
    Route::get('students/import', [StudentController::class, 'importForm'])->name('students.import_form');
    Route::post('students/import', [StudentController::class, 'import'])->name('students.import');
    Route::resource('students', StudentController::class);
    
    // Seating Plan Management
    Route::get('seating-plans', [SeatingPlanController::class, 'index'])->name('seating_plans.index');
    Route::get('seating-plans/create', [SeatingPlanController::class, 'create'])->name('seating_plans.create');
    Route::post('seating-plans', [SeatingPlanController::class, 'store'])->name('seating_plans.store');
    Route::get('seating-plans/{exam_name}/{exam_date}', [SeatingPlanController::class, 'show'])->name('seating_plans.show');
    Route::get('seating-plans/{exam_name}/{exam_date}/room/{room_id}', [SeatingPlanController::class, 'showRoom'])->name('seating_plans.show_room');
    Route::get('seating-plans/{exam_name}/{exam_date}/pdf', [SeatingPlanController::class, 'generatePdf'])->name('seating_plans.pdf');
    Route::delete('seating-plans/{exam_name}/{exam_date}', [SeatingPlanController::class, 'destroy'])->name('seating_plans.destroy');
    
    // Question Bank Module - Subject Management
    Route::resource('subjects', SubjectController::class);
    
    // Question Bank Module - Unit Management
    Route::resource('units', UnitController::class);
    Route::get('units/by-subject/{subject_id}', [UnitController::class, 'getUnitsBySubject'])->name('units.by_subject');
    
    // Question Bank Module - Topic Management
    Route::resource('topics', TopicController::class);
    Route::get('topics/by-unit/{unit_id}', [TopicController::class, 'getTopicsByUnit'])->name('topics.by_unit');
    Route::get('topics/by-subject/{subject_id}', [TopicController::class, 'getUnitsBySubject'])->name('topics.by_subject');
    
    // Question Bank Module - Bloom's Taxonomy Management
    Route::resource('blooms-taxonomy', BloomsTaxonomyController::class);
    
    // Question Bank Module - Question Management
    Route::resource('questions', QuestionController::class);
    Route::get('questions/by-subject/{subject_id}', [QuestionController::class, 'getUnitsBySubject'])->name('questions.by_subject');
    Route::get('questions/by-unit/{unit_id}', [QuestionController::class, 'getTopicsByUnit'])->name('questions.by_unit');
    
    // Question Bank Module - Blueprint Management
    Route::resource('blueprints', BlueprintController::class);
    Route::get('blueprints/{blueprint}/generate', [BlueprintController::class, 'generateQuestionPaper'])->name('blueprints.generate');
    Route::get('blueprints/by-subject/{subject_id}', [BlueprintController::class, 'getUnitsBySubject'])->name('blueprints.by_subject');
    Route::get('blueprints/topics-by-unit/{unit_id}', [BlueprintController::class, 'getTopicsByUnit'])->name('blueprints.topics_by_unit');
    
    // Question Bank Module - Question Paper Management
    Route::resource('question-papers', QuestionPaperController::class);
    Route::get('question-papers/generate/random', [QuestionPaperController::class, 'generateRandom'])->name('question_papers.generate_random');
    Route::post('question-papers/generate/random', [QuestionPaperController::class, 'storeRandom'])->name('question_papers.store_random');
    Route::get('question-papers/{questionPaper}/export-pdf', [QuestionPaperController::class, 'exportPdf'])->name('question_papers.export_pdf');
    Route::get('question-papers/blueprints-by-subject/{subject_id}', [QuestionPaperController::class, 'getBlueprintsBySubject'])->name('question_papers.blueprints_by_subject');
});
