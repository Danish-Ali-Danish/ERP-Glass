<?php
namespace App\Http\Controllers;

use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class WorkOrderController extends Controller
{
    // Show all Work Orders (DataTables)
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = WorkOrder::select(['id', 'work_order_no', 'customer_name', 'date', 'work_order_type'])
               ->orderBy('id', 'desc');;
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('date', function ($row) {
                    return \Carbon\Carbon::parse($row->date)->format('d-m-Y');
                })
                ->addColumn('action', function ($row) {
                    return '
                    <a class="text-secondary fs-18 viewBtn" data-id="' . $row->id . '">
                        <i class="las la-eye"></i>
                    </a>
                    <a class="text-secondary fs-18 editBtn" data-id="' . $row->id . '">
                        <i class="las la-pen"></i>
                    </a>
                    <a class="text-secondary fs-18 deleteBtn" data-id="' . $row->id . '">
                        <i class="las la-trash-alt"></i>
                    </a>
                ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('workorders.index');
    }

    // Show create form
    public function create()
    {
        $lastWO      = WorkOrder::latest('id')->first();
        $nextNumber  = $lastWO ? $lastWO->id + 1 : 1;
        $workOrderNo = 'EPGI-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

        return view('workorders.create', compact('workOrderNo'));
    }

    // Store new Work Order
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_name'          => 'required|string|max:255',
            'customer_mobile'        => 'nullable|string|max:50',
            'date'                   => 'required|date',
            'work_order_no'          => 'required|string|unique:work_orders',
            'processes'              => 'nullable|array',
            'items'                  => 'required|array|min:1',
            'items.*.outer_w'        => 'nullable|numeric|min:0',
            'items.*.outer_h'        => 'nullable|numeric|min:0',
            'items.*.inner_w'        => 'nullable|numeric|min:0',
            'items.*.inner_h'        => 'nullable|numeric|min:0',
            'items.*.qty'            => 'required|integer|min:1',
            'items.*.sqm'            => 'nullable|numeric|min:0',
            'items.*.lm'             => 'nullable|numeric|min:0',
            'items.*.chargeable_sqm' => 'nullable|numeric|min:0',
            'items.*.amount'         => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $workOrder = WorkOrder::create([
                'work_order_no'   => $request->work_order_no,
                'customer_name'   => $request->customer_name,
                'customer_mobile' => $request->customer_mobile,
                'date'            => $request->date,
                'work_order_type' => $request->work_order_type,
                'customer_ref'    => $request->customer_ref,
                'processes'       => $request->processes, // JSON
                'extra_price_sqm' => $request->extra_price_sqm,
                'extra_total'     => $request->extra_total,
            ]);

            foreach ($request->items as $item) {
                $workOrder->items()->create($item);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Work Order created successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // Show single Work Order
    public function show(WorkOrder $workorder)
    {
        $workorder->load('items');
        return response()->json($workorder);
    }

    // Show edit form
    public function edit(WorkOrder $workorder)
    {
        $workorder->load('items');
        return view('workorders.edit', compact('workorder'));
    }

    // Update Work Order
// Update Work Order
    public function update(Request $request, WorkOrder $workorder)
    {
        $data = $request->all();
        // items flatten hone ki wajah se unko re-group karna zaroori hai
        $items = [];
        if (isset($data['items']) && is_array($data['items'])) {
            $items = $data['items'];
        } else {
            // Agar frontend se items[0][...] wali format me aaya hai
            foreach ($data as $key => $value) {
                if (preg_match('/^items\[(\d+)\]\[(.+)\]$/', $key, $matches)) {
                    $index                 = $matches[1];
                    $field                 = $matches[2];
                    $items[$index][$field] = $value;
                }
            }
        }

        $validator = Validator::make([
            'customer_name' => $request->customer_name,
            'date'          => $request->date,
            'processes'     => $request->processes,
            'items'         => $items,
        ], [
            'customer_name'  => 'required|string|max:255',
            'date'           => 'required|date',
            'processes'      => 'nullable|array',
            'items'          => 'required|array|min:1',
            'items.*.qty'    => 'required|integer|min:1',
            'items.*.amount' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $workorder->update([
                'customer_name'   => $request->customer_name,
                'customer_mobile' => $request->customer_mobile,
                'date'            => $request->date,
                'work_order_type' => $request->work_order_type,
                'customer_ref'    => $request->customer_ref,
                'processes'       => $request->processes,
                'extra_price_sqm' => $request->extra_price_sqm,
                'extra_total'     => $request->extra_total,
            ]);

            // Purane items delete karke naye insert
            $workorder->items()->delete();
            foreach ($items as $item) {
                $workorder->items()->create($item);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Work Order updated successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
    public function preview(WorkOrder $workorder)
    {
        $workorder->load('items');
            $workorder->date = \Carbon\Carbon::parse($workorder->date)->format('d-m-Y');

        return view('workorders.preview', compact('workorder'));
    }

    // Delete Work Order
    public function destroy(WorkOrder $workorder)
    {
        try {
            $workorder->delete();
            return response()->json(['status' => 'success', 'message' => 'Work Order deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
