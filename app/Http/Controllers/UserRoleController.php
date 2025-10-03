<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class UserRoleController extends Controller
{
   public function index(Request $request)
{
    if ($request->ajax()) {
        $data = DB::table('users')
            ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
            ->leftJoin('roles', 'role_user.role_id', '=', 'roles.id')
            ->select(
                'users.id as user_id',
                'users.name as user_name',
                'roles.name as role_name',
                'role_user.status',
                'role_user.id as pivot_id'
            );

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('role', function($row){
                return $row->role_name ?? '<span class="badge bg-secondary">No Role</span>';
            })
            ->addColumn('status', function($row){
                return $row->status ?? '<span class="badge bg-danger">Inactive</span>';
            })
            ->addColumn('action', function($row){
                return '
                    <button class="btn btn-sm btn-info editRole" data-id="'.$row->pivot_id.'" data-user="'.$row->user_id.'">Edit</button>
                    <button class="btn btn-sm btn-danger deleteRole" data-id="'.$row->pivot_id.'">Delete</button>
                ';
            })
            ->rawColumns(['role','status','action'])
            ->make(true);
    }

    $roles = Role::all();
    return view('user_roles.index', compact('roles'));
}

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
            'status'  => 'required|in:active,inactive'
        ]);

        DB::table('role_user')->insert([
            'user_id'    => $request->user_id,
            'role_id'    => $request->role_id,
            'status'     => $request->status,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json(['success' => 'User role assigned successfully!']);
    }

    public function show($id)
    {
        $data = DB::table('role_user')
            ->join('users', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->select('role_user.*', 'users.name as user_name', 'roles.id as role_id')
            ->where('role_user.id', $id)
            ->first();

        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
            'status'  => 'required|in:active,inactive'
        ]);

        DB::table('role_user')->where('id', $id)->update([
            'user_id'    => $request->user_id,
            'role_id'    => $request->role_id,
            'status'     => $request->status,
            'updated_at' => now()
        ]);

        return response()->json(['success' => 'User role updated successfully!']);
    }

    public function destroy($id)
    {
        DB::table('role_user')->where('id', $id)->delete();
        return response()->json(['success' => 'User role deleted successfully!']);
    }

    public function searchUsers(Request $request)
    {
        $q = $request->q;
        $users = User::where('name', 'like', "%$q%")
            ->orWhere('email', 'like', "%$q%")
            ->select('id', DB::raw("CONCAT(name, ' (', email, ')') as text"))
            ->limit(10)->get();

        return response()->json(['results' => $users]);
    }
}
