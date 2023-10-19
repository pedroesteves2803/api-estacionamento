<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Parking>
 */
class CarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "plate" =>  "ZZZ2Y00",
            "model" =>  fake()->name(),
            "color" =>  fake()->name(),
            "parking_id" =>  1
        ];


    }
}
