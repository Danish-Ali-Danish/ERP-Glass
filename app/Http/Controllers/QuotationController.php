<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class QuotationController extends Controller
{
   
    // Show Create Quotation Form
    public function create()
    {
        $last = Quotation::latest('id')->first();
        $nextNumber = $last ? $last->id + 1 : 1;
        $quoteNo = 'QTN-' . str_pad($nextNumber, 8, '0', STR_PAD_LEFT);

        return view('quotations.create', compact('quoteNo'));
    }

    // List Quotations (for DataTables)
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Quotation::with('items')->latest();

            return DataTables::eloquent($data)
                ->addIndexColumn()
                ->addColumn('items_count', fn($row) => $row->items->count())
                ->addColumn('action', function ($row) {
                    return '
                        <a href="javascript:void(0)" class="text-secondary fs-18 me-2 viewBtn" data-id="' . $row->id . '" title="Preview">
                            <i class="las la-eye"></i>
                        </a>
                        <a href="/quotations/' . $row->id . '/edit" class="text-secondary fs-18 me-2" title="Edit">
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

        return view('quotations.index');
    }

    // Show single Quotation details
    public function show($id)
    {
        $quotation = Quotation::with('items')->findOrFail($id);

        return response()->json([
            'quote_no'     => $quotation->quote_no,
            'date'         => $quotation->date,
            'company_name' => $quotation->company_name,
            'project_name' => $quotation->project_name,
            'location'     => $quotation->location,
            'terms'        => $quotation->terms,
            'total'        => $quotation->total,
            'vat'          => $quotation->vat,
            'grand_total'  => $quotation->grand_total,
            'items'        => $quotation->items
        ]);
    }

    // Store new Quotation
    public function store(Request $request)
    {
        $last = Quotation::latest('id')->first();
        $nextNumber = $last ? $last->id + 1 : 1;
        $quoteNo = 'QTN-' . str_pad($nextNumber, 8, '0', STR_PAD_LEFT);

        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'company_name' => 'required|string',
            'project_name' => 'nullable|string',
            'location' => 'nullable|string',
            'terms' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.unit' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $quotation = DB::transaction(function () use ($request, $quoteNo) {
            $quotation = Quotation::create([
                'quote_no'     => $quoteNo,
                'date'         => $request->date,
                'company_name' => $request->company_name,
                'project_name' => $request->project_name,
                'location'     => $request->location,
                'terms'        => $request->terms,
            ]);

            $total = 0;
            foreach ($request->items as $item) {
                $lineTotal = $item['quantity'] * $item['unit_price'];
                $quotation->items()->create([
                    'quotation_id' => $quotation->id,
                    'item_id'      => $item['item_id'] ?? null,
                    'description'  => $item['description'],
                    'unit'         => $item['unit'],
                    'quantity'     => $item['quantity'],
                    'unit_price'   => $item['unit_price'],
                    'total'        => $lineTotal,
                ]);
                $total += $lineTotal;
            }

            $quotation->update([
                'total'       => $total,
                'vat'         => $total * 0.05,
                'grand_total' => $total * 1.05,
            ]);

            return $quotation;
        });

        return response()->json([
            'success'   => true,
            'message'   => 'Quotation saved successfully',
            'quote_no'  => $quotation->quote_no
        ]);
    }

    // Edit form
    public function edit($id)
    {
        $quotation = Quotation::with('items')->findOrFail($id);
        return view('quotations.edit', compact('quotation'));
    }

    // Update Quotation
    public function update(Request $request, $id)
    {
        $quotation = Quotation::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'quote_no' => 'required|unique:quotations,quote_no,' . $id,
            'date' => 'required|date',
            'company_name' => 'required|string',
            'project_name' => 'nullable|string',
            'location' => 'nullable|string',
            'terms' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.unit' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::transaction(function () use ($request, $quotation) {
            $quotation->update([
                'quote_no'     => $request->quote_no,
                'date'         => $request->date,
                'company_name' => $request->company_name,
                'project_name' => $request->project_name,
                'location'     => $request->location,
                'terms'        => $request->terms,
            ]);

            $quotation->items()->delete();

            $total = 0;
            foreach ($request->items as $item) {
                $lineTotal = $item['quantity'] * $item['unit_price'];
                $quotation->items()->create([
                    'quotation_id' => $quotation->id,
                    'item_id'      => $item['item_id'] ?? null,
                    'description'  => $item['description'],
                    'unit'         => $item['unit'],
                    'quantity'     => $item['quantity'],
                    'unit_price'   => $item['unit_price'],
                    'total'        => $lineTotal,
                ]);
                $total += $lineTotal;
            }

            $quotation->update([
                'total'       => $total,
                'vat'         => $total * 0.05,
                'grand_total' => $total * 1.05,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Quotation updated successfully'
        ]);
    }

    // Delete Quotation
    public function destroy($id)
    {
        $quotation = Quotation::findOrFail($id);
        $quotation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Quotation deleted successfully'
        ]);
    }
}
