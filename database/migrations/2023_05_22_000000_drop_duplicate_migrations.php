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
        // Drop tables created by 2025 migrations if they exist
        // This ensures we only use the 2023 migrations which have the correct schema
        if (Schema::hasTable('blocks')) {
            Schema::dropIfExists('blocks');
        }
        if (Schema::hasTable('rooms')) {
            Schema::dropIfExists('rooms');
        }
        if (Schema::hasTable('courses')) {
            Schema::dropIfExists('courses');
        }
        if (Schema::hasTable('students')) {
            Schema::dropIfExists('students');
        }
        if (Schema::hasTable('seating_plans')) {
            Schema::dropIfExists('seating_plans');
        }
        if (Schema::hasTable('seating_assignments')) {
            Schema::dropIfExists('seating_assignments');
        }
        if (Schema::hasTable('invigilators')) {
            Schema::dropIfExists('invigilators');
        }
        if (Schema::hasTable('invigilator_assignments')) {
            Schema::dropIfExists('invigilator_assignments');
        }
        if (Schema::hasTable('seating_rules')) {
            Schema::dropIfExists('seating_rules');
        }
        if (Schema::hasTable('seating_plan_rules')) {
            Schema::dropIfExists('seating_plan_rules');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nothing to do here as we're just dropping tables
        // that will be recreated by the 2023 migrations
    }
};

