<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\Role;

class UserRoleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = User::with('role:id,name')
                ->select(['id', 'name', 'role_id', 'status']);

            return DataTables::of($users)
                ->addColumn('role', function ($row) {
                    return $row->role ? $row->role->name : '-'; 
                })
                ->addColumn('action', function ($row) {
                    // GM protection
                    if ($row->role && $row->role->name === 'GM') {
                        return '<span class="badge bg-success">System User</span>';
                    }

                    $buttons = '';

                    if (hasPermission('user-roles.update')) {
                        $buttons .= '<a class="las la-pen text-secondary fs-18 editRole me-2" data-id="'.$row->id.'" title="Edit"></a>';
                    }

                    if (hasPermission('user-roles.destroy')) {
                        $buttons .= '<a class="las la-trash text-secondary fs-18 deleteRole" data-id="'.$row->id.'" title="Delete"></a>';
                    }

                    return $buttons;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $roles = Role::all();
        return view('user_roles.index', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required',
            'role_id' => 'required|integer',
            'status'  => 'required'
        ]);

        User::create($request->only(['name','role_id','status']));

        return response()->json(['success' => 'User Role Added Successfully!']);
    }

    public function show($id)
    {
        $user = User::with('role')->find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return $user;
    }

    public function update(Request $request, $id)
{
    $user = User::with('role')->find($id);

    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }

    // Safe GM check (string ya relation dono handle karega)
    if ((is_object($user->role) && $user->role->name === 'GM') ||
        (is_string($user->role) && $user->role === 'GM')) {
        return response()->json(['error' => 'GM user cannot be updated'], 403);
    }

    $request->validate([
        'name'    => 'required',
        'role_id' => 'required|integer',
        'status'  => 'required'
    ]);

    $user->update($request->only(['name','role_id','status']));

    return response()->json(['success' => 'User Role Updated Successfully!']);
}


    public function destroy($id)
    {
        $user = User::with('role')->find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        if ($user->role && $user->role->name === 'GM') {
            return response()->json(['error' => 'GM user cannot be deleted.'], 403);
        }

        $user->delete();
        return response()->json(['success' => 'User Role Deleted Successfully!']);
    }
}
