<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\PermissionRouter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    /**
     * Display roles management page
     */
    public function index()
    {

        try {
            // Test basic data loading
            Log::info('RoleController index method called');
            
            $roles = Role::with(['users', 'permissions'])->get();
            Log::info('Roles loaded: ' . $roles->count());
            
            $allUsers = User::all();
            Log::info('Users loaded: ' . $allUsers->count());
            
            $permissions = Permission::all();
            Log::info('Permissions loaded: ' . $permissions->count());

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

            Log::info('Data processed successfully');

            // Remove permissionRouters if you're not using it
            return view('roles.index', compact('roles', 'allUsers', 'permissionGroups', 'rolesArray'));

        } catch (\Exception $e) {
            Log::error('Error in RoleController: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->view('errors.500', [], 500);
        }
    }

    /**
     * Show the form for creating a new role
     */
    public function create()
    {
        $permissions = Permission::all();
        return view('roles.create', compact('permissions'));
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
     * Display the specified role
     */
    public function show(Role $role)
    {
        $role->load(['users', 'permissions']);
        return view('roles.show', compact('role'));
    }

    /**
     * Show the form for editing the role
     */
    public function edit(Role $role)
    {
        $permissions = Permission::all();
        $role->load('permissions');
        
        return view('roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified role
     */
    public function update(Request $request, Role $role)
    {
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
    public function destroy(Role $role)
    {
        DB::transaction(function () use ($role) {
            $role->permissions()->detach();
            $role->delete();
        });

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    /**
     * Assign users to role
     */
    public function assignUsers(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        $role = Role::findOrFail($request->role_id);
        $role->users()->syncWithoutDetaching($request->user_ids);

        return response()->json([
            'status' => 'success',
            'message' => 'Users assigned to role successfully.'
        ]);
    }

    /**
     * Assign role to users (replace existing roles)
     */
    public function assignRoleToUser(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        $role = Role::findOrFail($request->role_id);

        foreach ($request->user_ids as $userId) {
            $user = User::findOrFail($userId);
            $user->roles()->sync([$role->id]);
        }

        return redirect()->back()
            ->with('success', 'Role assigned to users successfully.');
    }

    /**
     * Remove user from role
     */
    public function removeUserFromRole(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'user_id' => 'required|exists:users,id'
        ]);

        $role = Role::findOrFail($request->role_id);
        $role->users()->detach($request->user_id);

        return response()->json([
            'status' => 'success',
            'message' => 'User removed from role successfully.'
        ]);
    }

    /**
     * Get role permissions
     */
    public function getRolePermissions(Role $role)
    {
        $permissions = $role->permissions;
        
        return response()->json([
            'permissions' => $permissions
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
}