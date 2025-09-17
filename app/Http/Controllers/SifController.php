<?php

namespace App\Http\Controllers;

use App\Models\Sif;
use App\Models\SifItem;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class SifController extends Controller
{
    // Show Add New SIF form
    public function create()
    {
        $departments = Department::all();
        $last = Sif::latest('id')->first();
        $nextNumber = $last ? $last->id + 1 : 1;
        $sifNo = 'SIF-' . str_pad($nextNumber, 8, '0', STR_PAD_LEFT);

        return view('sifs.add-new', compact('departments', 'sifNo'));
    }

    // List SIFs (for DataTables)
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Sif::with('department', 'items')->latest();

            return DataTables::eloquent($data)
                ->addIndexColumn()
                ->addColumn('department', fn($row) => $row->department->name ?? '-')
                ->addColumn('items_count', fn($row) => $row->items->count())
                ->addColumn('action', function ($row) {
                    return '
                        <a href="javascript:void(0)" class="text-secondary fs-18 me-2 viewBtn" data-id="' . $row->id . '" title="Preview">
                            <i class="las la-eye"></i>
                        </a>
                        <a href="/sifs/' . $row->id . '/edit" class="text-secondary fs-18 me-2 editBtn" title="Edit">
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

    // Show single SIF details (for preview modal)
    public function show($id)
    {
        $sif = Sif::with('items', 'department')->findOrFail($id);

        return response()->json([
            'sif_no' => $sif->sif_no,
            'date' => $sif->date,
            'issued_date' => $sif->issued_date,
            'requested_by' => $sif->requested_by,
            'department' => $sif->department->name,
            'project_name' => $sif->project_name,
            'remarks' => $sif->remarks,
            'items' => $sif->items
        ]);
    }

    // Store new SIF
    public function store(Request $request)
    {
        $last = Sif::latest('id')->first();
        $nextNumber = $last ? $last->id + 1 : 1;
        $sifNo = 'SIF-' . str_pad($nextNumber, 8, '0', STR_PAD_LEFT);

        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'issued_date' => 'required|date',
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
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $sif = DB::transaction(function () use ($request, $sifNo) {
            $sif = Sif::create([
                'sif_no' => $sifNo,
                'date' => $request->date,
                'issued_date' => $request->issued_date,
                'requested_by' => $request->requested_by,
                'department_id' => $request->department_id,
                'project_name' => $request->project_name,
                'remarks' => $request->remarks,
            ]);

            foreach ($request->items as $item) {
                $sif->items()->create($item);
            }

            return $sif;
        });

        return response()->json([
            'success' => true,
            'message' => 'SIF saved successfully',
            'sif_no' => $sif->sif_no
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
            'department_id' => 'required|exists:departments,id',
            'project_name' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.item_code' => 'required|string',
            'items.*.description' => 'required|string',
            'items.*.uom' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::transaction(function () use ($request, $sif) {
            $sif->update([
                'sif_no' => $request->sif_no,
                'date' => $request->date,
                'issued_date' => $request->issued_date,
                'requested_by' => $request->requested_by,
                'department_id' => $request->department_id,
                'project_name' => $request->project_name,
                'remarks' => $request->remarks,
            ]);

            // Delete old items
            $sif->items()->delete();

            // Add new items
            foreach ($request->items as $item) {
                $sif->items()->create($item);
            }
        });

        return response()->json(['success' => true, 'message' => 'SIF updated successfully']);
    }

    // Delete SIF
    public function destroy($id)
    {
        $sif = Sif::find($id);
        if (!$sif) {
            return response()->json(['success' => false, 'message' => 'SIF not found'], 404);
        }

        try {
            $sif->delete();
            return response()->json(['success' => true, 'message' => 'SIF deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Unable to delete'], 500);
        }
    }
}
