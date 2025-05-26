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
        Schema::create('student_priorities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->integer('priority_level'); // Higher number means higher priority
            $table->string('reason'); // 'disability', 'medical', 'special_request', etc.
            $table->text('notes')->nullable();
            $table->date('valid_until')->nullable(); // If the priority is temporary
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_priorities');
    }
};

