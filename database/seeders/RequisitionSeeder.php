<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Requisition;
use App\Models\RequisitionItem;
use Illuminate\Support\Facades\DB;

class RequisitionSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        RequisitionItem::truncate();
        Requisition::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $total = 100; // 1 lakh
        $batchSize = 500; // batching

        for ($i = 0; $i < $total; $i += $batchSize) {
            $requisitions = Requisition::factory($batchSize)->create();
            $itemsToInsert = [];

            foreach ($requisitions as $req) {
                $itemsCount = rand(1,5);
                for ($j = 0; $j < $itemsCount; $j++) {
                    $itemsToInsert[] = [
                        'requisition_id' => $req->id,
                        'item_code' => 'ITM' . rand(1000,9999),
                        'description' => 'Sample Item ' . rand(1,1000),
                        'uom' => 'pcs',
                        'quantity' => rand(1,100),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if(!empty($itemsToInsert)){
                RequisitionItem::insert($itemsToInsert);
            }

            echo "Inserted batch: ".($i + $batchSize)."\n";
        }

        echo "Requisitions seeding completed!\n";
    }
}
