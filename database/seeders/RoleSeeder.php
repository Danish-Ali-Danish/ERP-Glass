<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        $superAdmin = Role::firstOrCreate(
            ['name' => 'super-admin'],
            ['display_name' => 'Super Admin']
        );
        $superAdmin->permissions()->sync(Permission::pluck('id')->toArray());

        // Admin
        $admin = Role::firstOrCreate(
            ['name' => 'admin'],
            ['display_name' => 'Admin']
        );
        $admin->permissions()->sync(Permission::pluck('id')->toArray()); // agar restrict karna hai to filter lagado

        // User (read only)
        $user = Role::firstOrCreate(
            ['name' => 'user'],
            ['display_name' => 'User']
        );
        $viewPermissions = Permission::where('name', 'like', 'view-%')->pluck('id')->toArray();
        $user->permissions()->sync($viewPermissions);
    }
}
