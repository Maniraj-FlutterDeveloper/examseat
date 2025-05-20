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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained()->onDelete('cascade');
            $table->foreignId('bloom_id')->constrained('blooms_taxonomy')->onDelete('cascade');
            $table->text('question_text');
            $table->string('question_type'); // MCQ, Short Answer, Long Answer, etc.
            $table->text('options')->nullable(); // JSON encoded options for MCQs
            $table->text('correct_answer')->nullable();
            $table->decimal('marks', 5, 2);
            $table->enum('difficulty_level', ['easy', 'medium', 'hard'])->default('medium');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
