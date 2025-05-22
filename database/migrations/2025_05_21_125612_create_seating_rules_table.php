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
        Schema::create('seating_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // 'alternate_courses', 'distance', 'priority', etc.
            $table->text('description')->nullable();
            $table->json('parameters')->nullable(); // Store rule-specific parameters
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0); // Higher number means higher priority
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seating_rules');
    }
};

