<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        // Roles pehle find ya create kar lo
        $gm = Role::firstOrCreate(['name' => 'GM']);
        $sales = Role::firstOrCreate(['name' => 'Sales']);
        $accounts = Role::firstOrCreate(['name' => 'Accounts']);
        $production = Role::firstOrCreate(['name' => 'Production']);
        $stores = Role::firstOrCreate(['name' => 'Stores']);
        $estimation = Role::firstOrCreate(['name' => 'Estimation']);

        // Users create karo with role_id
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role_id' => $gm->id,
        ]);

        User::create([
            'name' => 'Sales User',
            'email' => 'sales@example.com',
            'password' => Hash::make('password'),
            'role_id' => $sales->id,
        ]);

        User::create([
            'name' => 'Accounts User',
            'email' => 'accounts@example.com',
            'password' => Hash::make('password'),
            'role_id' => $accounts->id,
        ]);

        User::create([
            'name' => 'Production User',
            'email' => 'production@example.com',
            'password' => Hash::make('password'),
            'role_id' => $production->id,
        ]);

        User::create([
            'name' => 'Stores User',
            'email' => 'stores@example.com',
            'password' => Hash::make('password'),
            'role_id' => $stores->id,
        ]);

        User::create([
            'name' => 'Estimation User',
            'email' => 'estimation@example.com',
            'password' => Hash::make('password'),
            'role_id' => $estimation->id,
        ]);
    }
}
