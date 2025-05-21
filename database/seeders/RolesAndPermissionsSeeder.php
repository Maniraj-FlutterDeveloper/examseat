<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create default permissions
        $this->createPermissions();

        // Create roles and assign permissions
        $this->createRoles();

        // Assign roles to admin user
        $this->assignRolesToAdminUser();
    }

    /**
     * Create default permissions.
     *
     * @return void
     */
    private function createPermissions()
    {
        $modules = Permission::getModules();

        foreach ($modules as $moduleKey => $moduleName) {
            // Create basic CRUD permissions for each module
            $this->createPermission("view_{$moduleKey}", "View {$moduleName}", $moduleKey);
            $this->createPermission("create_{$moduleKey}", "Create {$moduleName}", $moduleKey);
            $this->createPermission("edit_{$moduleKey}", "Edit {$moduleName}", $moduleKey);
            $this->createPermission("delete_{$moduleKey}", "Delete {$moduleName}", $moduleKey);

            // Add additional permissions for specific modules
            switch ($moduleKey) {
                case 'users':
                    $this->createPermission('assign_roles', 'Assign Roles to Users', $moduleKey);
                    $this->createPermission('view_activities', 'View User Activities', $moduleKey);
                    break;

                case 'roles':
                    $this->createPermission('assign_permissions', 'Assign Permissions to Roles', $moduleKey);
                    break;

                case 'students':
                    $this->createPermission('import_students', 'Import Students', $moduleKey);
                    $this->createPermission('export_students', 'Export Students', $moduleKey);
                    break;

                case 'seating_plans':
                    $this->createPermission('generate_seating_plans', 'Generate Seating Plans', $moduleKey);
                    $this->createPermission('print_seating_plans', 'Print Seating Plans', $moduleKey);
                    break;

                case 'question_papers':
                    $this->createPermission('generate_question_papers', 'Generate Question Papers', $moduleKey);
                    $this->createPermission('print_question_papers', 'Print Question Papers', $moduleKey);
                    break;

                case 'reports':
                    $this->createPermission('generate_reports', 'Generate Reports', $moduleKey);
                    $this->createPermission('export_reports', 'Export Reports', $moduleKey);
                    break;

                case 'settings':
                    $this->createPermission('manage_system_settings', 'Manage System Settings', $moduleKey);
                    break;
            }
        }
    }

    /**
     * Create a permission.
     *
     * @param  string  $slug
     * @param  string  $name
     * @param  string  $module
     * @return \App\Models\Permission
     */
    private function createPermission($slug, $name, $module)
    {
        return Permission::create([
            'name' => $name,
            'slug' => $slug,
            'module' => $module,
        ]);
    }

    /**
     * Create roles and assign permissions.
     *
     * @return void
     */
    private function createRoles()
    {
        // Create Super Admin role
        $superAdminRole = Role::create([
            'name' => 'Super Admin',
            'slug' => 'super-admin',
            'description' => 'Super Administrator with all permissions',
            'is_system' => true,
        ]);

        // Assign all permissions to Super Admin
        $superAdminRole->permissions()->sync(Permission::all());

        // Create Admin role
        $adminRole = Role::create([
            'name' => 'Administrator',
            'slug' => 'administrator',
            'description' => 'Administrator with most permissions',
            'is_system' => true,
        ]);

        // Assign permissions to Admin (excluding some sensitive permissions)
        $adminPermissions = Permission::whereNotIn('slug', [
            'manage_system_settings',
            'delete_users',
            'delete_roles',
            'delete_permissions',
        ])->get();

        $adminRole->permissions()->sync($adminPermissions);

        // Create Invigilator role
        $invigilatorRole = Role::create([
            'name' => 'Invigilator',
            'slug' => 'invigilator',
            'description' => 'Invigilator with seating plan permissions',
            'is_system' => true,
        ]);

        // Assign permissions to Invigilator
        $invigilatorPermissions = Permission::whereIn('slug', [
            'view_seating_plans',
            'print_seating_plans',
            'view_students',
            'view_rooms',
            'view_blocks',
        ])->get();

        $invigilatorRole->permissions()->sync($invigilatorPermissions);

        // Create Examiner role
        $examinerRole = Role::create([
            'name' => 'Examiner',
            'slug' => 'examiner',
            'description' => 'Examiner with question paper permissions',
            'is_system' => true,
        ]);

        // Assign permissions to Examiner
        $examinerPermissions = Permission::whereIn('module', [
            'subjects',
            'units',
            'topics',
            'questions',
            'blooms_taxonomies',
            'blueprints',
            'question_papers',
        ])->get();

        $examinerRole->permissions()->sync($examinerPermissions);

        // Create User role
        $userRole = Role::create([
            'name' => 'User',
            'slug' => 'user',
            'description' => 'Regular user with limited permissions',
            'is_system' => true,
        ]);

        // Assign permissions to User
        $userPermissions = Permission::whereIn('slug', [
            'view_seating_plans',
            'view_question_papers',
        ])->get();

        $userRole->permissions()->sync($userPermissions);
    }

    /**
     * Assign roles to admin user.
     *
     * @return void
     */
    private function assignRolesToAdminUser()
    {
        $adminUser = User::where('email', 'admin@kalvierp.com')->first();

        if ($adminUser) {
            $superAdminRole = Role::where('slug', 'super-admin')->first();
            $adminUser->assignRole($superAdminRole);
        }
    }
}

