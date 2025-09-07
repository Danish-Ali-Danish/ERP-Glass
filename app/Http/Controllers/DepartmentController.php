<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DepartmentController extends Controller
{
    /**
     * Display a listing of departments.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Department::latest()->get();

            return DataTables::of($data)
                ->addIndexColumn() // Sr No.
                ->addColumn('action', function ($row) {
                    return '
                        <a href="javascript:void(0)" 
                           class="editBtn las la-pen text-secondary fs-18" 
                           data-id="'.$row->id.'"></a>
                        <a href="javascript:void(0)" 
                           class="deleteBtn las la-trash-alt text-secondary fs-18" 
                           data-id="'.$row->id.'"></a>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('departments.index');
    }

    /**
     * Store a newly created department.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $department = Department::create([
            'name' => $request->name
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Department created successfully.',
            'data'    => $department
        ]);
    }

    /**
     * Display the specified department (for edit).
     */
    public function show($id)
    {
        $dept = Department::find($id);

        if (!$dept) {
            return response()->json([
                'success' => false,
                'message' => 'Department not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $dept
        ]);
    }

    /**
     * Update the specified department.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $dept = Department::find($id);

        if (!$dept) {
            return response()->json([
                'success' => false,
                'message' => 'Department not found.'
            ], 404);
        }

        $dept->update([
            'name' => $request->name
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Department updated successfully.',
            'data'    => $dept
        ]);
    }

    /**
     * Remove the specified department.
     */
    public function destroy($id)
    {
        $dept = Department::find($id);

        if (!$dept) {
            return response()->json([
                'success' => false,
                'message' => 'Department not found.'
            ], 404);
        }

        $dept->delete();

        return response()->json([
            'success' => true,
            'message' => 'Department deleted successfully.'
        ]);
    }
}
