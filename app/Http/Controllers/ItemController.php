<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    public function getCode(Request $request)
    {
        // Example: agar last item ka code le kar next code generate karna ho
        $lastItem = Item::latest('id')->first();
        $nextCode = $lastItem ? 'ITM-' . str_pad($lastItem->id + 1, 5, '0', STR_PAD_LEFT) : 'ITM-00001';

        return response()->json(['code' => $nextCode]);
    }

    // Show all Items (DataTables)
    public function index(Request $request)
    {

        if ($request->has('getCode')) {
            $lastItem = Item::latest('id')->first();
            $nextNumber = $lastItem ? $lastItem->id + 1 : 1;
            $nextCode = 'ITM-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

            return response()->json([
                'success' => true,
                'code' => $nextCode
            ]);
        }

        if ($request->ajax()) {
            $data = Item::latest();


            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '
                        <a class="las la-pen text-secondary fs-18 editBtn" data-id="' . $row->id . '"></a>
                        <a class="las la-trash-alt text-secondary fs-18 deleteBtn" data-id="' . $row->id . '"></a>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('items.index');
    }

    // Store new Item
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'uom'         => 'required|string|max:50',
            'remarks'     => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // Auto-generate unique item_code
            $last = Item::latest('id')->first();
            $nextNumber = $last ? $last->id + 1 : 1;
            $itemCode = 'ITM-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

            $item = Item::create([
                'item_code'   => $itemCode,
                'description' => $request->description,
                'uom'         => $request->uom,
                'remarks'     => $request->remarks,
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Item created successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // Show single item (for edit)
    public function show($id)
    {
        $item = Item::find($id);

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Item not found.'], 404);
        }

        return response()->json(['success' => true, 'data' => $item]);
    }

    // Update Item
    public function update(Request $request, $id)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'uom'         => 'required|string|max:50',
            'remarks'     => 'nullable|string|max:255',
        ]);

        $item = Item::find($id);

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Item not found.'], 404);
        }

        $item->update([
            'description' => $request->description,
            'uom'         => $request->uom,
            'remarks'     => $request->remarks,
        ]);

        return response()->json(['success' => true, 'message' => 'Item updated successfully.']);
    }

    // Delete Item
    public function destroy($id)
    {
        $item = Item::find($id);

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Item not found.'], 404);
        }

        $item->delete();

        return response()->json(['success' => true, 'message' => 'Item deleted successfully.']);
    }
}
