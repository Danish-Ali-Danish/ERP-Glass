<?php

namespace App\Http\Controllers;

use App\Models\Grn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class GrnController extends Controller
{
    // Show all GRNs (DataTables)
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Grn::select(['id','lpo_no','supplier_name','department','project_name']);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    return '
                        <a class="text-secondary fs-18 viewBtn" data-id="'.$row->id.'">
                            <i class="las la-eye"></i>
                        </a>
                        <a class="text-secondary fs-18 editBtn" data-id="'.$row->id.'">
                            <i class="las la-pen"></i>
                        </a>
                        <a class="text-secondary fs-18 deleteBtn" data-id="'.$row->id.'">
                            <i class="las la-trash-alt"></i>
                        </a>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('grns.index');
    }

    // Show create form
    public function create()
    {
        return view('grns.create');
    }

    // Store new GRN
    public function store(Request $request)
    {
        $request->validate([
            'lpo_no' => 'required|string|max:255',
            'supplier_name' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $grn = Grn::create([
                'lpo_no' => $request->lpo_no,
                'supplier_name' => $request->supplier_name,
                'lpo_date' => $request->lpo_date,
                'supplier_code' => $request->supplier_code,
                'requested_by' => $request->requested_by,
                'inv_no' => $request->inv_no,
                'department' => $request->department,
                'inv_date' => $request->inv_date,
                'project_name' => $request->project_name,
            ]);

            foreach ($request->items as $item) {
                $grn->items()->create([
                    'item_code' => $item['code'] ?? null,
                    'description' => $item['description'],
                    'uom' => $item['uom'],
                    'quantity' => $item['quantity'],
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'GRN created successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error: '.$e->getMessage()], 500);
        }
    }

    // Show single GRN (for preview modal)
    public function show(Grn $grn)
    {
        $grn->load('items');
        return response()->json($grn);
    }

    // Show edit form
    public function edit(Grn $grn)
    {
        $grn->load('items');
        return view('grns.edit', compact('grn'));
    }

    // Update GRN
    public function update(Request $request, Grn $grn)
    {
        $request->validate([
            'lpo_no' => 'required|string|max:255',
            'supplier_name' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $grn->update([
                'lpo_no' => $request->lpo_no,
                'supplier_name' => $request->supplier_name,
                'lpo_date' => $request->lpo_date,
                'supplier_code' => $request->supplier_code,
                'requested_by' => $request->requested_by,
                'inv_no' => $request->inv_no,
                'department' => $request->department,
                'inv_date' => $request->inv_date,
                'project_name' => $request->project_name,
            ]);

            // Purane items delete karke naye insert karna
            $grn->items()->delete();
            foreach ($request->items as $item) {
                $grn->items()->create([
                    'item_code' => $item['code'] ?? null,
                    'description' => $item['description'],
                    'uom' => $item['uom'],
                    'quantity' => $item['quantity'],
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'GRN updated successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error: '.$e->getMessage()], 500);
        }
    }

    // Delete GRN
    public function destroy(Grn $grn)
    {
        $grn->delete();
        return response()->json(['message' => 'GRN deleted successfully']);
    }
}
