<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use Illuminate\Support\Facades\DB;


class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        // Agar already data hai to delete karne ke liye
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Department::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Department::factory()->count(50)->create(); // 50 departments
        echo "Departments seeded.\n";
    }
}
