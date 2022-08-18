<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cinema>
 */
class CinemaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => $this->faker->realText(20),
            'address' => $this->faker->address(),
            'reference_point' => mt_rand(0, 1) ? $this->faker->address() : null,
            'longitude' => $this->faker->longitude(),
            'latitude' => $this->faker->latitude()
        ];
    }
}
