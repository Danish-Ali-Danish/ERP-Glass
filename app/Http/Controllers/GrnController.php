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
    // List all GRNs
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $grns = Grn::with(['lpo', 'department'])->latest();

            return DataTables::of($grns)
                ->addIndexColumn()
                ->addColumn('grn_no', fn($row) => $row->grn_no)
                ->addColumn('lpo_no', fn($row) => $row->lpo?->lpo_no ?? '-')
                ->addColumn('supplier_name', fn($row) => $row->lpo?->supplier_name ?? $row->supplier_name)
                ->addColumn('lpo_date', fn($row) =>
                    $row->lpo_date
                        ? \Carbon\Carbon::parse($row->lpo_date)->format('d/m/Y')
                        : ($row->lpo?->date ? \Carbon\Carbon::parse($row->lpo->date)->format('d/m/Y') : '-')
                )
                ->addColumn('requested_by', fn($row) => $row->requested_by ?? '-')
                ->addColumn('department', fn($row) => $row->department?->name ?? '-')
                ->addColumn('project_name', fn($row) => $row->project_name ?? '-')
                ->addColumn('action', function ($row) {
                    return '
                        <a class="text-secondary fs-18 viewBtn" data-id="' . $row->id . '">
                            <i class="las la-eye"></i>
                        </a>
                        <a class="text-secondary fs-18 editBtn" data-id="' . $row->id . '">
                            <i class="las la-pen"></i>
                        </a>
                        <a class="text-secondary fs-18 deleteBtn" data-id="' . $row->id . '">
                            <i class="las la-trash"></i>
                        </a>
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

        // ✅ sirf un LPOs ko lao jinke liye abhi tak GRN nahi bana aur jinka lpo_no empty na ho
        // ✅ Modified: Also exclude LPOs that already have a GRN (even if grn_generated flag is not set)
        $lpos = Lpo::where('grn_generated', 0)
            ->whereNotNull('lpo_no')
            ->where('lpo_no', '!=', '')
            ->whereNotIn('id', function($query) {
                $query->select('lpo_id')
                    ->from('grns')
                    ->whereNotNull('lpo_id');
            })
            ->limit(20) // Limit to 20 records for performance
            ->get();

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
        
        // Check if this LPO already has a GRN
        $existingGrn = Grn::where('lpo_id', $id)->first();
        if ($existingGrn) {
            return response()->json([
                'error' => 'This LPO already has a GRN created. Please select another LPO.'
            ], 422);
        }

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

        // Additional validation to ensure LPO doesn't already have a GRN
        $existingGrn = Grn::where('lpo_id', $request->lpo_id)->first();
        if ($existingGrn) {
            return response()->json([
                'message' => 'This LPO already has a GRN created.'
            ], 422);
        }

        DB::transaction(function () use ($request) {
            $lpo = Lpo::findOrFail($request->lpo_id);

            if ($lpo->grn_generated) {
                abort(400, 'This LPO already has a GRN.');
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
                'lpo_date'      => $request->lpo_date,
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

            // ✅ GRN banne ke baad LPO ko mark kar do
            $lpo->update(['grn_generated' => 1]);
        });

        return response()->json(['message' => 'GRN created successfully']);
    }

    // Show GRN details
    public function show($id)
    {
        $grn = Grn::with(['items', 'lpo', 'department'])->findOrFail($id);
        return response()->json($grn);
    }

    // Show edit form
    public function edit(Grn $grn)
    {
        $departments = Department::all();

        // ✅ sirf wo LPOs show karo jinke liye GRN abhi tak nahi bana
        // plus current GRN ka LPO allow karo (taake edit ho sake)
        $lpos = Lpo::where(function ($query) use ($grn) {
                $query->where('grn_generated', 0)
                      ->orWhere('id', $grn->lpo_id);
            })
            ->whereNotNull('lpo_no')
            ->where('lpo_no', '!=', '')
            ->whereNotIn('id', function($query) use ($grn) {
                $query->select('lpo_id')
                    ->from('grns')
                    ->whereNotNull('lpo_id')
                    ->where('id', '!=', $grn->id);
            })
            ->get();

        return view('grns.edit', compact('grn', 'departments', 'lpos'));
    }

    // Handle update
    public function update(Request $request, Grn $grn)
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

        // Additional validation to ensure the new LPO doesn't already have a GRN
        if ($request->lpo_id != $grn->lpo_id) {
            $existingGrn = Grn::where('lpo_id', $request->lpo_id)->first();
            if ($existingGrn) {
                return redirect()->back()->with('error', 'The selected LPO already has a GRN created.');
            }
        }

        DB::transaction(function () use ($request, $grn) {
            $oldLpoId = $grn->lpo_id;
            $lpo = Lpo::findOrFail($request->lpo_id);

            if ($lpo->grn_generated && $lpo->id !== $oldLpoId) {
                abort(400, "This LPO already has a GRN.");
            }

            $grn->update([
                'lpo_id'        => $lpo->id,
                'lpo_no'        => $lpo->lpo_no,
                'supplier_name' => $request->supplier_name,
                'lpo_date'      => $request->lpo_date,
                'supplier_code' => $request->supplier_code,
                'requested_by'  => $request->requested_by,
                'inv_no'        => $request->inv_no,
                'department_id' => $request->department_id,
                'inv_date'      => $request->inv_date,
                'project_name'  => $request->project_name,
            ]);

            // replace items
            $grn->items()->delete();
            foreach ($request->items as $item) {
                $grn->items()->create([
                    'description' => $item['description'],
                    'uom'         => $item['uom'],
                    'quantity'    => $item['quantity'],
                ]);
            }

            // reset old LPO if changed
            if ($oldLpoId !== $lpo->id) {
                Lpo::where('id', $oldLpoId)->update(['grn_generated' => 0]);
            }

            // ✅ naya LPO mark karo
            $lpo->update(['grn_generated' => 1]);
        });

        return redirect()->route('grns.index')->with('success', 'GRN updated successfully');
    }

    // Delete GRN
    public function destroy($id)
    {
        $grn = Grn::findOrFail($id);

        DB::transaction(function () use ($grn) {
            $lpo = $grn->lpo;
            $grn->items()->delete();
            $grn->delete();

            if ($lpo) {
                // ✅ GRN delete hone par LPO ko dobara allow karo
                $lpo->update(['grn_generated' => 0]);
            }
        });

        return response()->json(['message' => 'GRN deleted successfully']);
    }
}