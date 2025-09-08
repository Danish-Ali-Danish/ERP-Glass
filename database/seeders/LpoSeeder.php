<?php

namespace Database\Seeders;

use App\Models\Lpo;
use App\Models\LpoItem;
use Illuminate\Database\Seeder;

class LpoSeeder extends Seeder
{
    public function run(): void
    {
        // Test ke liye chhoti count rakho (jaise 100), baad me 100000 kar lena
        Lpo::factory()
            ->count(100000) 
            ->create()
            ->each(function ($lpo) {
                // Har LPO ke liye 2-10 items banayein
                $items = LpoItem::factory()->count(rand(2, 10))->make();

                // Subtotal calculate karein
                $subTotal = $items->sum('total');
                $vat = $subTotal * 0.05;
                $netTotal = $subTotal + $vat;

                // LPO update karein
                $lpo->update([
                    'sub_total' => $subTotal,
                    'vat' => $vat,
                    'net_total' => $netTotal,
                ]);

                // Items insert karein
                $lpo->items()->createMany($items->toArray());
            });
    }
}
