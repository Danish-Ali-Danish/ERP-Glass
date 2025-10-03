<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class PermissionRouterSeeder extends Seeder
{
    public function run(): void
    {
        $routes = [
            // Dashboard
            'view-dashboard' => ['dashboard'],

            // Departments
            'view-departments'   => ['departments.index', 'departments.show'],
            'create-departments' => ['departments.create', 'departments.store'],
            'edit-departments'   => ['departments.edit', 'departments.update'],
            'delete-departments' => ['departments.destroy'],

            // Items
            'view-items'   => ['items.index', 'items.show'],
            'create-items' => ['items.create', 'items.store'],
            'edit-items'   => ['items.edit', 'items.update'],
            'delete-items' => ['items.destroy'],

            // Requisitions
            'view-requisitions'   => ['requisitions.index', 'requisitions.show'],
            'create-requisitions' => ['requisitions.create', 'requisitions.store'],
            'edit-requisitions'   => ['requisitions.edit', 'requisitions.update'],
            'delete-requisitions' => ['requisitions.destroy'],

            // LPOs
            'view-lpos'   => ['lpos.index', 'lpos.show'],
            'create-lpos' => ['lpos.create', 'lpos.store'],
            'edit-lpos'   => ['lpos.edit', 'lpos.update'],
            'delete-lpos' => ['lpos.destroy'],

            // GRNs
            'view-grns'   => ['grns.index', 'grns.show'],
            'create-grns' => ['grns.create', 'grns.store'],
            'edit-grns'   => ['grns.edit', 'grns.update'],
            'delete-grns' => ['grns.destroy'],

            // SIFs
            'view-sifs'   => ['sifs.index', 'sifs.show'],
            'create-sifs' => ['sifs.create', 'sifs.store'],
            'edit-sifs'   => ['sifs.edit', 'sifs.update'],
            'delete-sifs' => ['sifs.destroy'],

            // Work Orders
            'view-workorders'   => ['workorders.index', 'workorders.show'],
            'create-workorders' => ['workorders.create', 'workorders.store'],
            'edit-workorders'   => ['workorders.edit', 'workorders.update'],
            'delete-workorders' => ['workorders.destroy'],

            // Quotations
            'view-quotations'   => ['quotations.index', 'quotations.show'],
            'create-quotations' => ['quotations.create', 'quotations.store'],
            'edit-quotations'   => ['quotations.edit', 'quotations.update'],
            'delete-quotations' => ['quotations.destroy'],

            // ✅ Users
            'view-users'   => ['users.index', 'users.show'],
            'create-users' => ['users.create', 'users.store'],
            'edit-users'   => ['users.edit', 'users.update', 'users.reset-password'],
            'delete-users' => ['users.destroy'],

            // ✅ User Roles / Designations
            'view-user-roles'   => ['user.roles.index', 'user.roles.show'],
            'create-user-roles' => ['user.roles.store'],
            'edit-user-roles'   => ['user.roles.update'],
            'delete-user-roles' => ['user.roles.destroy'],

            // ✅ Roles
            'view-roles'   => ['roles.index', 'roles.show'],
            'create-roles' => ['roles.create', 'roles.store'],
            'edit-roles'   => [
                'roles.edit', 
                'roles.update',
                'roles.assignUsers',
                'roles.assignRoleToUser',
                'roles.updatePermissions'
            ],
            'delete-roles' => ['roles.destroy'],
            'remove-role-users' => ['roles.removeUser'], // extra for removing users from role

            // ✅ Permissions
            'view-permissions'   => ['permissions.index'],
            'create-permissions' => ['permissions.store'],
            'edit-permissions'   => ['permissions.update', 'permissions.addRouter'],
            'delete-permissions' => ['permissions.destroy', 'permissions.removeRouter'],
        ];

        foreach ($routes as $permissionName => $routeNames) {
            $permission = Permission::where('name', $permissionName)->first();

            if ($permission) {
                foreach ($routeNames as $route) {
                    DB::table('permission_router')->insertOrIgnore([
                        'permission_id' => $permission->id,
                        'route_name'    => $route,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                }
            }
        }
    }
}
