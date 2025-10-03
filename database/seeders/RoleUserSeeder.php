<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class RoleUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            // role name user table se lo (e.g. GM, Sales, Accounts)
            $role = Role::where('name', $user->role)->first();

            if ($role) {
                DB::table('role_user')->insertOrIgnore([
                    'role_id'    => $role->id,
                    'user_id'    => $user->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
