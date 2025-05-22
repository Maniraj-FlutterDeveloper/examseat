<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create subjects table
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Create units table
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Create topics table
        Schema::create('topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Create blooms_taxonomy table
        Schema::create('blooms_taxonomy', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('level')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Create question_types table
        Schema::create('question_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('structure')->nullable(); // JSON structure for dynamic fields
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Create questions table
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_type_id')->constrained()->onDelete('restrict');
            $table->foreignId('blooms_taxonomy_id')->nullable()->constrained('blooms_taxonomy')->onDelete('set null');
            $table->text('question_text');
            $table->json('options')->nullable(); // For MCQs, etc.
            $table->text('answer')->nullable();
            $table->text('solution')->nullable();
            $table->integer('difficulty_level')->default(1); // 1-5 scale
            $table->integer('marks')->default(1);
            $table->integer('estimated_time')->default(60); // in seconds
            $table->json('metadata')->nullable(); // Additional metadata
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Create blueprints table
        Schema::create('blueprints', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->integer('total_marks')->default(0);
            $table->integer('duration')->default(0); // in minutes
            $table->json('structure')->nullable(); // JSON structure for blueprint
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Create blueprint_conditions table
        Schema::create('blueprint_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blueprint_id')->constrained()->onDelete('cascade');
            $table->string('condition_type'); // unit, topic, difficulty, blooms, etc.
            $table->foreignId('reference_id')->nullable(); // ID of unit, topic, etc.
            $table->integer('question_count')->default(0);
            $table->integer('marks_per_question')->default(1);
            $table->foreignId('question_type_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('blooms_taxonomy_id')->nullable()->constrained('blooms_taxonomy')->onDelete('set null');
            $table->integer('difficulty_level')->nullable();
            $table->json('additional_criteria')->nullable();
            $table->timestamps();
        });

        // Create question_papers table
        Schema::create('question_papers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('blueprint_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('total_marks')->default(0);
            $table->integer('duration')->default(0); // in minutes
            $table->date('exam_date')->nullable();
            $table->string('status')->default('draft'); // draft, published, archived
            $table->timestamps();
        });

        // Create question_paper_questions table (pivot)
        Schema::create('question_paper_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_paper_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->integer('section_number')->default(1);
            $table->integer('question_number')->default(1);
            $table->integer('marks')->default(1);
            $table->boolean('is_optional')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_paper_questions');
        Schema::dropIfExists('question_papers');
        Schema::dropIfExists('blueprint_conditions');
        Schema::dropIfExists('blueprints');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('question_types');
        Schema::dropIfExists('blooms_taxonomy');
        Schema::dropIfExists('topics');
        Schema::dropIfExists('units');
        Schema::dropIfExists('subjects');
    }
};

