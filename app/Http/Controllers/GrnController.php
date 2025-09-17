<?php
namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Grn;
use App\Models\Lpo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class GrnController extends Controller
{
    // List all GRNs (for DataTable)
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $grns = Grn::with(['lpo', 'department'])->latest();

            return DataTables::of($grns)
                ->addIndexColumn()
                ->addColumn('grn_no', fn($row) => $row->grn_no)
                ->addColumn('lpo_no', fn($row) => $row->lpo?->lpo_no ?? '-')
                ->addColumn('supplier_name', fn($row) => $row->lpo?->supplier_name ?? $row->supplier_name)
                ->addColumn('date', fn($row) => $row->date ? \Carbon\Carbon::parse($row->date)->format('d/m/Y') : '-')
                ->addColumn('requested_by', fn($row) => $row->requested_by ?? '-')
                ->addColumn('department', fn($row) => $row->department?->name ?? '-')
                ->addColumn('project_name', fn($row) => $row->project_name ?? '-')
                ->addColumn('action', function ($row) {
                    return '
                        <button class="btn btn-sm btn-info viewBtn" data-id="' . $row->id . '">
                            <i class="las la-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-warning editBtn" data-id="' . $row->id . '">
                            <i class="las la-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger deleteBtn" data-id="' . $row->id . '">
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
        $departments = Department::all();
        $lpos = Lpo::where('grn_generated', 0)->limit(20)->get();

        // Auto-generate GRN no
        $lastGrn = Grn::latest()->first();
        $nextNo  = $lastGrn ? ((int) str_replace("GRN-", "", $lastGrn->grn_no)) + 1 : 1;
        $grn_no  = "GRN-" . str_pad($nextNo, 6, "0", STR_PAD_LEFT);

        return view('grns.create', compact('lpos', 'grn_no', 'departments'));
    }

    // Fetch LPO details with items
    public function getLpoDetails($id)
    {
        $lpo = Lpo::with(['items', 'department'])->findOrFail($id);

        $mappedItems = $lpo->items->map(fn($item) => [
            'description' => $item->description,
            'uom'         => $item->uom,
            'quantity'    => $item->quantity ?? 0,
        ]);

        return response()->json([
            'lpo_no'        => $lpo->lpo_no,
            'supplier_name' => $lpo->supplier_name,
            'lpo_date'      => $lpo->date,
            'supplier_code' => $lpo->supplier_code,
            'requested_by'  => $lpo->requested_by,
            'department_id' => $lpo->department_id,
            'department'    => $lpo->department?->name,
            'project_name'  => $lpo->project_name,
            'inv_no'        => $lpo->inv_no,
            'inv_date'      => $lpo->inv_date,
            'items'         => $mappedItems,
        ]);
    }

    // Store GRN with items
    public function store(Request $request)
    {
        $request->validate([
            'lpo_id'              => 'required|exists:lpos,id',
            'items'               => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.uom'         => 'required|string',
            'items.*.quantity'    => 'required|numeric|min:1',
            'supplier_name'       => 'required|string',
            'lpo_date'            => 'required|date',
            'supplier_code'       => 'nullable|string',
            'requested_by'        => 'nullable|string',
            'inv_no'              => 'nullable|string',
            'department_id'       => 'required|exists:departments,id',
            'inv_date'            => 'nullable|date',
            'project_name'        => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $lpo = Lpo::findOrFail($request->lpo_id);
           // ✅ Block access if this LPO already has a GRN
        if ($lpo->grn_generated) {
            return response()->json(['error' => 'This LPO already has a GRN.'], 400);
        }
            // Auto-generate GRN number
            $lastGrn = Grn::latest()->first();
            $nextNo  = $lastGrn ? ((int) str_replace("GRN-", "", $lastGrn->grn_no)) + 1 : 1;
            $grnNo   = "GRN-" . str_pad($nextNo, 6, "0", STR_PAD_LEFT);

            $grn = Grn::create([
                'grn_no'        => $grnNo,
                'lpo_id'        => $lpo->id,
                'lpo_no'        => $lpo->lpo_no,
                'supplier_name' => $request->supplier_name,
                'date'          => $request->lpo_date,
                'supplier_code' => $request->supplier_code,
                'requested_by'  => $request->requested_by,
                'inv_no'        => $request->inv_no,
                'department_id' => $request->department_id,
                'inv_date'      => $request->inv_date,
                'project_name'  => $request->project_name,
            ]);

            foreach ($request->items as $item) {
                $grn->items()->create([
                    'description' => $item['description'],
                    'uom'         => $item['uom'],
                    'quantity'    => $item['quantity'],
                ]);
            }

            $lpo->update(['grn_generated' => 1]);
        });

        return response()->json(['message' => 'GRN created successfully']);
    }

    // Show GRN details (for preview modal)
    public function show($id)
    {
        $grn = Grn::with(['items', 'lpo', 'department'])->findOrFail($id);
        return response()->json($grn);
    }

    // Delete GRN
    public function destroy($id)
    {
        $grn = Grn::findOrFail($id);
        DB::transaction(function () use ($grn) {
            $lpo = $grn->lpo;
            $grn->delete();
            if ($lpo) {
                $lpo->update(['grn_generated' => 0]);
            }
        });

        return response()->json(['message' => 'GRN deleted successfully']);
    }
}
