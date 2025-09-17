<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'view-dashboard', 'group' => 'Dashboard', 'display_name' => 'View Dashboard'],
            ['name' => 'view-users', 'group' => 'Users', 'display_name' => 'View Users'],
            ['name' => 'create-users', 'group' => 'Users', 'display_name' => 'Create Users'],
            ['name' => 'edit-users', 'group' => 'Users', 'display_name' => 'Edit Users'],
            ['name' => 'delete-users', 'group' => 'Users', 'display_name' => 'Delete Users'],

            ['name' => 'view-roles', 'group' => 'Roles', 'display_name' => 'View Roles'],
            ['name' => 'view-products', 'group' => 'Products', 'display_name' => 'View Products'],
            ['name' => 'view-orders', 'group' => 'Orders', 'display_name' => 'View Orders'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm['name']], $perm);
        }
    }
}
