<?php

namespace App\Http\Controllers;

use App\Models\Grn;
use App\Models\GrnItem;
use App\Models\Lpo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GrnController extends Controller
{
    // List all GRNs (for DataTable)
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $grns = Grn::latest()->withCount('items')->get();
            return datatables()->of($grns)
                ->addIndexColumn()
                ->addColumn('action', function ($grn) {
                    return '
                        <button class="btn btn-sm btn-primary viewBtn" data-id="'.$grn->id.'">
                            <i class="las la-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-warning editBtn" data-id="'.$grn->id.'">
                            <i class="las la-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger deleteBtn" data-id="'.$grn->id.'">
                            <i class="las la-trash"></i>
                        </button>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('grns.index');
    }

    // Show create GRN page
    public function create()
    {
        $lpos = Lpo::where('grn_generated', 0)->limit(20)->get();
        return view('grns.create', compact('lpos'));
    }

    // Fetch LPO details for GRN creation
    public function getLpoDetails($id)
    {
        $lpo = Lpo::with('items')->findOrFail($id);

        $mappedItems = $lpo->items->map(function ($item) {
            return [
                'description' => $item->description,
                'uom' => $item->uom,
                'quantity' => $item->quantity ?? 0
            ];
        });

        return response()->json([
            'lpo_no' => $lpo->lpo_no,
            'supplier_name' => $lpo->supplier_name,
            'date' => $lpo->date,
            'supplier_code' => $lpo->supplier_code,
            'requested_by' => $lpo->requested_by,
            'department' => $lpo->department,
            'project_name' => $lpo->project_name,
            'inv_no' => $lpo->inv_no,
            'inv_date' => $lpo->inv_date,
            'items' => $mappedItems
        ]);
    }

    // Store GRN with items
    public function store(Request $request)
    {
        $request->validate([
            'lpo_id' => 'required|exists:lpos,id',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.uom' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'supplier_name'=>'required|string',
            'date'=>'required|date',
            'supplier_code'=>'nullable|string',
            'requested_by'=>'nullable|string',
            'inv_no'=>'nullable|string',
            'department'=>'nullable|string',
            'inv_date'=>'nullable|date',
            'project_name'=>'nullable|string'
        ]);

        DB::transaction(function() use ($request) {
            $lpo = Lpo::findOrFail($request->lpo_id);
            if ($lpo->grn_generated) {
                abort(400, "This LPO already has a GRN.");
            }

            $grn = Grn::create($request->only([
                'lpo_id','lpo_no','supplier_name','date','supplier_code',
                'requested_by','inv_no','department','inv_date','project_name'
            ]));

            foreach ($request->items as $item) {
                $grn->items()->create([
                    'description' => $item['description'],
                    'uom' => $item['uom'],
                    'quantity' => $item['quantity']
                ]);
            }

            $lpo->grn_generated = 1;
            $lpo->save();
        });

        return response()->json(['message' => 'GRN created successfully']);
    }

    // Show GRN details (for preview modal)
    public function show($id)
    {
        $grn = Grn::with('items')->findOrFail($id);
        return response()->json($grn);
    }

    // Delete GRN
    public function destroy($id)
    {
        $grn = Grn::findOrFail($id);
        DB::transaction(function() use ($grn) {
            $lpo = $grn->lpo;
            $grn->delete();
            if ($lpo) {
                $lpo->grn_generated = 0;
                $lpo->save();
            }
        });

        return response()->json(['message' => 'GRN deleted successfully']);
    }
}
