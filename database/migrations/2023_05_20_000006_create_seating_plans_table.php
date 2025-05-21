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
            $table->integer('seat_number');
            $table->string('exam_name', 100);
            $table->date('exam_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
            
            // Ensure each student has only one seat per exam
            $table->unique(['student_id', 'exam_name', 'exam_date']);
            
            // Ensure each seat in a room is assigned to only one student per exam
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
