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

        $routeName = $request->route()->getName();
        $roleName = $user->role;

        $role = DB::table('roles')->where('name', $roleName)->first();

        if (!$role) {
            return redirect()->route('auth-404');
        }

        $permissionIds = DB::table('permission_role')
            ->where('role_id', $role->id)
            ->pluck('permission_id')
            ->toArray();

        if (empty($permissionIds)) {
            return redirect()->route('auth-404');
        }

        $hasAccess = DB::table('permission_router')
            ->whereIn('permission_id', $permissionIds)
            ->where('route_name', $routeName)
            ->exists();

        if (!$hasAccess) {
            return redirect()->route('auth-404');
        }

        return $next($request);
    }
}
