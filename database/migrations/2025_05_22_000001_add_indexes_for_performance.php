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
        // Add indexes to improve query performance
        
        // Seat Plan Module Indexes
        if (Schema::hasTable('rooms')) {
            Schema::table('rooms', function (Blueprint $table) {
                $table->index('block_id');
                $table->index('capacity');
                $table->index('is_active');
            });
        }
        
        if (Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $table) {
                $table->index('course_id');
                $table->index('roll_number');
                $table->index('year');
                $table->index('section');
                $table->index('is_active');
            });
        }
        
        if (Schema::hasTable('seating_plans')) {
            Schema::table('seating_plans', function (Blueprint $table) {
                $table->index('exam_date');
                $table->index('status');
            });
        }
        
        if (Schema::hasTable('seating_assignments')) {
            Schema::table('seating_assignments', function (Blueprint $table) {
                $table->index(['seating_plan_id', 'room_id']);
                $table->index(['seating_plan_id', 'student_id']);
            });
        }
        
        if (Schema::hasTable('invigilator_assignments')) {
            Schema::table('invigilator_assignments', function (Blueprint $table) {
                $table->index(['seating_plan_id', 'room_id']);
                $table->index(['seating_plan_id', 'invigilator_id']);
            });
        }
        
        // Question Bank Module Indexes
        if (Schema::hasTable('subjects')) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->index('code');
                $table->index('is_active');
            });
        }
        
        if (Schema::hasTable('units')) {
            Schema::table('units', function (Blueprint $table) {
                $table->index('subject_id');
                $table->index('code');
                $table->index('order');
                $table->index('is_active');
            });
        }
        
        if (Schema::hasTable('topics')) {
            Schema::table('topics', function (Blueprint $table) {
                $table->index('unit_id');
                $table->index('code');
                $table->index('order');
                $table->index('is_active');
            });
        }
        
        if (Schema::hasTable('questions')) {
            Schema::table('questions', function (Blueprint $table) {
                $table->index('topic_id');
                $table->index('question_type_id');
                $table->index('blooms_taxonomy_id');
                $table->index('difficulty_level');
                $table->index('marks');
                $table->index('is_active');
            });
        }
        
        if (Schema::hasTable('blueprints')) {
            Schema::table('blueprints', function (Blueprint $table) {
                $table->index('subject_id');
                $table->index('is_active');
            });
        }
        
        if (Schema::hasTable('question_papers')) {
            Schema::table('question_papers', function (Blueprint $table) {
                $table->index('subject_id');
                $table->index('blueprint_id');
                $table->index('exam_date');
                $table->index('status');
            });
        }
        
        if (Schema::hasTable('question_paper_questions')) {
            Schema::table('question_paper_questions', function (Blueprint $table) {
                $table->index(['question_paper_id', 'section_number']);
                $table->index(['question_paper_id', 'question_number']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove indexes
        
        // Seat Plan Module Indexes
        if (Schema::hasTable('rooms')) {
            Schema::table('rooms', function (Blueprint $table) {
                $table->dropIndex(['block_id']);
                $table->dropIndex(['capacity']);
                $table->dropIndex(['is_active']);
            });
        }
        
        if (Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropIndex(['course_id']);
                $table->dropIndex(['roll_number']);
                $table->dropIndex(['year']);
                $table->dropIndex(['section']);
                $table->dropIndex(['is_active']);
            });
        }
        
        if (Schema::hasTable('seating_plans')) {
            Schema::table('seating_plans', function (Blueprint $table) {
                $table->dropIndex(['exam_date']);
                $table->dropIndex(['status']);
            });
        }
        
        if (Schema::hasTable('seating_assignments')) {
            Schema::table('seating_assignments', function (Blueprint $table) {
                $table->dropIndex(['seating_plan_id', 'room_id']);
                $table->dropIndex(['seating_plan_id', 'student_id']);
            });
        }
        
        if (Schema::hasTable('invigilator_assignments')) {
            Schema::table('invigilator_assignments', function (Blueprint $table) {
                $table->dropIndex(['seating_plan_id', 'room_id']);
                $table->dropIndex(['seating_plan_id', 'invigilator_id']);
            });
        }
        
        // Question Bank Module Indexes
        if (Schema::hasTable('subjects')) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->dropIndex(['code']);
                $table->dropIndex(['is_active']);
            });
        }
        
        if (Schema::hasTable('units')) {
            Schema::table('units', function (Blueprint $table) {
                $table->dropIndex(['subject_id']);
                $table->dropIndex(['code']);
                $table->dropIndex(['order']);
                $table->dropIndex(['is_active']);
            });
        }
        
        if (Schema::hasTable('topics')) {
            Schema::table('topics', function (Blueprint $table) {
                $table->dropIndex(['unit_id']);
                $table->dropIndex(['code']);
                $table->dropIndex(['order']);
                $table->dropIndex(['is_active']);
            });
        }
        
        if (Schema::hasTable('questions')) {
            Schema::table('questions', function (Blueprint $table) {
                $table->dropIndex(['topic_id']);
                $table->dropIndex(['question_type_id']);
                $table->dropIndex(['blooms_taxonomy_id']);
                $table->dropIndex(['difficulty_level']);
                $table->dropIndex(['marks']);
                $table->dropIndex(['is_active']);
            });
        }
        
        if (Schema::hasTable('blueprints')) {
            Schema::table('blueprints', function (Blueprint $table) {
                $table->dropIndex(['subject_id']);
                $table->dropIndex(['is_active']);
            });
        }
        
        if (Schema::hasTable('question_papers')) {
            Schema::table('question_papers', function (Blueprint $table) {
                $table->dropIndex(['subject_id']);
                $table->dropIndex(['blueprint_id']);
                $table->dropIndex(['exam_date']);
                $table->dropIndex(['status']);
            });
        }
        
        if (Schema::hasTable('question_paper_questions')) {
            Schema::table('question_paper_questions', function (Blueprint $table) {
                $table->dropIndex(['question_paper_id', 'section_number']);
                $table->dropIndex(['question_paper_id', 'question_number']);
            });
        }
    }
};

