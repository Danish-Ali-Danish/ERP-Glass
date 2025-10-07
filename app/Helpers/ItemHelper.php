<?php

namespace App\Helpers;

use App\Models\Item;

class ItemHelper
{
    /**
     * Search items by description or code (reusable)
     *
     * @param string|null $query
     * @param string|null $type  'code' ya 'description'
     * @return \Illuminate\Support\Collection
     */
    public static function searchItems($query = null, $type = null)
    {
        return Item::query()
            ->select('id', 'item_code', 'description', 'uom', 'size', 'color', 'type', 'remarks')
            ->when($query, function ($q) use ($query, $type) {
                if ($type === 'code') {
                    $q->where('item_code', 'like', "%{$query}%");
                } else {
                    $q->where('description', 'like', "%{$query}%");
                }
            })
            ->limit(10)
            ->get();
    }
}
