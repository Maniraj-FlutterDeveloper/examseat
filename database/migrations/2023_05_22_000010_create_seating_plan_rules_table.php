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
        Schema::create('seating_plan_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seating_plan_id')->constrained()->onDelete('cascade');
            $table->foreignId('seating_rule_id')->constrained()->onDelete('cascade');
            $table->json('parameters')->nullable()->comment('Specific parameters for this rule application');
            $table->integer('priority')->default(0)->comment('Higher priority rules are applied first');
            $table->timestamps();
            
            // Ensure a rule is applied only once per seating plan
            $table->unique(['seating_plan_id', 'seating_rule_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seating_plan_rules');
    }
};

