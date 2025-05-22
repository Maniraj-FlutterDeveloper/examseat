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
        Schema::create('seating_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seating_plan_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->integer('seat_number');
            $table->integer('row_number')->nullable();
            $table->integer('column_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Ensure a student is assigned only once per seating plan
            $table->unique(['seating_plan_id', 'student_id']);
            
            // Ensure a seat is assigned only once per room and seating plan
            $table->unique(['seating_plan_id', 'room_id', 'seat_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seating_assignments');
    }
};

