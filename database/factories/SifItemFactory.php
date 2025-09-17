<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SifItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'item_code'   => strtoupper($this->faker->bothify('ITM-#####')),
            'description' => $this->faker->sentence(6),
            'uom'         => $this->faker->randomElement(['PCS','KG','MTR','LTR']),
            'quantity'    => $this->faker->randomFloat(2, 1, 500),
        ];
    }
}
