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
        Schema::create('seating_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seating_plan_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->string('seat_number');
            $table->text('reason')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            // Ensure a student can only have one override per seating plan
            $table->unique(['seating_plan_id', 'student_id']);
            
            // Ensure a seat can only be assigned once per room in a seating plan
            $table->unique(['seating_plan_id', 'room_id', 'seat_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seating_overrides');
    }
};

