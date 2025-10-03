<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Roles ko fetch karna
        $gm         = Role::where('name', 'GM')->first();
        $accounts   = Role::where('name', 'Accounts')->first();
        $sales      = Role::where('name', 'Sales')->first();
        $production = Role::where('name', 'Production')->first();
        $stores     = Role::where('name', 'Stores')->first();
        $estimation = Role::where('name', 'Estimation')->first();

        // Saare permissions
        $allPermissions = Permission::pluck('id')->toArray();

        // GM → sab permissions
        if ($gm) {
            foreach ($allPermissions as $perm) {
                DB::table('permission_role')->insertOrIgnore([
                    'role_id' => $gm->id,
                    'permission_id' => $perm,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Accounts → sirf accounts related permissions + view
        if ($accounts) {
            $accountsPermissions = Permission::where('name', 'like', '%accounts%')
                                            ->orWhere('name', 'like', 'view_%')
                                            ->pluck('id')->toArray();

            foreach ($accountsPermissions as $perm) {
                DB::table('permission_role')->insertOrIgnore([
                    'role_id' => $accounts->id,
                    'permission_id' => $perm,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Sales → sirf sales related + view
        if ($sales) {
            $salesPermissions = Permission::where('name', 'like', '%sales%')
                                        ->orWhere('name', 'like', 'view_%')
                                        ->pluck('id')->toArray();

            foreach ($salesPermissions as $perm) {
                DB::table('permission_role')->insertOrIgnore([
                    'role_id' => $sales->id,
                    'permission_id' => $perm,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Production → production related + view
        if ($production) {
            $productionPermissions = Permission::where('name', 'like', '%production%')
                                            ->orWhere('name', 'like', 'view_%')
                                            ->pluck('id')->toArray();

            foreach ($productionPermissions as $perm) {
                DB::table('permission_role')->insertOrIgnore([
                    'role_id' => $production->id,
                    'permission_id' => $perm,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Stores → inventory/stores related + view
        if ($stores) {
            $storesPermissions = Permission::where('name', 'like', '%store%')
                                        ->orWhere('name', 'like', '%inventory%')
                                        ->orWhere('name', 'like', 'view_%')
                                        ->pluck('id')->toArray();

            foreach ($storesPermissions as $perm) {
                DB::table('permission_role')->insertOrIgnore([
                    'role_id' => $stores->id,
                    'permission_id' => $perm,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Estimation → estimation related + view
        if ($estimation) {
            $estimationPermissions = Permission::where('name', 'like', '%estimation%')
                                            ->orWhere('name', 'like', 'view_%')
                                            ->pluck('id')->toArray();

            foreach ($estimationPermissions as $perm) {
                DB::table('permission_role')->insertOrIgnore([
                    'role_id' => $estimation->id,
                    'permission_id' => $perm,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
