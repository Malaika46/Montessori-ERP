<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Seed the 6 system roles and core module permissions.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'name' => 'superadmin',
                'display_name' => 'Superadmin',
                'description' => 'Highest-level system administrator with total access to all modules, campuses, users, and settings.'
            ],
            [
                'name' => 'principal',
                'display_name' => 'Principal',
                'description' => 'Senior school executive administrator managing academic operations, campuses, and staff.'
            ],
            [
                'name' => 'admin',
                'display_name' => 'Admin',
                'description' => 'Administrative management role overseeing day-to-day operations, attendance, and finance.'
            ],
            [
                'name' => 'teacher',
                'display_name' => 'Teacher / Guide',
                'description' => 'Montessori Guide managing assigned classrooms, lessons, observations, and child progress reports.'
            ],
            [
                'name' => 'student',
                'display_name' => 'Student',
                'description' => 'Student account accessing personal learning journeys, lessons, and Gamified LMS.'
            ],
            [
                'name' => 'parent',
                'display_name' => 'Parent',
                'description' => 'Parent portal user accessing child attendance, released narrative reports, and fee challans.'
            ],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(['name' => $roleData['name']], $roleData);
        }

        // Core Module Permissions
        $permissions = [
            // User & System
            ['name' => 'manage-users', 'display_name' => 'Manage System Users', 'module' => 'system'],
            ['name' => 'manage-campuses', 'display_name' => 'Manage Campuses', 'module' => 'system'],
            ['name' => 'view-audit-logs', 'display_name' => 'View System Audit Logs', 'module' => 'system'],
            ['name' => 'manage-settings', 'display_name' => 'Manage Global System Settings', 'module' => 'system'],

            // Academic
            ['name' => 'manage-students', 'display_name' => 'Manage Student Records', 'module' => 'academic'],
            ['name' => 'manage-classrooms', 'display_name' => 'Manage Classrooms & Environments', 'module' => 'academic'],
            ['name' => 'manage-curriculum', 'display_name' => 'Manage Montessori Curriculum', 'module' => 'academic'],
            ['name' => 'manage-lessons', 'display_name' => 'Manage Lesson Plans', 'module' => 'academic'],
            ['name' => 'manage-observations', 'display_name' => 'Record & Review Observations', 'module' => 'academic'],
            ['name' => 'access-lms', 'display_name' => 'Access Gamified LMS', 'module' => 'academic'],
            ['name' => 'manage-assessments', 'display_name' => 'Create & Release Reports', 'module' => 'academic'],

            // Operations & Finance
            ['name' => 'manage-attendance', 'display_name' => 'Manage Attendance & Gate Pass', 'module' => 'operations'],
            ['name' => 'manage-communication', 'display_name' => 'Send School Communications', 'module' => 'operations'],
            ['name' => 'manage-inventory', 'display_name' => 'Manage Material Inventory', 'module' => 'operations'],
            ['name' => 'manage-fees', 'display_name' => 'Manage Student Fees & Vouchers', 'module' => 'finance'],
            ['name' => 'manage-payroll', 'display_name' => 'Manage Staff & Payroll', 'module' => 'people'],
        ];

        foreach ($permissions as $permData) {
            Permission::firstOrCreate(['name' => $permData['name']], $permData);
        }

        // Attach default permission mappings
        $superadmin = Role::where('name', 'superadmin')->first();
        $allPermIds = Permission::pluck('id')->toArray();
        $superadmin->permissions()->sync($allPermIds);

        $principal = Role::where('name', 'principal')->first();
        $principalPerms = Permission::whereIn('module', ['academic', 'operations', 'finance', 'people'])->pluck('id')->toArray();
        $principal->permissions()->sync($principalPerms);

        $admin = Role::where('name', 'admin')->first();
        $adminPerms = Permission::whereIn('module', ['academic', 'operations', 'finance'])->pluck('id')->toArray();
        $admin->permissions()->sync($adminPerms);

        $teacher = Role::where('name', 'teacher')->first();
        $teacherPerms = Permission::whereIn('name', ['manage-lessons', 'manage-observations', 'access-lms', 'manage-assessments', 'manage-attendance'])->pluck('id')->toArray();
        $teacher->permissions()->sync($teacherPerms);

        $student = Role::where('name', 'student')->first();
        $studentPerms = Permission::whereIn('name', ['access-lms'])->pluck('id')->toArray();
        $student->permissions()->sync($studentPerms);

        $parent = Role::where('name', 'parent')->first();
        $parentPerms = Permission::whereIn('name', ['manage-communication'])->pluck('id')->toArray();
        $parent->permissions()->sync($parentPerms);
    }
}
