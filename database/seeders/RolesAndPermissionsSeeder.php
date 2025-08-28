<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            ['name' => 'edit articles', 'display_name' => 'Edit Articles', 'group' => 'articles'],
            ['name' => 'delete articles', 'display_name' => 'Delete Articles', 'group' => 'articles'],
            ['name' => 'publish articles', 'display_name' => 'Publish Articles', 'group' => 'articles'],
            ['name' => 'unpublish articles', 'display_name' => 'Unpublish Articles', 'group' => 'articles'],
            ['name' => 'read-products', 'display_name' => 'Read Products', 'group' => 'system'],
            ['name' => 'read-orders', 'display_name' => 'Read Orders', 'group' => 'system'],
            ['name' => 'create_employees', 'display_name' => 'Create Employees', 'group' => 'employees'],
            ['name' => 'read_employees', 'display_name' => 'Read Employees', 'group' => 'employees'],
            ['name' => 'update_employees', 'display_name' => 'Update Employees', 'group' => 'employees'],
            ['name' => 'delete_employees', 'display_name' => 'Delete Employees', 'group' => 'employees'],
        ];

        // Create or update permissions
        foreach ($permissions as $perm) {
            Permission::updateOrCreate(
                ['name' => $perm['name'], 'guard_name' => 'web'],
                ['display_name' => $perm['display_name'], 'group' => $perm['group']]
            );
        }

        // Create or update roles
        $adminRole = Role::updateOrCreate(
            ['name' => 'admin', 'guard_name' => 'web'],
            ['display_name' => 'المدير العام']
        );
        $employeeRole = Role::updateOrCreate(
            ['name' => 'employee', 'guard_name' => 'web'],
            ['display_name' => 'الموظف']
        );

        // Assign all permissions to admin
        $adminRole->syncPermissions(Permission::all());

        // Assign specific permissions to employee
        $employeeRole->syncPermissions([
            'read-products',
            'read-orders',
            'create_employees',
            'read_employees',
            'update_employees',
            'delete_employees',
        ]);

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@g.c'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'is_admin' => true
            ]
        );
        $admin->assignRole($adminRole);
    }
}