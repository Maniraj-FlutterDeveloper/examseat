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
        // Add missing foreign key constraints
        
        // Check and add foreign keys to seating_assignments table
        if (Schema::hasTable('seating_assignments') && 
            !$this->hasForeignKey('seating_assignments', 'seating_assignments_seating_plan_id_foreign')) {
            Schema::table('seating_assignments', function (Blueprint $table) {
                // Check if columns exist before adding constraints
                if (Schema::hasColumn('seating_assignments', 'seating_plan_id')) {
                    $table->foreign('seating_plan_id')
                          ->references('id')
                          ->on('seating_plans')
                          ->onDelete('cascade');
                }
                
                if (Schema::hasColumn('seating_assignments', 'room_id')) {
                    $table->foreign('room_id')
                          ->references('id')
                          ->on('rooms')
                          ->onDelete('cascade');
                }
                
                if (Schema::hasColumn('seating_assignments', 'student_id')) {
                    $table->foreign('student_id')
                          ->references('id')
                          ->on('students')
                          ->onDelete('cascade');
                }
            });
        }
        
        // Check and add foreign keys to seating_plan_rules table
        if (Schema::hasTable('seating_plan_rules') && 
            !$this->hasForeignKey('seating_plan_rules', 'seating_plan_rules_seating_plan_id_foreign')) {
            Schema::table('seating_plan_rules', function (Blueprint $table) {
                if (Schema::hasColumn('seating_plan_rules', 'seating_plan_id')) {
                    $table->foreign('seating_plan_id')
                          ->references('id')
                          ->on('seating_plans')
                          ->onDelete('cascade');
                }
                
                if (Schema::hasColumn('seating_plan_rules', 'seating_rule_id')) {
                    $table->foreign('seating_rule_id')
                          ->references('id')
                          ->on('seating_rules')
                          ->onDelete('cascade');
                }
            });
        }
        
        // Check and add foreign keys to invigilator_assignments table
        if (Schema::hasTable('invigilator_assignments') && 
            !$this->hasForeignKey('invigilator_assignments', 'invigilator_assignments_seating_plan_id_foreign')) {
            Schema::table('invigilator_assignments', function (Blueprint $table) {
                if (Schema::hasColumn('invigilator_assignments', 'seating_plan_id')) {
                    $table->foreign('seating_plan_id')
                          ->references('id')
                          ->on('seating_plans')
                          ->onDelete('cascade');
                }
                
                if (Schema::hasColumn('invigilator_assignments', 'room_id')) {
                    $table->foreign('room_id')
                          ->references('id')
                          ->on('rooms')
                          ->onDelete('cascade');
                }
                
                if (Schema::hasColumn('invigilator_assignments', 'invigilator_id')) {
                    $table->foreign('invigilator_id')
                          ->references('id')
                          ->on('invigilators')
                          ->onDelete('cascade');
                }
            });
        }
        
        // Check and add foreign keys to rooms table
        if (Schema::hasTable('rooms') && 
            !$this->hasForeignKey('rooms', 'rooms_block_id_foreign')) {
            Schema::table('rooms', function (Blueprint $table) {
                if (Schema::hasColumn('rooms', 'block_id')) {
                    $table->foreign('block_id')
                          ->references('id')
                          ->on('blocks')
                          ->onDelete('cascade');
                }
            });
        }
        
        // Check and add foreign keys to students table
        if (Schema::hasTable('students') && 
            !$this->hasForeignKey('students', 'students_course_id_foreign')) {
            Schema::table('students', function (Blueprint $table) {
                if (Schema::hasColumn('students', 'course_id')) {
                    $table->foreign('course_id')
                          ->references('id')
                          ->on('courses')
                          ->onDelete('cascade');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove foreign key constraints
        
        if (Schema::hasTable('seating_assignments')) {
            Schema::table('seating_assignments', function (Blueprint $table) {
                if ($this->hasForeignKey('seating_assignments', 'seating_assignments_seating_plan_id_foreign')) {
                    $table->dropForeign('seating_assignments_seating_plan_id_foreign');
                }
                
                if ($this->hasForeignKey('seating_assignments', 'seating_assignments_room_id_foreign')) {
                    $table->dropForeign('seating_assignments_room_id_foreign');
                }
                
                if ($this->hasForeignKey('seating_assignments', 'seating_assignments_student_id_foreign')) {
                    $table->dropForeign('seating_assignments_student_id_foreign');
                }
            });
        }
        
        if (Schema::hasTable('seating_plan_rules')) {
            Schema::table('seating_plan_rules', function (Blueprint $table) {
                if ($this->hasForeignKey('seating_plan_rules', 'seating_plan_rules_seating_plan_id_foreign')) {
                    $table->dropForeign('seating_plan_rules_seating_plan_id_foreign');
                }
                
                if ($this->hasForeignKey('seating_plan_rules', 'seating_plan_rules_seating_rule_id_foreign')) {
                    $table->dropForeign('seating_plan_rules_seating_rule_id_foreign');
                }
            });
        }
        
        if (Schema::hasTable('invigilator_assignments')) {
            Schema::table('invigilator_assignments', function (Blueprint $table) {
                if ($this->hasForeignKey('invigilator_assignments', 'invigilator_assignments_seating_plan_id_foreign')) {
                    $table->dropForeign('invigilator_assignments_seating_plan_id_foreign');
                }
                
                if ($this->hasForeignKey('invigilator_assignments', 'invigilator_assignments_room_id_foreign')) {
                    $table->dropForeign('invigilator_assignments_room_id_foreign');
                }
                
                if ($this->hasForeignKey('invigilator_assignments', 'invigilator_assignments_invigilator_id_foreign')) {
                    $table->dropForeign('invigilator_assignments_invigilator_id_foreign');
                }
            });
        }
        
        if (Schema::hasTable('rooms')) {
            Schema::table('rooms', function (Blueprint $table) {
                if ($this->hasForeignKey('rooms', 'rooms_block_id_foreign')) {
                    $table->dropForeign('rooms_block_id_foreign');
                }
            });
        }
        
        if (Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $table) {
                if ($this->hasForeignKey('students', 'students_course_id_foreign')) {
                    $table->dropForeign('students_course_id_foreign');
                }
            });
        }
    }
    
    /**
     * Check if a foreign key exists
     */
    private function hasForeignKey($table, $key)
    {
        $conn = Schema::getConnection();
        $dbSchemaManager = $conn->getDoctrineSchemaManager();
        $doctrineTable = $dbSchemaManager->listTableDetails($table);
        
        return $doctrineTable->hasForeignKey($key);
    }
};

