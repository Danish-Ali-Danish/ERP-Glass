<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckPermission
{
    public function handle(Request $request, Closure $next)
    {
        // Public routes jinko permission check nahi karna
        $excludedRoutes = [
            'login',
            'logout',
            'auth-404'
        ];

        if (in_array($request->route()->getName(), $excludedRoutes)) {
            return $next($request);
        }

        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Current route name
        $routeName = $request->route()->getName();

        // User role string (e.g. GM, Sales, Production)
        $roleName = $user->role;

        // Role record fetch
        $role = DB::table('roles')->where('name', $roleName)->first();

        if (!$role) {
            return abort(403, 'Role not found for user.');
        }

        // Role ki sari permissions fetch karo (IDs)
        $permissionIds = DB::table('permission_role')
            ->where('role_id', $role->id)
            ->pluck('permission_id')
            ->toArray();

        if (empty($permissionIds)) {
            return abort(403, 'No permissions assigned to this role.');
        }

        // Check if route linked with role's permissions
        $hasAccess = DB::table('permission_router')
            ->whereIn('permission_id', $permissionIds)
            ->where('route_name', $routeName)
            ->exists();

        if (!$hasAccess) {
            return abort(403, 'Unauthorized Access');
        }

        return $next($request);
    }
}
