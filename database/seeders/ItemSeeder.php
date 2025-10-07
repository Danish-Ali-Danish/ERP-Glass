<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Glass industry–relevant data
        $sizes = [
            '2x3 ft', '3x4 ft', '4x6 ft', '5x8 ft', '6x8 ft',
            '8x10 ft', '10x12 ft', '12x15 ft'
        ];

        $colors = [
            'Clear', 'Tinted Green', 'Tinted Blue', 'Frosted', 'Bronze', 'Reflective Silver'
        ];

        $types = [
            'Float Glass', 'Tempered Glass', 'Laminated Glass', 'Mirror', 'Double Glazed Unit', 'Pattern Glass'
        ];

        $uoms = ['SQFT', 'PCS', 'MM', 'SET'];

        $records = [];

        for ($i = 1; $i <= 100; $i++) {
            $type = $types[array_rand($types)];
            $size = $sizes[array_rand($sizes)];
            $color = $colors[array_rand($colors)];
            $uom = $uoms[array_rand($uoms)];

            $records[] = [
                'item_code'   => 'ITM-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'description' => $type . ' ' . $color . ' ' . $size,
                'uom'         => $uom,
                'size'        => $size,
                'color'       => $color,
                'type'        => $type,
                'remarks'     => 'Standard ' . strtolower($type) . ' item for glass production',
                'created_at'  => now(),
                'updated_at'  => now(),
            ];

            if ($i % 1000 == 0) {
                DB::table('items')->insert($records);
                $records = [];
            }
        }

        if (!empty($records)) {
            DB::table('items')->insert($records);
        }
    }
}
