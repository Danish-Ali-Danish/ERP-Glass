<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use Illuminate\Support\Facades\DB;


class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $records = [];

        for ($i = 1; $i <= 100; $i++) {
            $records[] = [
                'item_code'   => 'ITM-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'description' => 'Sample Item ' . $i,
                'uom'         => 'PCS',
                'remarks'     => 'Auto seeded record ' . $i,
                'created_at'  => now(),
                'updated_at'  => now(),
            ];

            // Har 1000 records batch insert karega taake memory issue na ho
            if ($i % 1000 == 0) {
                DB::table('items')->insert($records);
                $records = [];
            }
        }

        // Jo bache hue records hain unko bhi insert karna
        if (!empty($records)) {
            DB::table('items')->insert($records);
        }
    }
}
