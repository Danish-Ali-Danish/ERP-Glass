<?php
namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\QuotationItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Quotation::withCount('items')->latest();
            return DataTables::eloquent($data)
                ->addIndexColumn()
                ->addColumn('items_count', fn($row) => $row->items_count)
                ->addColumn('action', function ($row) {
                    $buttons = '';
                    $buttons .= '<a href="javascript:void(0)" class="text-secondary fs-18 me-2 viewBtn" data-id="' . $row->id . '" title="Preview"><i class="las la-eye"></i></a>';
                    if (hasPermission('quotations.edit')) {
                        $buttons .= '<a href="/quotations/' . $row->id . '/edit" class="text-secondary fs-18 me-2" title="Edit"><i class="las la-pen"></i></a>';
                    }
                    if (hasPermission('quotations.destroy')) {
                        $buttons .= '<a href="javascript:void(0)" class="text-secondary fs-18 deleteBtn" data-id="' . $row->id . '" title="Delete"><i class="las la-trash-alt"></i></a>';
                    }
                    return $buttons;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('quotations.index');
    }

    public function create()
    {
        $last = Quotation::latest('id')->first();
        $nextNumber = $last ? $last->id + 1 : 1;
        $quoteNo = 'QTN-' . str_pad($nextNumber, 8, '0', STR_PAD_LEFT);
        return view('quotations.create', compact('quoteNo'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'company_name' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.uom' => 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()], 422);
        }

        $quotation = DB::transaction(function() use ($request) {
            $last = Quotation::latest('id')->first();
            $nextNumber = $last ? $last->id + 1 : 1;
            $quoteNo = 'QTN-' . str_pad($nextNumber, 8, '0', STR_PAD_LEFT);

            $q = Quotation::create([
                'quote_no' => $quoteNo,
                'date' => $request->date,
                'company_name' => $request->company_name,
                'project_name' => $request->project_name,
                'location' => $request->location,
                'terms' => $request->terms,
            ]);

            $total = 0;
            foreach ($request->items as $it) {
                $lineTotal = (float)$it['quantity'] * (float)$it['unit_price'];
                $q->items()->create([
                    'item_id' => $it['item_id'] ?? null,
                    'item_code' => $it['item_code'] ?? null,
                    'description' => $it['description'] ?? null,
                    'uom' => $it['uom'] ?? null,
                    'size' => $it['size'] ?? null,
                    'color' => $it['color'] ?? null,
                    'type' => $it['type'] ?? null,
                    'remarks' => $it['remarks'] ?? null,
                    'quantity' => $it['quantity'],
                    'unit_price' => $it['unit_price'],
                    'total' => $lineTotal,
                ]);
                $total += $lineTotal;
            }

            $q->update([
                'total' => $total,
                'vat' => $total * 0.05,
                'grand_total' => $total * 1.05
            ]);

            return $q;
        });

        return response()->json([
            'success' => true,
            'message' => 'Quotation saved successfully',
            'quote_no' => $quotation->quote_no
        ]);
    }

    public function show($id)
    {
        $quotation = Quotation::with('items')->findOrFail($id);
        return response()->json($quotation);
    }

    public function edit($id)
    {
        $quotation = Quotation::with('items')->findOrFail($id);
        return view('quotations.edit', compact('quotation'));
    }

    public function update(Request $request, $id)
    {
        $quotation = Quotation::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'quote_no' => 'required|unique:quotations,quote_no,'.$id,
            'date' => 'required|date',
            'company_name' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()], 422);
        }

        DB::transaction(function() use ($request, $quotation) {
            $quotation->update([
                'quote_no' => $request->quote_no,
                'date' => $request->date,
                'company_name' => $request->company_name,
                'project_name' => $request->project_name,
                'location' => $request->location,
                'terms' => $request->terms,
            ]);

            $quotation->items()->delete();

            $total = 0;
            foreach ($request->items as $it) {
                $lineTotal = (float)$it['quantity'] * (float)$it['unit_price'];
                $quotation->items()->create([
                    'item_id' => $it['item_id'] ?? null,
                    'item_code' => $it['item_code'] ?? null,
                    'description' => $it['description'] ?? null,
                    'uom' => $it['uom'] ?? null,
                    'size' => $it['size'] ?? null,
                    'color' => $it['color'] ?? null,
                    'type' => $it['type'] ?? null,
                    'remarks' => $it['remarks'] ?? null,
                    'quantity' => $it['quantity'],
                    'unit_price' => $it['unit_price'],
                    'total' => $lineTotal,
                ]);
                $total += $lineTotal;
            }

            $quotation->update([
                'total' => $total,
                'vat' => $total * 0.05,
                'grand_total' => $total * 1.05
            ]);
        });

        return response()->json(['success'=>true,'message'=>'Quotation updated successfully']);
    }

    public function destroy($id)
    {
        $quotation = Quotation::findOrFail($id);
        $quotation->delete();
        return response()->json(['success'=>true,'message'=>'Quotation deleted successfully']);
    }
}
