<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Requisition;
use App\Models\Department;

class RequisitionFactory extends Factory
{
    protected $model = Requisition::class;

    public function definition(): array
    {
        return [
            'req_no' => 'REQ-' . $this->faker->unique()->numberBetween(100000, 999999),
            'date' => $this->faker->date(),
            'req_date' => $this->faker->date(),
            'requested_by' => $this->faker->name(),
            'department_id' => Department::inRandomOrder()->first()->id, // valid department
            'project_name' => $this->faker->sentence(3),
            'remarks' => $this->faker->sentence(6),
        ];
    }
}
