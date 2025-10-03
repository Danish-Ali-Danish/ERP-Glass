<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'GM'],
            ['name' => 'Sales'],
            ['name' => 'Accounts'],
            ['name' => 'Production'],
            ['name' => 'Stores'],
            ['name' => 'Estimation'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate($role);
        }
    }
}
