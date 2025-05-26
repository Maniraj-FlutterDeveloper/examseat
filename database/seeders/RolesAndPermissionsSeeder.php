<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $adminRole = Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'description' => 'System administrator with full access to all features',
        ]);

        $examCoordinatorRole = Role::create([
            'name' => 'exam_coordinator',
            'display_name' => 'Exam Coordinator',
            'description' => 'Manages exam seating plans and question papers',
        ]);

        $invigilatorRole = Role::create([
            'name' => 'invigilator',
            'display_name' => 'Invigilator',
            'description' => 'Supervises exams and manages assigned rooms',
        ]);

        $studentRole = Role::create([
            'name' => 'student',
            'display_name' => 'Student',
            'description' => 'Student access for viewing seating plans and exam details',
        ]);

        // Create permissions by module
        $this->createSeatPlanPermissions();
        $this->createQuestionBankPermissions();
        $this->createUserManagementPermissions();
        $this->createReportingPermissions();

        // Assign permissions to roles
        $this->assignPermissionsToRoles($adminRole, $examCoordinatorRole, $invigilatorRole, $studentRole);

        // Assign admin role to default admin user
        $adminUser = User::where('email', 'admin@kalvierp.com')->first();
        if ($adminUser) {
            $adminUser->roles()->attach($adminRole->id);
        }
    }

    /**
     * Create permissions for Seat Plan module
     */
    private function createSeatPlanPermissions(): void
    {
        $seatPlanPermissions = [
            // Block management
            ['name' => 'blocks.view', 'display_name' => 'View Blocks', 'module' => 'seat_plan'],
            ['name' => 'blocks.create', 'display_name' => 'Create Blocks', 'module' => 'seat_plan'],
            ['name' => 'blocks.edit', 'display_name' => 'Edit Blocks', 'module' => 'seat_plan'],
            ['name' => 'blocks.delete', 'display_name' => 'Delete Blocks', 'module' => 'seat_plan'],

            // Room management
            ['name' => 'rooms.view', 'display_name' => 'View Rooms', 'module' => 'seat_plan'],
            ['name' => 'rooms.create', 'display_name' => 'Create Rooms', 'module' => 'seat_plan'],
            ['name' => 'rooms.edit', 'display_name' => 'Edit Rooms', 'module' => 'seat_plan'],
            ['name' => 'rooms.delete', 'display_name' => 'Delete Rooms', 'module' => 'seat_plan'],

            // Course management
            ['name' => 'courses.view', 'display_name' => 'View Courses', 'module' => 'seat_plan'],
            ['name' => 'courses.create', 'display_name' => 'Create Courses', 'module' => 'seat_plan'],
            ['name' => 'courses.edit', 'display_name' => 'Edit Courses', 'module' => 'seat_plan'],
            ['name' => 'courses.delete', 'display_name' => 'Delete Courses', 'module' => 'seat_plan'],

            // Student management
            ['name' => 'students.view', 'display_name' => 'View Students', 'module' => 'seat_plan'],
            ['name' => 'students.create', 'display_name' => 'Create Students', 'module' => 'seat_plan'],
            ['name' => 'students.edit', 'display_name' => 'Edit Students', 'module' => 'seat_plan'],
            ['name' => 'students.delete', 'display_name' => 'Delete Students', 'module' => 'seat_plan'],
            ['name' => 'students.import', 'display_name' => 'Import Students', 'module' => 'seat_plan'],
            ['name' => 'students.export', 'display_name' => 'Export Students', 'module' => 'seat_plan'],

            // Invigilator management
            ['name' => 'invigilators.view', 'display_name' => 'View Invigilators', 'module' => 'seat_plan'],
            ['name' => 'invigilators.create', 'display_name' => 'Create Invigilators', 'module' => 'seat_plan'],
            ['name' => 'invigilators.edit', 'display_name' => 'Edit Invigilators', 'module' => 'seat_plan'],
            ['name' => 'invigilators.delete', 'display_name' => 'Delete Invigilators', 'module' => 'seat_plan'],
            ['name' => 'invigilators.assign', 'display_name' => 'Assign Invigilators', 'module' => 'seat_plan'],

            // Seating plan management
            ['name' => 'seating_plans.view', 'display_name' => 'View Seating Plans', 'module' => 'seat_plan'],
            ['name' => 'seating_plans.create', 'display_name' => 'Create Seating Plans', 'module' => 'seat_plan'],
            ['name' => 'seating_plans.edit', 'display_name' => 'Edit Seating Plans', 'module' => 'seat_plan'],
            ['name' => 'seating_plans.delete', 'display_name' => 'Delete Seating Plans', 'module' => 'seat_plan'],
            ['name' => 'seating_plans.generate', 'display_name' => 'Generate Seating Plans', 'module' => 'seat_plan'],
            ['name' => 'seating_plans.publish', 'display_name' => 'Publish Seating Plans', 'module' => 'seat_plan'],
            ['name' => 'seating_plans.export', 'display_name' => 'Export Seating Plans', 'module' => 'seat_plan'],

            // Seating rules management
            ['name' => 'seating_rules.view', 'display_name' => 'View Seating Rules', 'module' => 'seat_plan'],
            ['name' => 'seating_rules.create', 'display_name' => 'Create Seating Rules', 'module' => 'seat_plan'],
            ['name' => 'seating_rules.edit', 'display_name' => 'Edit Seating Rules', 'module' => 'seat_plan'],
            ['name' => 'seating_rules.delete', 'display_name' => 'Delete Seating Rules', 'module' => 'seat_plan'],
        ];

        foreach ($seatPlanPermissions as $permission) {
            Permission::create($permission);
        }
    }

    /**
     * Create permissions for Question Bank module
     */
    private function createQuestionBankPermissions(): void
    {
        $questionBankPermissions = [
            // Subject management
            ['name' => 'subjects.view', 'display_name' => 'View Subjects', 'module' => 'question_bank'],
            ['name' => 'subjects.create', 'display_name' => 'Create Subjects', 'module' => 'question_bank'],
            ['name' => 'subjects.edit', 'display_name' => 'Edit Subjects', 'module' => 'question_bank'],
            ['name' => 'subjects.delete', 'display_name' => 'Delete Subjects', 'module' => 'question_bank'],

            // Unit management
            ['name' => 'units.view', 'display_name' => 'View Units', 'module' => 'question_bank'],
            ['name' => 'units.create', 'display_name' => 'Create Units', 'module' => 'question_bank'],
            ['name' => 'units.edit', 'display_name' => 'Edit Units', 'module' => 'question_bank'],
            ['name' => 'units.delete', 'display_name' => 'Delete Units', 'module' => 'question_bank'],

            // Topic management
            ['name' => 'topics.view', 'display_name' => 'View Topics', 'module' => 'question_bank'],
            ['name' => 'topics.create', 'display_name' => 'Create Topics', 'module' => 'question_bank'],
            ['name' => 'topics.edit', 'display_name' => 'Edit Topics', 'module' => 'question_bank'],
            ['name' => 'topics.delete', 'display_name' => 'Delete Topics', 'module' => 'question_bank'],

            // Bloom's Taxonomy management
            ['name' => 'blooms_taxonomy.view', 'display_name' => 'View Bloom\'s Taxonomy', 'module' => 'question_bank'],
            ['name' => 'blooms_taxonomy.create', 'display_name' => 'Create Bloom\'s Taxonomy', 'module' => 'question_bank'],
            ['name' => 'blooms_taxonomy.edit', 'display_name' => 'Edit Bloom\'s Taxonomy', 'module' => 'question_bank'],
            ['name' => 'blooms_taxonomy.delete', 'display_name' => 'Delete Bloom\'s Taxonomy', 'module' => 'question_bank'],

            // Question management
            ['name' => 'questions.view', 'display_name' => 'View Questions', 'module' => 'question_bank'],
            ['name' => 'questions.create', 'display_name' => 'Create Questions', 'module' => 'question_bank'],
            ['name' => 'questions.edit', 'display_name' => 'Edit Questions', 'module' => 'question_bank'],
            ['name' => 'questions.delete', 'display_name' => 'Delete Questions', 'module' => 'question_bank'],
            ['name' => 'questions.import', 'display_name' => 'Import Questions', 'module' => 'question_bank'],
            ['name' => 'questions.export', 'display_name' => 'Export Questions', 'module' => 'question_bank'],

            // Blueprint management
            ['name' => 'blueprints.view', 'display_name' => 'View Blueprints', 'module' => 'question_bank'],
            ['name' => 'blueprints.create', 'display_name' => 'Create Blueprints', 'module' => 'question_bank'],
            ['name' => 'blueprints.edit', 'display_name' => 'Edit Blueprints', 'module' => 'question_bank'],
            ['name' => 'blueprints.delete', 'display_name' => 'Delete Blueprints', 'module' => 'question_bank'],

            // Question paper management
            ['name' => 'question_papers.view', 'display_name' => 'View Question Papers', 'module' => 'question_bank'],
            ['name' => 'question_papers.create', 'display_name' => 'Create Question Papers', 'module' => 'question_bank'],
            ['name' => 'question_papers.edit', 'display_name' => 'Edit Question Papers', 'module' => 'question_bank'],
            ['name' => 'question_papers.delete', 'display_name' => 'Delete Question Papers', 'module' => 'question_bank'],
            ['name' => 'question_papers.generate', 'display_name' => 'Generate Question Papers', 'module' => 'question_bank'],
            ['name' => 'question_papers.publish', 'display_name' => 'Publish Question Papers', 'module' => 'question_bank'],
            ['name' => 'question_papers.export', 'display_name' => 'Export Question Papers', 'module' => 'question_bank'],
        ];

        foreach ($questionBankPermissions as $permission) {
            Permission::create($permission);
        }
    }

    /**
     * Create permissions for User Management
     */
    private function createUserManagementPermissions(): void
    {
        $userManagementPermissions = [
            // User management
            ['name' => 'users.view', 'display_name' => 'View Users', 'module' => 'user_management'],
            ['name' => 'users.create', 'display_name' => 'Create Users', 'module' => 'user_management'],
            ['name' => 'users.edit', 'display_name' => 'Edit Users', 'module' => 'user_management'],
            ['name' => 'users.delete', 'display_name' => 'Delete Users', 'module' => 'user_management'],

            // Role management
            ['name' => 'roles.view', 'display_name' => 'View Roles', 'module' => 'user_management'],
            ['name' => 'roles.create', 'display_name' => 'Create Roles', 'module' => 'user_management'],
            ['name' => 'roles.edit', 'display_name' => 'Edit Roles', 'module' => 'user_management'],
            ['name' => 'roles.delete', 'display_name' => 'Delete Roles', 'module' => 'user_management'],

            // Permission management
            ['name' => 'permissions.view', 'display_name' => 'View Permissions', 'module' => 'user_management'],
            ['name' => 'permissions.assign', 'display_name' => 'Assign Permissions', 'module' => 'user_management'],
        ];

        foreach ($userManagementPermissions as $permission) {
            Permission::create($permission);
        }
    }

    /**
     * Create permissions for Reporting
     */
    private function createReportingPermissions(): void
    {
        $reportingPermissions = [
            // Dashboard
            ['name' => 'dashboard.view', 'display_name' => 'View Dashboard', 'module' => 'reporting'],

            // Reports
            ['name' => 'reports.seating_plans', 'display_name' => 'Seating Plan Reports', 'module' => 'reporting'],
            ['name' => 'reports.invigilators', 'display_name' => 'Invigilator Reports', 'module' => 'reporting'],
            ['name' => 'reports.question_papers', 'display_name' => 'Question Paper Reports', 'module' => 'reporting'],
            ['name' => 'reports.students', 'display_name' => 'Student Reports', 'module' => 'reporting'],
            ['name' => 'reports.export', 'display_name' => 'Export Reports', 'module' => 'reporting'],
        ];

        foreach ($reportingPermissions as $permission) {
            Permission::create($permission);
        }
    }

    /**
     * Assign permissions to roles
     */
    private function assignPermissionsToRoles($adminRole, $examCoordinatorRole, $invigilatorRole, $studentRole): void
    {
        // Admin gets all permissions
        $allPermissions = Permission::all();
        $adminRole->permissions()->attach($allPermissions->pluck('id')->toArray());

        // Exam Coordinator permissions
        $examCoordinatorPermissions = Permission::whereIn('module', ['seat_plan', 'question_bank', 'reporting'])
            ->where('name', 'not like', '%.delete')
            ->where('name', 'not like', 'users.%')
            ->where('name', 'not like', 'roles.%')
            ->where('name', 'not like', 'permissions.%')
            ->get();
        $examCoordinatorRole->permissions()->attach($examCoordinatorPermissions->pluck('id')->toArray());

        // Invigilator permissions
        $invigilatorPermissions = Permission::whereIn('name', [
            'seating_plans.view', 'seating_plans.export',
            'rooms.view', 'students.view',
            'invigilators.view',
            'dashboard.view',
            'reports.seating_plans', 'reports.invigilators',
        ])->get();
        $invigilatorRole->permissions()->attach($invigilatorPermissions->pluck('id')->toArray());

        // Student permissions
        $studentPermissions = Permission::whereIn('name', [
            'seating_plans.view',
            'question_papers.view',
        ])->get();
        $studentRole->permissions()->attach($studentPermissions->pluck('id')->toArray());
    }
}

