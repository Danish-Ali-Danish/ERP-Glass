<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {

        $this->call([
            ItemSeeder::class,
            DepartmentSeeder::class,
            RequisitionSeeder::class,
            LpoSeeder::class,
            SifSeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
            RoleUserSeeder::class,
            UsersTableSeeder::class,
            RolePermissionSeeder::class,
            PermissionRouterSeeder::class,
            QuotationSeeder::class,
            QuotationItemSeeder::class,
            
        ]);
    }
}
