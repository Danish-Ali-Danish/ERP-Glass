<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;

class RoleController extends Controller
{
    /**
     * Show roles with permissions.
     */
    
    public function index(Request $request)
    {
        // Get roles with permissions
        $roles = Role::with('permissions')->get();

        // Roles array for JS
        $rolesArray = $roles->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions->pluck('id')->toArray(),
            ];
        });

        // Group permissions
        $permissions = Permission::all()->groupBy('group');
        $permissionGroups = $permissions->map(function ($group, $name) {
            return [
                'name' => $name,
                'permissions' => $group->map(fn($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                ])->toArray(),
            ];
        })->values();

        return view('roles.index', [
            'roles' => $roles,
            'rolesArray' => $rolesArray,
            'permissionGroups' => $permissionGroups,
        ]);
    }

    /**
     * Store new role.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'array',
        ]);

        $role = Role::create(['name' => $request->name]);
        $role->permissions()->sync($request->permissions ?? []);

        return $this->respondSuccess($request, 'Role created successfully.', $role);
    }

    /**
     * Update role.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'array',
        ]);

        $role->update(['name' => $request->name]);
        $role->permissions()->sync($request->permissions ?? []);

        return $this->respondSuccess($request, 'Role updated successfully.', $role);
    }

    /**
     * Delete role.
     */
    public function destroy(Request $request, Role $role)
    {
        try {
            $role->delete();
            return $this->respondSuccess($request, 'Role deleted successfully.');
        } catch (\Exception $e) {
            return $this->respondFail($request, 'Unable to delete role. It may be in use.');
        }
    }

    /**
     * Helpers
     */
    protected function respondSuccess(Request $request, $message, $data = null)
    {
        if ($request->wantsJson()) {
            return response()->json(['status' => 'ok', 'message' => $message, 'data' => $data]);
        }
        return redirect()->back()->with('success', $message);
    }

    protected function respondFail(Request $request, $message, $status = 422)
    {
        if ($request->wantsJson()) {
            return response()->json(['status' => 'error', 'message' => $message], $status);
        }
        return redirect()->back()->with('error', $message);
    }
}
