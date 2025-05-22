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
        Schema::create('invigilator_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seating_plan_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('invigilator_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['primary', 'assistant', 'relief'])->default('assistant');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Ensure an invigilator is assigned only once per room and seating plan
            $table->unique(['seating_plan_id', 'room_id', 'invigilator_id'], 'invig_assign_plan_room_invig_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invigilator_assignments');
    }
};
