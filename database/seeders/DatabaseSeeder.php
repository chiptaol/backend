<?php

namespace Database\Seeders;

use App\Models\Cinema;
use App\Models\Format;
use App\Models\Hall;
use App\Models\User;
use Database\Factories\CinemaFactory;
use Database\Factories\HallFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->count(1)->create([
            'email' => 'fbb@gmail.com',
            'password' => bcrypt('300620036Fbb')
        ]);

        Format::factory()->count(2)
            ->create();

        Cinema::factory()
            ->count(4)->create();




    }
}
