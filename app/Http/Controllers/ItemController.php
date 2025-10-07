<?php
namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ItemController extends Controller
{

    // Display items or handle DataTables AJAX
    public function index(Request $request)
    {
        if ($request->has('getCode')) {
            $lastItem   = Item::latest('id')->first();
            $nextNumber = $lastItem ? $lastItem->id + 1 : 1;
            $nextCode   = 'ITM-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
            return response()->json(['success' => true, 'code' => $nextCode]);
        }

        if ($request->ajax()) {
            $query = Item::select('id', 'item_code', 'description', 'uom', 'size', 'color', 'type', 'remarks')->latest();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '
                        <a class="las la-pen text-secondary fs-18 editBtn" data-id="' . $row->id . '"></a>
                        <a class="las la-trash-alt text-secondary fs-18 deleteBtn ms-2" data-id="' . $row->id . '"></a>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('items.index');
    }

   
    // Store new item
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'uom'         => 'required|string|max:50',
            'remarks'     => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $last       = Item::latest('id')->first();
            $nextNumber = $last ? $last->id + 1 : 1;
            $itemCode   = 'ITM-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

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

    // Show single item for edit
    public function show($id)
    {
        $item = Item::find($id);
        if (! $item) {
            return response()->json(['success' => false, 'message' => 'Item not found.'], 404);
        }

        return response()->json(['success' => true, 'data' => $item]);
    }

    // Update item
    public function update(Request $request, $id)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'uom'         => 'required|string|max:50',
            'remarks'     => 'nullable|string|max:255',
        ]);

        $item = Item::find($id);
        if (! $item) {
            return response()->json(['success' => false, 'message' => 'Item not found.'], 404);
        }

        $item->update([
            'description' => $request->description,
            'uom'         => $request->uom,
            'remarks'     => $request->remarks,
        ]);

        return response()->json(['success' => true, 'message' => 'Item updated successfully.']);
    }

    // Delete item
    public function destroy($id)
    {
        $item = Item::find($id);
        if (! $item) {
            return response()->json(['success' => false, 'message' => 'Item not found.'], 404);
        }

        $item->delete();
        return response()->json(['success' => true, 'message' => 'Item deleted successfully.']);
    }

    public function search(Request $request)
    {
        $query = $request->get('q') ?? $request->get('query') ?? '';
        $type  = $request->get('type') ?? 'description'; // 'code' or 'description'

        if (trim($query) === '') {
            return response()->json(['results' => []]);
        }

        $items = ItemHelper::searchItems($query, $type);

        $results = $items->map(function ($i) {
            return [
                'id'          => $i->id,
                'item_code'   => $i->item_code,
                'description' => $i->description,
                'uom'         => $i->uom,
                'size'        => $i->size,
                'color'       => $i->color,
                'type'        => $i->type,
                'remarks'     => $i->remarks,
            ];
        });

        return response()->json(['results' => $results]);
    }

}
