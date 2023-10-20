<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Parking>
 */
class EmployeesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'       => fake()->name(),
            'cpf'        => '123123123',
            'email'      => 'exemplo@exemplo.com',
            'office'     => fake()->name(),
            'active'     => (bool) rand(0, 1),
            'parking_id' => 1,
        ];
    }
}
