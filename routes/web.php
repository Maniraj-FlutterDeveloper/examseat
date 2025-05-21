<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PdfController;

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

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard', 'App\Http\Controllers\Admin\DashboardController@index')->name('dashboard');
    
    // Blocks
    Route::resource('blocks', 'App\Http\Controllers\Admin\BlockController');
    
    // Rooms
    Route::resource('rooms', 'App\Http\Controllers\Admin\RoomController');
    
    // Courses
    Route::resource('courses', 'App\Http\Controllers\Admin\CourseController');
    
    // Students
    Route::resource('students', 'App\Http\Controllers\Admin\StudentController');
    Route::post('students/import', 'App\Http\Controllers\Admin\StudentController@import')->name('students.import');
    
    // Seating Plans
    Route::resource('seating_plans', 'App\Http\Controllers\Admin\SeatingPlanController');
    Route::get('seating_plans/{id}/print', 'App\Http\Controllers\Admin\SeatingPlanController@print')->name('seating_plans.print');
    
    // Subjects
    Route::resource('subjects', 'App\Http\Controllers\Admin\SubjectController');
    
    // Units
    Route::resource('units', 'App\Http\Controllers\Admin\UnitController');
    Route::get('units/by-subject/{subject_id}', 'App\Http\Controllers\Admin\UnitController@getBySubject')->name('units.by_subject');
    
    // Topics
    Route::resource('topics', 'App\Http\Controllers\Admin\TopicController');
    Route::get('topics/by-unit/{unit_id}', 'App\Http\Controllers\Admin\TopicController@getByUnit')->name('topics.by_unit');
    
    // Bloom's Taxonomy
    Route::resource('blooms_taxonomies', 'App\Http\Controllers\Admin\BloomsTaxonomyController');
    
    // Questions
    Route::resource('questions', 'App\Http\Controllers\Admin\QuestionController');
    Route::get('questions/alternatives/{question_id}', 'App\Http\Controllers\Admin\QuestionController@getAlternatives')->name('questions.alternatives');
    
    // Blueprints
    Route::resource('blueprints', 'App\Http\Controllers\Admin\BlueprintController');
    Route::get('blueprints/{id}/details', 'App\Http\Controllers\Admin\BlueprintController@getDetails')->name('blueprints.details');
    
    // Question Papers
    Route::resource('question_papers', 'App\Http\Controllers\Admin\QuestionPaperController');
    Route::post('question_papers/update-question-order', 'App\Http\Controllers\Admin\QuestionPaperController@updateQuestionOrder')->name('question_papers.update_question_order');
    Route::post('question_papers/replace-question', 'App\Http\Controllers\Admin\QuestionPaperController@replaceQuestion')->name('question_papers.replace_question');
    Route::post('question_papers/{id}/regenerate-questions', 'App\Http\Controllers\Admin\QuestionPaperController@regenerateQuestions')->name('question_papers.regenerate_questions');
    
    // PDF Generation
    Route::get('pdf/question-paper/{id}', [PdfController::class, 'generateQuestionPaperPdf'])->name('pdf.question_paper');
});

// Auth routes
Auth::routes();

// Home route
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
