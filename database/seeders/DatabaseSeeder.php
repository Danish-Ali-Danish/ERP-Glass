<?php

namespace Database\Seeders;

use App\Models\Lpo;
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
            
        ]);
    }
}
