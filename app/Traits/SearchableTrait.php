<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use App\Helpers\ItemHelper; // ✅ for item search reuse

class QuotationController extends Controller
{
    // Show Create Quotation Form
    public function create()
    {
        $last       = Quotation::latest('id')->first();
        $nextNumber = $last ? $last->id + 1 : 1;
        $quoteNo    = 'QTN-' . str_pad($nextNumber, 8, '0', STR_PAD_LEFT);

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
                    $buttons = '';

                    $buttons .= '<a href="javascript:void(0)" class="text-secondary fs-18 me-2 viewBtn" data-id="' . $row->id . '" title="Preview">
                                    <i class="las la-eye"></i>
                                 </a>';

                    if (hasPermission('quotations.edit')) {
                        $buttons .= '<a href="/quotations/' . $row->id . '/edit" class="text-secondary fs-18 me-2" title="Edit">
                                        <i class="las la-pen"></i>
                                     </a>';
                    }

                    if (hasPermission('quotations.destroy')) {
                        $buttons .= '<a href="javascript:void(0)" class="text-secondary fs-18 deleteBtn" data-id="' . $row->id . '" title="Delete">
                                        <i class="las la-trash-alt"></i>
                                     </a>';
                    }

                    return $buttons;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('quotations.index');
    }

    // Show single Quotation details
    public function show($id)
    {
        $quotation = Quotation::with('items.item')->findOrFail($id);

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
            // ✅ include all item fields from items table
            'items' => $quotation->items->map(function ($item) {
                return [
                    'item_id'      => $item->item_id,
                    'item_code'    => $item->item->item_code ?? null,
                    'description'  => $item->item->description ?? $item->description,
                    'uom'          => $item->item->uom ?? null,
                    'size'         => $item->item->size ?? null,
                    'color'        => $item->item->color ?? null,
                    'type'         => $item->item->type ?? null,
                    'remarks'      => $item->item->remarks ?? null,
                    'quantity'     => $item->quantity,
                    'unit_price'   => $item->unit_price,
                    'total'        => $item->total,
                ];
            }),
        ]);
    }

    // Store new Quotation
    public function store(Request $request)
    {
        $last       = Quotation::latest('id')->first();
        $nextNumber = $last ? $last->id + 1 : 1;
        $quoteNo    = 'QTN-' . str_pad($nextNumber, 8, '0', STR_PAD_LEFT);

        $validator = Validator::make($request->all(), [
            'date'                => 'required|date',
            'company_name'        => 'required|string',
            'project_name'        => 'nullable|string',
            'location'            => 'nullable|string',
            'terms'               => 'nullable|string',
            'items'               => 'required|array|min:1',
            'items.*.item_id'     => 'nullable|exists:items,id',
            'items.*.description' => 'required|string',
            'items.*.unit'        => 'required|string',
            'items.*.quantity'    => 'required|numeric|min:0.01',
            'items.*.unit_price'  => 'required|numeric|min:0',
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
            'success'  => true,
            'message'  => 'Quotation saved successfully',
            'quote_no' => $quotation->quote_no,
        ]);
    }

    // Search items (using helper)
    public function search(Request $request)
    {
        $query = $request->get('q');
        $type  = $request->get('type', 'description');

        $items = \App\Helpers\ItemHelper::searchItems($query, $type);

        return response()->json([
            'success' => true,
            'results' => $items,
        ]);
    }
}
