<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    /**
     * Display roles management page
     */
    public function index()
    {
        try {
            Log::info('RoleController index method called');
            
            $roles = Role::with(['users', 'permissions'])->get();
            $allUsers = User::all();
            $permissions = Permission::all();

            // Transform roles for JS
            $rolesArray = $roles->map(function ($role) {
                return [
                    'id'          => $role->id,
                    'name'        => $role->name,
                    'permissions' => $role->permissions->pluck('id')->toArray(),
                ];
            });

            // Group permissions by 'group' column
            $permissionGroups = $permissions->groupBy('group')->map(function ($items, $key) {
                return [
                    'name'        => $key,
                    'permissions' => $items->map(fn($perm) => [
                        'id'   => $perm->id,
                        'name' => $perm->name,
                    ])->toArray(),
                ];
            })->values()->toArray();

            return view('roles.index', compact('roles', 'allUsers', 'permissionGroups', 'rolesArray'));

        } catch (\Exception $e) {
            Log::error('Error in RoleController: ' . $e->getMessage());
            return response()->view('errors.500', [], 500);
        }
    }

    /**
     * Store a newly created role
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        DB::transaction(function () use ($request) {
            $role = Role::create([
                'name' => $request->name
            ]);

            if ($request->has('permissions')) {
                $role->permissions()->sync($request->permissions);
            }
        });

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Assign role to multiple users (replace role_id)
     */
    public function assignRoleToUser(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        foreach ($request->user_ids as $userId) {
            $user = User::findOrFail($userId);
            $user->role_id = $request->role_id; // simple assign
            $user->save();
        }

        return redirect()->back()
            ->with('success', 'Role assigned to users successfully.');
    }

    /**
     * Remove user from role (set role_id null)
     */
    public function removeUserFromRole(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'user_id' => 'required|exists:users,id'
        ]);

        $user = User::findOrFail($request->user_id);
        if ($user->role_id == $request->role_id) {
            $user->role_id = null;
            $user->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'User removed from role successfully.'
        ]);
    }

    /**
     * Update role permissions
     */
    public function updateRolePermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role->permissions()->sync($request->permissions);

        return response()->json([
            'status' => 'success',
            'message' => 'Role permissions updated successfully.'
        ]);
    }
    public function update(Request $request, $id)
{
    $role = Role::findOrFail($id);

    $request->validate([
        'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
        'permissions' => 'array',
        'permissions.*' => 'exists:permissions,id'
    ]);

    DB::transaction(function () use ($request, $role) {
        $role->update([
            'name' => $request->name
        ]);

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        } else {
            $role->permissions()->detach();
        }
    });

    return redirect()->route('roles.index')
        ->with('success', 'Role updated successfully.');
}
    /**
     * Remove the specified role
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        // Detach all permissions
        $role->permissions()->detach();

        // Set role_id to null for all users with this role
        User::where('role_id', $role->id)->update(['role_id' => null]);

        // Delete the role
        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    /**
     * Get permissions for a specific role
     */
    public function getRolePermissions(Role $role)
    {
        $permissions = $role->permissions()->pluck('id')->toArray();

        return response()->json([
            'status' => 'success',
            'permissions' => $permissions
        ]);
    }
}
