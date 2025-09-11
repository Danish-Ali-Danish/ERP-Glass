<?php

namespace App\Http\Controllers;

use App\Models\Lpo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class LpoController extends Controller
{
    // Show all LPOs (DataTables)
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Lpo::select(['id','supplier_name','contact_person','pi_no','supplier_trn','lpo_no']);
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
        return view('lpos.index');
    }

    // Show create form
    public function create()
    {
        // Generate next LPO No for display
        $lastLpo = Lpo::latest('id')->first();
        $nextNumber = $lastLpo ? $lastLpo->id + 1 : 1;
        $lpoNo = 'LPO-' . str_pad($nextNumber, 8, '0', STR_PAD_LEFT);

        return view('lpos.create', compact('lpoNo'));
    }

    // Store new LPO
    public function store(Request $request)
    {
        $request->validate([
            'supplier_name' => 'required|string',
            'date' => 'required|date',
            'items' => 'required|array|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Auto-generate again to avoid duplication
            $lastLpo = Lpo::latest('id')->first();
            $nextNumber = $lastLpo ? $lastLpo->id + 1 : 1;
            $lpoNo = 'LPO-' . str_pad($nextNumber, 8, '0', STR_PAD_LEFT);

            $lpo = Lpo::create([
                'supplier_name' => $request->supplier_name,
                'date' => $request->date,
                'contact_person' => $request->contact_person,
                'lpo_no' => $lpoNo,
                'contact_no' => $request->contact_no,
                'pi_no' => $request->pi_no,
                'supplier_trn' => $request->supplier_trn,
                'address' => $request->address,
                'sub_total' => $request->sub_total,
                'vat' => $request->vat,
                'net_total' => $request->net_total,
            ]);

            foreach ($request->items as $item) {
                $lpo->items()->create([
                    'description' => $item['description'],
                    'area' => $item['area'] ?? 0,
                    'quantity' => $item['quantity'],
                    'uom' => $item['uom'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'LPO created successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error: '.$e->getMessage()], 500);
        }
    }

    // Show single LPO (for preview modal)
    public function show(Lpo $lpo)
    {
        $lpo->load('items');
        return response()->json($lpo);
    }

    // Show edit form
    public function edit(Lpo $lpo)
    {
        $lpo->load('items');
        return view('lpos.edit', compact('lpo'));
    }

    // Update LPO
    public function update(Request $request, Lpo $lpo)
    {
        $request->validate([
            'supplier_name' => 'required|string',
            'date' => 'required|date',
            'items' => 'required|array|min:1',
        ]);

        DB::beginTransaction();
        try {
            $lpo->update([
                'supplier_name' => $request->supplier_name,
                'date' => $request->date,
                'contact_person' => $request->contact_person,
                'contact_no' => $request->contact_no,
                'pi_no' => $request->pi_no,
                'supplier_trn' => $request->supplier_trn,
                'address' => $request->address,
                'sub_total' => $request->sub_total,
                'vat' => $request->vat,
                'net_total' => $request->net_total,
            ]);

            // Purane items delete karke naye insert karna
            $lpo->items()->delete();
            foreach ($request->items as $item) {
                $lpo->items()->create([
                    'description' => $item['description'],
                    'area' => $item['area'] ?? 0,
                    'quantity' => $item['quantity'],
                    'uom' => $item['uom'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'LPO updated successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error: '.$e->getMessage()], 500);
        }
    }

    // Delete LPO
    public function destroy(Lpo $lpo)
    {
        $lpo->delete();
        return response()->json(['message' => 'LPO deleted successfully']);
    }
}
