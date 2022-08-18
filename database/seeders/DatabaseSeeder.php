<?php

namespace Database\Seeders;

use App\Models\Cinema;
use App\Models\User;
use Database\Factories\CinemaFactory;
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

        Cinema::factory()->count(5)->create();
        Cinema::create([
            'title' => 'Magic City',
            'address' => 'some address',
            'longitude' => 69.245077,
            'latitude' => 41.303663
        ]);
        Cinema::create([
            'title' => 'Minor метро',
            'address' => 'some address',
            'longitude' => 69.283407,
            'latitude' => 41.326226

        ]);


    }
}
