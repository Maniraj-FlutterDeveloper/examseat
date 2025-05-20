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
        Schema::create('seating_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('seat_number');
            $table->string('exam_name');
            $table->date('exam_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('status')->default('scheduled'); // scheduled, ongoing, completed, cancelled
            $table->timestamps();
            
            // Unique constraint to ensure a student is not assigned to multiple seats for the same exam
            $table->unique(['student_id', 'exam_name', 'exam_date']);
            
            // Unique constraint to ensure a seat is not assigned to multiple students for the same exam
            $table->unique(['room_id', 'seat_number', 'exam_name', 'exam_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seating_plans');
    }
};
