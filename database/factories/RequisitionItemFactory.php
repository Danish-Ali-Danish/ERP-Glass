<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\RequisitionItem;

class RequisitionItemFactory extends Factory
{
    protected $model = RequisitionItem::class;

    public function definition(): array
    {
        return [
            'item_code' => 'ITM' . $this->faker->numberBetween(1000, 9999),
            'description' => $this->faker->sentence(3),
            'uom' => 'pcs',
            'quantity' => $this->faker->numberBetween(1, 100),
        ];
    }
}
