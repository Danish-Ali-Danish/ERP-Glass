<?php

namespace App\Http\Controllers;

use App\Models\Lpo;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
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
        $lastLpo = Lpo::latest('id')->first();
        $nextNumber = $lastLpo ? $lastLpo->id + 1 : 1;
        $lpoNo = 'LPO-' . str_pad($nextNumber, 8, '0', STR_PAD_LEFT);

        return view('lpos.create', compact('lpoNo'));
    }

    // Store new LPO
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier_name' => 'required|string|max:255',
            'date' => 'required|date',
            'contact_person' => 'nullable|string|max:255',
            'contact_no' => 'nullable|string|max:50',
            'pi_no' => 'nullable|string|max:50',
            'supplier_trn' => 'nullable|string|max:50',
            'address' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0.01',
            'items.*.uom' => 'required|string|max:50',
            'items.*.area' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
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
            return response()->json(['status'=>'success','message' => 'LPO created successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status'=>'error','message' => 'Error: '.$e->getMessage()], 500);
        }
    }

    // Show single LPO
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
        $validator = Validator::make($request->all(), [
            'supplier_name' => 'required|string|max:255',
            'date' => 'required|date',
            'contact_person' => 'nullable|string|max:255',
            'contact_no' => 'nullable|string|max:50',
            'pi_no' => 'nullable|string|max:50',
            'supplier_trn' => 'nullable|string|max:50',
            'address' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0.01',
            'items.*.uom' => 'required|string|max:50',
            'items.*.area' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

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
            return response()->json(['status'=>'success','message' => 'LPO updated successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status'=>'error','message' => 'Error: '.$e->getMessage()], 500);
        }
    }

    // Delete LPO
    public function destroy(Lpo $lpo)
    {
        try {
            $lpo->delete();
            return response()->json(['status'=>'success','message' => 'LPO deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['status'=>'error','message' => 'Error: '.$e->getMessage()], 500);
        }
    }

    // 🔎 Search Items for LPO (Live Suggestions)
    public function searchItems(Request $request)
    {
        $q = $request->get('q');

        $query = Item::query();

        if(!$q || $q === '*'){
            $query->limit(20);
        } else {
            $query->where('description','like',"%$q%")->limit(20);
        }

        $items = $query->select('id','description','uom')->get();

        return response()->json([
            'results' => $items
        ]);
    }
}
