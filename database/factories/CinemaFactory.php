<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use phpDocumentor\Reflection\Types\Self_;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cinema>
 */
class CinemaFactory extends Factory
{
    private static $i = 0;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $realData = [
            [
                'cinema_name' => 'Next Cinema',
                'cinema_latitude' => 41.2988352,
                'cinema_longitude' => 69.2451793,
            ],
            [
                'cinema_name' => 'Salom Cinema',
                'cinema_longitude' => 69.3411065,
                'cinema_latitude' => 41.3277166,
            ],
            [
                'cinema_name' => 'Parus Cinema',
                'cinema_latitude' => 41.2919784,
                'cinema_longitude' => 69.2088091,
            ],
            [
                'cinema_name' => 'Riviera',
                'cinema_latitude' => 41.3392565,
                'cinema_longitude' => 69.252237,

            ]
        ];

        return [
            'title' => $realData[self::$i]['cinema_name'],
            'address' => $this->faker->address(),
            'reference_point' => mt_rand(0, 1) ? $this->faker->address() : null,
            'longitude' => $realData[self::$i]['cinema_longitude'],
            'latitude' => $realData[self::$i++]['cinema_latitude'],
            'phone' => $this->faker->phoneNumber()
        ];
    }
}
