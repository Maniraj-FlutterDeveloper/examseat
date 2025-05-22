<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if any 2025 migrations have been run
        $migrationTable = DB::table('migrations')->where('migration', 'like', '2025_%')->count();
        
        if ($migrationTable > 0) {
            // If 2025 migrations exist, we need to handle the conflicts
            
            // 1. Add missing columns from 2025 migrations to existing tables
            
            // Add auth fields to students table if they don't exist
            if (Schema::hasTable('students') && !Schema::hasColumn('students', 'email')) {
                Schema::table('students', function (Blueprint $table) {
                    $table->string('email')->nullable()->unique();
                    $table->string('password')->nullable();
                    $table->rememberToken();
                    $table->timestamp('email_verified_at')->nullable();
                });
            }
            
            // Add missing fields to seating_plans table
            if (Schema::hasTable('seating_plans') && !Schema::hasColumn('seating_plans', 'exam_date')) {
                Schema::table('seating_plans', function (Blueprint $table) {
                    $table->date('exam_date')->nullable();
                    $table->time('start_time')->nullable();
                    $table->time('end_time')->nullable();
                    $table->string('status')->default('draft');
                });
            }
            
            // 2. Create tables that only exist in 2025 migrations
            
            // Create notifications table if it doesn't exist
            if (!Schema::hasTable('notifications')) {
                Schema::create('notifications', function (Blueprint $table) {
                    $table->id();
                    $table->string('type');
                    $table->morphs('notifiable');
                    $table->text('data');
                    $table->timestamp('read_at')->nullable();
                    $table->timestamps();
                });
            }
            
            // Create student_priorities table if it doesn't exist
            if (!Schema::hasTable('student_priorities')) {
                Schema::create('student_priorities', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('student_id')->constrained()->onDelete('cascade');
                    $table->string('priority_type');
                    $table->text('description')->nullable();
                    $table->boolean('is_active')->default(true);
                    $table->timestamps();
                });
            }
            
            // Create seating_overrides table if it doesn't exist
            if (!Schema::hasTable('seating_overrides')) {
                Schema::create('seating_overrides', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('seating_plan_id')->constrained()->onDelete('cascade');
                    $table->foreignId('student_id')->constrained()->onDelete('cascade');
                    $table->foreignId('room_id')->nullable()->constrained()->onDelete('set null');
                    $table->string('seat_number')->nullable();
                    $table->text('reason')->nullable();
                    $table->timestamps();
                });
            }
            
            // Create seating_plan_student table if it doesn't exist
            if (!Schema::hasTable('seating_plan_student')) {
                Schema::create('seating_plan_student', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('seating_plan_id')->constrained()->onDelete('cascade');
                    $table->foreignId('student_id')->constrained()->onDelete('cascade');
                    $table->boolean('is_present')->default(false);
                    $table->timestamps();
                    
                    $table->unique(['seating_plan_id', 'student_id'], 'seat_plan_student_unique');
                });
            }
            
            // Create subject_course table if it doesn't exist
            if (!Schema::hasTable('subject_course')) {
                Schema::create('subject_course', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('subject_id')->constrained()->onDelete('cascade');
                    $table->foreignId('course_id')->constrained()->onDelete('cascade');
                    $table->timestamps();
                    
                    $table->unique(['subject_id', 'course_id'], 'subject_course_unique');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tables created in this migration
        Schema::dropIfExists('subject_course');
        Schema::dropIfExists('seating_plan_student');
        Schema::dropIfExists('seating_overrides');
        Schema::dropIfExists('student_priorities');
        Schema::dropIfExists('notifications');
        
        // Remove columns added to existing tables
        if (Schema::hasTable('students') && Schema::hasColumn('students', 'email')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn(['email', 'password', 'remember_token', 'email_verified_at']);
            });
        }
        
        if (Schema::hasTable('seating_plans') && Schema::hasColumn('seating_plans', 'exam_date')) {
            Schema::table('seating_plans', function (Blueprint $table) {
                $table->dropColumn(['exam_date', 'start_time', 'end_time', 'status']);
            });
        }
    }
};

