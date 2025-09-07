<?php

namespace App\Http\Controllers;

use App\Models\MaterialRequisition;
use App\Models\Department;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class MaterialRequisitionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = MaterialRequisition::with('department')->latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('department', fn($row) => $row->department->name ?? '-')
                ->addColumn('action', function ($row) {
                    return '
                        <button class="btn btn-sm btn-info editBtn" data-id="'.$row->id.'">Edit</button>
                        <button class="btn btn-sm btn-danger deleteBtn" data-id="'.$row->id.'">Delete</button>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $departments = Department::all();
        return view('material_requisitions.index', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'req_no' => 'required|unique:material_requisitions,req_no',
            'req_date' => 'required|date',
            'department_id' => 'required|exists:departments,id',
            'project_name' => 'required|string|max:255',
            'requested_by' => 'required|string|max:255',
            'delivery_date' => 'nullable|date',
            'remarks' => 'nullable|string',
        ]);

        MaterialRequisition::create($request->all());
        return response()->json(['success' => true, 'message' => 'Material Requisition created successfully']);
    }

    public function edit($id)
    {
        return MaterialRequisition::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $requisition = MaterialRequisition::findOrFail($id);

        $request->validate([
            'req_no' => 'required|unique:material_requisitions,req_no,'.$id,
            'req_date' => 'required|date',
            'department_id' => 'required|exists:departments,id',
            'project_name' => 'required|string|max:255',
            'requested_by' => 'required|string|max:255',
            'delivery_date' => 'nullable|date',
            'remarks' => 'nullable|string',
        ]);

        $requisition->update($request->all());
        return response()->json(['success' => true, 'message' => 'Material Requisition updated successfully']);
    }

    public function destroy($id)
    {
        $requisition = MaterialRequisition::findOrFail($id);
        $requisition->delete();
        return response()->json(['success' => true, 'message' => 'Material Requisition deleted successfully']);
    }
}
