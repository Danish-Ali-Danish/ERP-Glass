<?php

namespace App\Http\Controllers;

use App\Models\Requisition;
use App\Models\Department;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class RequisitionController extends Controller
{
    // Show Add New form
    public function create()
    {
        $departments = Department::all();

        // Auto-generate Req No
        $lastReq = Requisition::latest('id')->first();
        $nextNumber = $lastReq ? $lastReq->id + 1 : 1;
        $reqNo = 'REQ-' . str_pad($nextNumber, 8, '0', STR_PAD_LEFT);

        return view('requisitions.add-new', compact('departments', 'reqNo'));
    }

    // Saved Requisitions - DataTables AJAX
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Requisition::with('department', 'items')->latest();

            return DataTables::eloquent($data)
                ->addIndexColumn()
                ->addColumn('department', fn($row) => $row->department->name ?? '-')
                ->addColumn('items_count', fn($row) => $row->items->count())
                ->addColumn('action', function ($row) {
                    return '
                        <a href="javascript:void(0)" class="text-secondary fs-18 me-2 viewBtn" data-id="' . $row->id . '" title="Preview">
                            <i class="las la-eye"></i>
                        </a>
                        <a href="/requisitions/' . $row->id . '/edit" class="text-secondary fs-18 me-2 editBtn" data-id="' . $row->id . '" title="Edit">
                            <i class="las la-pen"></i>
                        </a>
                        <a href="javascript:void(0)" class="text-secondary fs-18 deleteBtn" data-id="' . $row->id . '" title="Delete">
                            <i class="las la-trash-alt"></i>
                        </a>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('requisitions.index');
    }

    // Show requisition details
    public function show($id)
    {
        $req = Requisition::with('items', 'department')->findOrFail($id);
        return response()->json([
            'req_no' => $req->req_no,
            'project_name' => $req->project_name,
            'date' => $req->date,
            'req_date' => $req->req_date,
            'requested_by' => $req->requested_by,
            'department' => $req->department->name,
            'remarks' => $req->remarks,
            'delivery_date' => $req->delivery_date,
            'items' => $req->items
        ]);
    }

    // Store requisition
    public function store(Request $request)
    {
        $lastReq = Requisition::latest('id')->first();
        $nextNumber = $lastReq ? $lastReq->id + 1 : 1;
        $reqNo = 'REQ-' . str_pad($nextNumber, 8, '0', STR_PAD_LEFT);

        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'req_date' => 'required|date',
            'requested_by' => 'required|string',
            'department_id' => 'required|exists:departments,id',
            'project_name' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.item_code' => 'required|string',
            'items.*.description' => 'required|string',
            'items.*.uom' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $requisition = DB::transaction(function () use ($request, $reqNo) {
            $req = Requisition::create([
                'req_no' => $reqNo,
                'date' => $request->date,
                'req_date' => $request->req_date,
                'requested_by' => $request->requested_by,
                'department_id' => $request->department_id,
                'project_name' => $request->project_name,
                'remarks' => $request->remarks,
                'delivery_date' => $request->delivery_date,
            ]);

            foreach ($request->items as $item) {
                $req->items()->create([
                    'item_code' => $item['item_code'],
                    'description' => $item['description'],
                    'uom' => $item['uom'],
                    'quantity' => $item['quantity'],
                ]);
            }

            return $req;
        });

        return response()->json([
            'success' => true,
            'message' => 'Requisition saved successfully.',
            'data' => [
                'id' => $requisition->id,
                'req_no' => $requisition->req_no,
                'date' => $requisition->date,
                'req_date' => $requisition->req_date,
                'requested_by' => $requisition->requested_by,
                'department' => $requisition->department->name ?? '',
                'project_name' => $requisition->project_name,
            ]
        ]);
    }

    // Edit form
    public function edit($id)
    {
        $requisition = Requisition::with('items')->findOrFail($id);
        $departments = Department::all();
        return view('requisitions.edit', compact('requisition', 'departments'));
    }

    // Update requisition
    public function update(Request $request, $id)
    {
        $requisition = Requisition::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'req_no' => 'required|unique:requisitions,req_no,' . $id,
            'date' => 'required|date',
            'req_date' => 'required|date',
            'requested_by' => 'required|string',
            'department_id' => 'required|exists:departments,id',
            'project_name' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.item_code' => 'required|string',
            'items.*.description' => 'required|string',
            'items.*.uom' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::transaction(function () use ($request, $requisition) {
            $requisition->update([
                'req_no' => $request->req_no,
                'date' => $request->date,
                'req_date' => $request->req_date,
                'requested_by' => $request->requested_by,
                'department_id' => $request->department_id,
                'project_name' => $request->project_name,
                'remarks' => $request->remarks,
                'delivery_date' => $request->delivery_date,
            ]);

            $requisition->items()->delete();

            foreach ($request->items as $item) {
                $requisition->items()->create([
                    'item_code' => $item['item_code'],
                    'description' => $item['description'],
                    'uom' => $item['uom'],
                    'quantity' => $item['quantity'],
                ]);
            }
        });

        return response()->json(['success' => true, 'message' => 'Requisition updated successfully']);
    }

    // Delete requisition
    public function destroy($id)
    {
        $req = Requisition::find($id);
        if (!$req) return response()->json(['success' => false, 'message' => 'Requisition not found'], 404);

        try {
            $req->delete();
            return response()->json(['success' => true, 'message' => 'Requisition deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Unable to delete.'], 500);
        }
    }

    // 🔎 Search Items for Selectr
public function searchItems(Request $request)
{
    $q = $request->get('q');
    $type = $request->get('type'); // 'code' or 'desc'

    $query = Item::query();

    if($q) {
        if($type === 'code') {
            $query->where('item_code', 'like', "%$q%");
        } else if($type === 'desc') {
            $query->where('description', 'like', "%$q%");
        }
    }

    $items = $query->select('id', 'item_code', 'description', 'uom')
                   ->limit(20)
                   ->get();

    return response()->json([
        'results' => $items
    ]);
}

}
