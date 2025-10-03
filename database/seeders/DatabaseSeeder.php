<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {

        $this->call([
            DepartmentSeeder::class,
            RequisitionSeeder::class,
            LpoSeeder::class,
            SifSeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
            RoleUserSeeder::class,
            PermissionRoleSeeder::class,
            UsersTableSeeder::class,
            RolePermissionSeeder::class,
            PermissionRoleSeeder::class,
            PermissionRouterSeeder::class,
        ]);
    }
}
