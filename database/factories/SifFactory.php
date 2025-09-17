<?php

namespace Database\Factories;
use App\Models\Sif;
use Illuminate\Database\Eloquent\Factories\Factory;

class SifFactory extends Factory
{
    public function definition(): array
    {
        static $counter = 1;

        return [
            'sif_no'       => 'SIF-' . str_pad($counter++, 8, '0', STR_PAD_LEFT),
            'date'         => $this->faker->date(),
            'issued_date'  => $this->faker->date(),
            'requested_by' => $this->faker->name(),
            'department_id'=> rand(1, 10), // maan lo 10 departments hain
            'project_name' => $this->faker->sentence(3),
            'remarks'      => $this->faker->optional()->sentence(),
        ];
    }
}
