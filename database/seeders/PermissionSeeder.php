<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            'dashboard'    => 'Dashboard',
            'departments'  => 'Departments',
            'items'        => 'Items',
            'requisitions' => 'Material Requisitions',
            'lpos'         => 'Local Purchase Orders',
            'grns'         => 'GRNs',
            'sifs'         => 'Stock Issuance Forms',
            'workorders'   => 'Work Orders',
            'quotations'   => 'Quotations',
            'users'        => 'Users',
            'roles'        => 'Roles',
            'permissions'  => 'Permissions',
            'user-roles'  => 'User Roles',
        ];

        $actions = [
            'view'   => 'View',
            'create' => 'Create',
            'edit'   => 'Edit',
            'delete' => 'Delete',
        ];

        foreach ($modules as $key => $group) {
            foreach ($actions as $action => $label) {
                Permission::firstOrCreate(
                    ['name' => $action . '-' . $key],
                    [
                        'group'        => $group,
                        'display_name' => $label . ' ' . $group,
                    ]
                );
            }
        }
    }
}
