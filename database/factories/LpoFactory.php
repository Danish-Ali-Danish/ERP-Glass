<?php

namespace Database\Factories;

use App\Models\Lpo;
use Illuminate\Database\Eloquent\Factories\Factory;

class LpoFactory extends Factory
{
    protected $model = Lpo::class;

    public function definition(): array
    {
        static $counter = 1; // har bar auto-increment hoga

        return [
            'supplier_name' => $this->faker->company,
            'date' => $this->faker->date(),
            'contact_person' => $this->faker->name,
            'lpo_no' => 'LPO-' . str_pad($counter++, 6, '0', STR_PAD_LEFT), // ✅ unique every time
            'contact_no' => $this->faker->phoneNumber,
            'pi_no' => 'PI-' . str_pad($this->faker->unique()->numberBetween(1, 999999), 6, '0', STR_PAD_LEFT),
            'supplier_trn' => $this->faker->numerify('TRN#######'),
            'address' => $this->faker->address,
            'sub_total' => 0,
            'vat' => 0,
            'net_total' => 0,
        ];
    }
}
