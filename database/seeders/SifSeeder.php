<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sif;
use App\Models\SifItem;

class SifSeeder extends Seeder
{
    public function run(): void
    {
        $chunkSize = 1000; // ek batch mein
        $total = 100000;   // 1 lakh records

        for ($i = 0; $i < $total / $chunkSize; $i++) {
            $sifs = Sif::factory()->count($chunkSize)->create();

            // Har SIF ke saath 2–5 random items
            foreach ($sifs as $sif) {
                SifItem::factory()->count(rand(2,5))->create([
                    'sif_id' => $sif->id,
                ]);
            }
        }
    }
}
