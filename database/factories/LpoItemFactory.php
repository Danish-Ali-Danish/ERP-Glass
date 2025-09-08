<?php

namespace Database\Factories;

use App\Models\LpoItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class LpoItemFactory extends Factory
{
    protected $model = LpoItem::class;

    public function definition(): array
    {
        $qty = $this->faker->numberBetween(1, 100);
        $price = $this->faker->randomFloat(2, 10, 500);

        return [
            'description' => $this->faker->words(3, true),
            'area' => $this->faker->randomFloat(2, 1, 50),
            'quantity' => $qty,
            'uom' => $this->faker->randomElement(['PCS','SQM','KG']),
            'unit_price' => $price,
            'total' => $qty * $price,
        ];
    }
}
