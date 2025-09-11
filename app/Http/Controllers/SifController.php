<?php

namespace App\Http\Controllers;

use App\Models\Sif;
use App\Models\SifItem;
use App\Models\Department;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class SifController extends Controller
{
    // Show Add New form
    public function create()
    {
        $departments = Department::all();

        // Auto-generate SIF No
        $lastSif = Sif::latest('id')->first();
        $nextNumber = $lastSif ? $lastSif->id + 1 : 1;
        $sifNo = 'SIF-' . str_pad($nextNumber, 8, '0', STR_PAD_LEFT);

        return view('sifs.create', compact('departments', 'sifNo'));
    }

    // Saved SIFs - DataTables AJAX
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Sif::with('items')->latest();

            return DataTables::eloquent($data)
                ->addIndexColumn()
                ->addColumn('department', fn($row) => $row->department ?? '-')
                ->addColumn('items_count', fn($row) => $row->items->count())
                ->addColumn('action', function ($row) {
                    return '
                        <a href="javascript:void(0)" class="text-secondary fs-18 me-2 viewBtn" data-id="' . $row->id . '" title="Preview">
                            <i class="las la-eye"></i>
                        </a>
                        <a href="/sifs/' . $row->id . '/edit" class="text-secondary fs-18 me-2 editBtn" data-id="' . $row->id . '" title="Edit">
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

        return view('sifs.index');
    }

    // Show SIF details
    public function show($id)
    {
        $sif = Sif::with('items')->findOrFail($id);
        return response()->json([
            'sif_no' => $sif->sif_no,
            'date' => $sif->date,
            'issued_date' => $sif->issued_date,
            'requested_by' => $sif->requested_by,
            'department' => $sif->department,
            'project_name' => $sif->project_name,
            'remarks' => $sif->remarks,
            'items' => $sif->items
        ]);
    }

    // Store SIF
    public function store(Request $request)
    {
        $lastSif = Sif::latest('id')->first();
        $nextNumber = $lastSif ? $lastSif->id + 1 : 1;
        $sifNo = 'SIF-' . str_pad($nextNumber, 8, '0', STR_PAD_LEFT);

        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'issued_date' => 'required|date',
            'requested_by' => 'required|string',
            'department' => 'required|string',
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

        $sif = DB::transaction(function () use ($request, $sifNo) {
            $sif = Sif::create([
                'sif_no' => $sifNo,
                'date' => $request->date,
                'issued_date' => $request->issued_date,
                'requested_by' => $request->requested_by,
                'department' => $request->department,
                'project_name' => $request->project_name,
                'remarks' => $request->remarks,
            ]);

            foreach ($request->items as $item) {
                $sif->items()->create([
                    'item_code' => $item['item_code'],
                    'description' => $item['description'],
                    'uom' => $item['uom'],
                    'quantity' => $item['quantity'],
                ]);
            }

            return $sif;
        });

        return response()->json([
            'success' => true,
            'message' => 'Stock Issuance Form saved successfully.',
            'data' => [
                'id' => $sif->id,
                'sif_no' => $sif->sif_no,
                'date' => $sif->date,
                'issued_date' => $sif->issued_date,
                'requested_by' => $sif->requested_by,
                'department' => $sif->department,
                'project_name' => $sif->project_name,
            ]
        ]);
    }

    // Edit form
    public function edit($id)
    {
        $sif = Sif::with('items')->findOrFail($id);
        $departments = Department::all();
        return view('sifs.edit', compact('sif', 'departments'));
    }

    // Update SIF
    public function update(Request $request, $id)
    {
        $sif = Sif::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'sif_no' => 'required|unique:sifs,sif_no,' . $id,
            'date' => 'required|date',
            'issued_date' => 'required|date',
            'requested_by' => 'required|string',
            'department' => 'required|string',
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

        DB::transaction(function () use ($request, $sif) {
            $sif->update([
                'sif_no' => $request->sif_no,
                'date' => $request->date,
                'issued_date' => $request->issued_date,
                'requested_by' => $request->requested_by,
                'department' => $request->department,
                'project_name' => $request->project_name,
                'remarks' => $request->remarks,
            ]);

            $sif->items()->delete();

            foreach ($request->items as $item) {
                $sif->items()->create([
                    'item_code' => $item['item_code'],
                    'description' => $item['description'],
                    'uom' => $item['uom'],
                    'quantity' => $item['quantity'],
                ]);
            }
        });

        return response()->json(['success' => true, 'message' => 'SIF updated successfully']);
    }

    // Delete SIF
    public function destroy($id)
    {
        $sif = Sif::find($id);
        if (!$sif) return response()->json(['success' => false, 'message' => 'SIF not found'], 404);

        try {
            $sif->delete();
            return response()->json(['success' => true, 'message' => 'SIF deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Unable to delete.'], 500);
        }
    }

    // 🔎 Search Items
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
