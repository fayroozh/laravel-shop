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
            // Product Permissions
            ['name' => 'create_products', 'display_name' => 'Create Products', 'group' => 'Products'],
            ['name' => 'edit_products', 'display_name' => 'Edit Products', 'group' => 'Products'],
            ['name' => 'delete_products', 'display_name' => 'Delete Products', 'group' => 'Products'],
            ['name' => 'read_products', 'display_name' => 'Read Products', 'group' => 'Products'],

            // Order Permissions
            ['name' => 'read_orders', 'display_name' => 'Read Orders', 'group' => 'Orders'],

            // Employee Permissions
            ['name' => 'create_employees', 'display_name' => 'Create Employees', 'group' => 'Employees'],
            ['name' => 'read_employees', 'display_name' => 'Read Employees', 'group' => 'Employees'],
            ['name' => 'update_employees', 'display_name' => 'Update Employees', 'group' => 'Employees'],
            ['name' => 'delete_employees', 'display_name' => 'Delete Employees', 'group' => 'Employees'],

            // Financial Permissions (Placeholder)
            ['name' => 'manage_finances', 'display_name' => 'Manage Finances', 'group' => 'Finances'],
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
            ['display_name' => 'Administrator']
        );
        $hrRole = Role::updateOrCreate(
            ['name' => 'human_resources', 'guard_name' => 'web'],
            ['display_name' => 'Human Resources Manager']
        );
        $productManagerRole = Role::updateOrCreate(
            ['name' => 'product_manager', 'guard_name' => 'web'],
            ['display_name' => 'Product Manager']
        );

        // Assign all permissions to admin
        $adminRole->syncPermissions(Permission::all());

        // Assign specific permissions to Human Resources
        $hrRole->syncPermissions([
            'create_employees',
            'read_employees',
            'update_employees',
            'delete_employees',
            'manage_finances',
        ]);

        // Assign specific permissions to Product Manager
        $productManagerRole->syncPermissions([
            'create_products',
            'read_products',
            'edit_products',
            'delete_products',
        ]);

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'is_admin' => true
            ]
        );
        $admin->assignRole($adminRole);
    }
}