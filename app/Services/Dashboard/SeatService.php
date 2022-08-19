<?php

namespace App\Services\Dashboard;

use App\Models\Hall;

final class SeatService
{
    public function store(array $seats, Hall $hall)
    {
        return $hall->seats()
            ->createMany($seats);
    }
}
