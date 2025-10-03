<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    public function search(Request $request)
    {
        $q = $request->get('q');

        $users = User::query()
            ->when($q, fn($query) =>
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
            )
            ->select('id', 'name', 'email')
            ->limit(10)
            ->get();

        return response()->json($users); // ✅ JSON only
    }

    /**
     * Display listing of users with DataTable.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = User::latest()->get();

            return datatables()->of($users)
                ->addIndexColumn()

                ->addColumn('image', function ($row) {
                    $url = $row->image
                        ? asset('uploads/users/' . $row->image)
                        : asset('default.png');

                    return '<img src="' . $url . '"
                             width="40" height="40"
                             class=" userImage"
                             style="cursor:pointer">';
                })
                ->addColumn('action', function ($row) {
    $buttons = '';

    if (hasPermission('users.edit')) {
        $buttons .= '<a class=" text-secondary fs-18 editUser" data-id="' . $row->id . '" title="Edit"><i class="las la-pen"></i></a>';
    }

    if (hasPermission('users.destroy')) {
        $buttons .= '<a class="text-secondary fs-18 deleteUser p-1" data-id="' . $row->id . '" title="Delete"><i class="las la-trash "></i></a>';
    }

    if (hasPermission('users.reset-password')) {
        $buttons .= '<a class="text-secondary fs-18 resetPassword " data-id="' . $row->id . '" title="Reset Password"><i class="las la-key"></i></a>';
    }

    return $buttons;
})

                ->rawColumns(['image', 'action'])
                ->make(true);
        }

        return view('users.index');
    }

    /**
     * Store new user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
            'phone'    => 'required|numeric',
            'address'  => 'required|string',
            'image'    => 'required|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        $imageName = null;
        if ($request->hasFile('image')) {
            $imageName = time() . '_' . $request->image->getClientOriginalName();
            $request->image->move(public_path('uploads/users'), $imageName);
        }

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'address'  => $request->address,
            'image'    => $imageName,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['success' => 'User created successfully.']);
    }

    /**
     * Show single user for edit.
     */
    public function show(User $user)
    {
        return response()->json($user);
    }

    /**
     * Update user details (no password here).
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'    => 'required|string',
            'email'   => 'required|email|unique:users,email,' . $user->id,
            'phone'   => 'required|numeric',
            'address' => 'required|string',
            'image'   => 'required|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        // handle image upload
        if ($request->hasFile('image')) {
            if ($user->image && file_exists(public_path('uploads/users/' . $user->image))) {
                unlink(public_path('uploads/users/' . $user->image));
            }

            $imageName = time() . '_' . $request->image->getClientOriginalName();
            $request->image->move(public_path('uploads/users'), $imageName);
            $user->image = $imageName;
        }

        // update basic fields
        $user->name    = $request->name;
        $user->email   = $request->email;
        $user->phone   = $request->phone;
        $user->address = $request->address;
        $user->save();

        return response()->json(['success' => 'User updated successfully.']);
    }

    /**
     * Delete user + image.
     */
    public function destroy(User $user)
    {
        if ($user->image && file_exists(public_path('uploads/users/' . $user->image))) {
            unlink(public_path('uploads/users/' . $user->image));
        }

        $user->delete();
        return response()->json(['success' => 'User deleted successfully.']);
    }

    /**
     * Reset password (separate modal).
     */
    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['success' => 'Password reset successfully.']);
    }
}

