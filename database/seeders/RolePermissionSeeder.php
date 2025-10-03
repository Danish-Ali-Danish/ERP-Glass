<?php
namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // GM -> all permissions
        $gm = Role::where('name', 'GM')->first();
        $gm->permissions()->sync(Permission::pluck('id')->toArray());

        // Sales -> sirf items aur quotations de dete hain
        $sales            = Role::where('name', 'Sales')->first();
        $salesPermissions = Permission::whereIn('name', [
            'view-items', 'create-items',
            'view-quotations', 'create-quotations',
        ])->pluck('id')->toArray();
        $sales->permissions()->sync($salesPermissions);

        // Accounts -> sirf LPO aur GRN
        $accounts            = Role::where('name', 'Accounts')->first();
        $accountsPermissions = Permission::whereIn('name', [
            'view-lpos', 'create-lpos',
            'view-grns', 'create-grns',
        ])->pluck('id')->toArray();
        $accounts->permissions()->sync($accountsPermissions);

        // Stores -> requisitions + stock
        $stores            = Role::where('name', 'Stores')->first();
        $storesPermissions = Permission::whereIn('name', [
            'view-requisitions', 'create-requisitions',
            'view-sifs', 'create-sifs',
        ])->pluck('id')->toArray();
        $stores->permissions()->sync($storesPermissions);

        // Production -> workorders
        $production            = Role::where('name', 'Production')->first();
        $productionPermissions = Permission::whereIn('name', [
            'view-workorders', 'create-workorders',
        ])->pluck('id')->toArray();
        $production->permissions()->sync($productionPermissions);

        // Estimation -> sirf quotations
        $estimation            = Role::where('name', 'Estimation')->first();
        $estimationPermissions = Permission::whereIn('name', [
            'view-quotations', 'create-quotations',
        ])->pluck('id')->toArray();
        $estimation->permissions()->sync($estimationPermissions);
    }
}
