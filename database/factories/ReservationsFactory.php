<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Parking>
 */
class ReservationsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'parking_id' =>  rand(0, 2),
            'vacancy_id' =>  rand(0, 2),
            'car_id' =>  rand(0, 2),
            'start_date'     => "05/12/2023",
            'end_date'  => null,
            'status'  =>  rand(0, 2),
        ];
    }
}
