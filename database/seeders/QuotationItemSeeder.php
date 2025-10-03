<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\QuotationItem;

class QuotationItemSeeder extends Seeder
{
    public function run(): void
    {
        // Quotation 1 ke items
        QuotationItem::create([
            'quotation_id' => 1,
            'item_id'      => null,
            'description'  => 'Cement Bags',
            'unit'         => 'Bag',
            'quantity'     => 100,
            'unit_price'   => 50,
            'total'        => 5000,
        ]);

        // Quotation 2 ke items
        QuotationItem::create([
            'quotation_id' => 2,
            'item_id'      => null,
            'description'  => 'Bricks',
            'unit'         => 'Piece',
            'quantity'     => 2000,
            'unit_price'   => 5,
            'total'        => 10000,
        ]);
    }
}
