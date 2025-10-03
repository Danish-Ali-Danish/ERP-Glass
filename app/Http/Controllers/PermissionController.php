<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\PermissionRouter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    /**
     * Display permissions management page
     */
    public function index()
    {
        
        $permissions = Permission::with('router')->get();
        return view('permissions.index', compact('permissions'));
    }

    /**
     * Store a newly created permission
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'router' => 'array',
            'router.*' => 'string'
        ]);

        DB::transaction(function () use ($request) {
            $permission = Permission::create([
                'name' => $request->name
            ]);

            if ($request->has('router')) {
                foreach ($request->router as $router) {
                    PermissionRouter::create([
                        'permission_id' => $permission->id,
                        'router' => $router
                    ]);
                }
            }
        });

        return redirect()->route('permissions.index')
            ->with('success', 'Permission created successfully.');
    }

    /**
     * Update the specified permission
     */
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
            'router' => 'array',
            'router.*' => 'string'
        ]);

        DB::transaction(function () use ($request, $permission) {
            $permission->update([
                'name' => $request->name
            ]);

            // Update router
            if ($request->has('router')) {
                $permission->router()->delete();
                
                foreach ($request->router as $router) {
                    PermissionRouter::create([
                        'permission_id' => $permission->id,
                        'router' => $router
                    ]);
                }
            }
        });

        return redirect()->route('permissions.index')
            ->with('success', 'Permission updated successfully.');
    }

    /**
     * Remove the specified permission
     */
    public function destroy(Permission $permission)
    {
        DB::transaction(function () use ($permission) {
            $permission->router()->delete();
            $permission->roles()->detach();
            $permission->delete();
        });

        return redirect()->route('permissions.index')
            ->with('success', 'Permission deleted successfully.');
    }

    /**
     * Add router to permission
     */
    public function addRouter(Request $request, Permission $permission)
    {
        $request->validate([
            'router' => 'required|string'
        ]);

        PermissionRouter::create([
            'permission_id' => $permission->id,
            'router' => $request->router
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Router added to permission successfully.'
        ]);
    }

    /**
     * Remove router from permission
     */
    public function removeRouter(PermissionRouter $permissionRouter)
    {
        $permissionRouter->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Router removed from permission successfully.'
        ]);
    }
}