<?php

use Illuminate\Support\Facades\Route;
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
| Question Bank Routes
|--------------------------------------------------------------------------
|
| Here is where you can register question bank routes for your application.
|
*/

Route::prefix('question-bank')->name('question-bank.')->group(function () {
    // Subjects
    Route::get('subjects/search', [SubjectController::class, 'search'])->name('subjects.search');
    Route::patch('subjects/{subject}/toggle-active', [SubjectController::class, 'toggleActive'])->name('subjects.toggle-active');
    Route::resource('subjects', SubjectController::class);
    
    // Units
    Route::get('subjects/{subject}/units', [UnitController::class, 'index'])->name('subjects.units.index');
    Route::get('subjects/{subject}/units/create', [UnitController::class, 'create'])->name('subjects.units.create');
    Route::post('subjects/{subject}/units', [UnitController::class, 'store'])->name('subjects.units.store');
    Route::get('subjects/{subject}/units/{unit}', [UnitController::class, 'show'])->name('subjects.units.show');
    Route::get('subjects/{subject}/units/{unit}/edit', [UnitController::class, 'edit'])->name('subjects.units.edit');
    Route::patch('subjects/{subject}/units/{unit}', [UnitController::class, 'update'])->name('subjects.units.update');
    Route::delete('subjects/{subject}/units/{unit}', [UnitController::class, 'destroy'])->name('subjects.units.destroy');
    Route::patch('subjects/{subject}/units/{unit}/toggle-active', [UnitController::class, 'toggleActive'])->name('subjects.units.toggle-active');
    Route::post('subjects/{subject}/units/reorder', [UnitController::class, 'reorder'])->name('subjects.units.reorder');
    
    // Topics
    Route::get('units/{unit}/topics', [TopicController::class, 'index'])->name('units.topics.index');
    Route::get('units/{unit}/topics/create', [TopicController::class, 'create'])->name('units.topics.create');
    Route::post('units/{unit}/topics', [TopicController::class, 'store'])->name('units.topics.store');
    Route::get('units/{unit}/topics/{topic}', [TopicController::class, 'show'])->name('units.topics.show');
    Route::get('units/{unit}/topics/{topic}/edit', [TopicController::class, 'edit'])->name('units.topics.edit');
    Route::patch('units/{unit}/topics/{topic}', [TopicController::class, 'update'])->name('units.topics.update');
    Route::delete('units/{unit}/topics/{topic}', [TopicController::class, 'destroy'])->name('units.topics.destroy');
    Route::patch('units/{unit}/topics/{topic}/toggle-active', [TopicController::class, 'toggleActive'])->name('units.topics.toggle-active');
    Route::post('units/{unit}/topics/reorder', [TopicController::class, 'reorder'])->name('units.topics.reorder');
    
    // Bloom's Taxonomy
    Route::patch('blooms-taxonomy/{bloomsTaxonomy}/toggle-active', [BloomsTaxonomyController::class, 'toggleActive'])->name('blooms-taxonomy.toggle-active');
    Route::resource('blooms-taxonomy', BloomsTaxonomyController::class);
    
    // Question Types
    Route::patch('question-types/{questionType}/toggle-active', [QuestionTypeController::class, 'toggleActive'])->name('question-types.toggle-active');
    Route::resource('question-types', QuestionTypeController::class);
    
    // Questions
    Route::get('questions/search', [QuestionController::class, 'search'])->name('questions.search');
    Route::get('topics/{topic}/questions/create', [QuestionController::class, 'create'])->name('topics.questions.create');
    Route::patch('questions/{question}/toggle-active', [QuestionController::class, 'toggleActive'])->name('questions.toggle-active');
    Route::post('questions/{question}/clone', [QuestionController::class, 'clone'])->name('questions.clone');
    Route::resource('questions', QuestionController::class);
    
    // Blueprints
    Route::patch('blueprints/{blueprint}/toggle-active', [BlueprintController::class, 'toggleActive'])->name('blueprints.toggle-active');
    Route::get('blueprints/{blueprint}/conditions/create', [BlueprintController::class, 'createCondition'])->name('blueprints.conditions.create');
    Route::post('blueprints/{blueprint}/conditions', [BlueprintController::class, 'storeCondition'])->name('blueprints.conditions.store');
    Route::get('blueprints/{blueprint}/conditions/{condition}/edit', [BlueprintController::class, 'editCondition'])->name('blueprints.conditions.edit');
    Route::patch('blueprints/{blueprint}/conditions/{condition}', [BlueprintController::class, 'updateCondition'])->name('blueprints.conditions.update');
    Route::delete('blueprints/{blueprint}/conditions/{condition}', [BlueprintController::class, 'destroyCondition'])->name('blueprints.conditions.destroy');
    Route::get('blueprints/{blueprint}/preview-questions', [BlueprintController::class, 'previewQuestions'])->name('blueprints.preview-questions');
    Route::resource('blueprints', BlueprintController::class);
    
    // Question Papers
    Route::post('question-papers/{questionPaper}/generate', [QuestionPaperController::class, 'generate'])->name('question-papers.generate');
    Route::post('question-papers/{questionPaper}/generate-random', [QuestionPaperController::class, 'generateRandom'])->name('question-papers.generate-random');
    Route::post('question-papers/{questionPaper}/clear', [QuestionPaperController::class, 'clear'])->name('question-papers.clear');
    Route::get('question-papers/{questionPaper}/add-question', [QuestionPaperController::class, 'addQuestion'])->name('question-papers.add-question');
    Route::post('question-papers/{questionPaper}/questions', [QuestionPaperController::class, 'storeQuestion'])->name('question-papers.store-question');
    Route::delete('question-papers/{questionPaper}/questions/{question}', [QuestionPaperController::class, 'removeQuestion'])->name('question-papers.remove-question');
    Route::get('question-papers/{questionPaper}/export-pdf', [QuestionPaperController::class, 'exportPdf'])->name('question-papers.export-pdf');
    Route::patch('question-papers/{questionPaper}/publish', [QuestionPaperController::class, 'publish'])->name('question-papers.publish');
    Route::patch('question-papers/{questionPaper}/archive', [QuestionPaperController::class, 'archive'])->name('question-papers.archive');
    Route::resource('question-papers', QuestionPaperController::class);
});

