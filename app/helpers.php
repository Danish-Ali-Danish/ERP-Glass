<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

if (!function_exists('hasPermission')) {
    function hasPermission($routeName)
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        // User ka role name
        $roleName = $user->role;

        // roles table se role record
        $role = DB::table('roles')->where('name', $roleName)->first();
        if (!$role) {
            return false;
        }

        // role ki permissions
        $permissionIds = DB::table('permission_role')
            ->where('role_id', $role->id)
            ->pluck('permission_id');

        // route check
        return DB::table('permission_router')
            ->whereIn('permission_id', $permissionIds)
            ->where('route_name', $routeName)
            ->exists();
    }
}
