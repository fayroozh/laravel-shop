<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Create Roles
        $roles = [
            ['name' => 'super_admin', 'display_name' => 'Super Admin', 'description' => 'Has access to everything'],
            ['name' => 'sales_manager', 'display_name' => 'Sales Manager', 'description' => 'Manages products and orders'],
            ['name' => 'hr_manager', 'display_name' => 'HR Manager', 'description' => 'Manages employees'],
            ['name' => 'accountant', 'display_name' => 'Accountant', 'description' => 'Manages reports and orders']
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }

        // Create Permissions
        $permissionGroups = [
            'products' => [
                ['name' => 'view_products', 'display_name' => 'View Products'],
                ['name' => 'create_products', 'display_name' => 'Create Products'],
                ['name' => 'edit_products', 'display_name' => 'Edit Products'],
                ['name' => 'delete_products', 'display_name' => 'Delete Products']
            ],
            'employees' => [
                ['name' => 'view_employees', 'display_name' => 'View Employees'],
                ['name' => 'create_employees', 'display_name' => 'Create Employees'],
                ['name' => 'edit_employees', 'display_name' => 'Edit Employees'],
                ['name' => 'delete_employees', 'display_name' => 'Delete Employees']
            ],
            'orders' => [
                ['name' => 'view_orders', 'display_name' => 'View Orders'],
                ['name' => 'create_orders', 'display_name' => 'Create Orders'],
                ['name' => 'edit_orders', 'display_name' => 'Edit Orders'],
                ['name' => 'delete_orders', 'display_name' => 'Delete Orders']
            ],
            'reports' => [
                ['name' => 'view_reports', 'display_name' => 'View Reports'],
                ['name' => 'generate_reports', 'display_name' => 'Generate Reports']
            ]
        ];

        foreach ($permissionGroups as $group => $permissions) {
            foreach ($permissions as $permission) {
                Permission::create([
                    'name' => $permission['name'],
                    'display_name' => $permission['display_name'],
                    'description' => $permission['display_name'],
                    'group' => $group
                ]);
            }
        }
    }
}