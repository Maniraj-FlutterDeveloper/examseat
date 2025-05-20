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
        Schema::create('room_invigilator_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('invigilator_id')->constrained()->onDelete('cascade');
            $table->string('exam_name');
            $table->date('exam_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('status', ['assigned', 'confirmed', 'completed', 'absent'])->default('assigned');
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            // Ensure an invigilator is not assigned to multiple rooms for the same exam at the same time
            $table->unique(['invigilator_id', 'exam_name', 'exam_date', 'start_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_invigilator_assignments');
    }
};
