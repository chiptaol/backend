<?php

namespace App\Services\Dashboard;

use App\Exceptions\BusinessException;
use App\Models\Hall;

final class SeatService
{
    public function store(array $seats, Hall $hall)
    {
        if ($hall->seats->isNotEmpty()) {
            throw new BusinessException(trans('This hall already has seats.'), 400);
        }

        $hall->seats()
            ->createMany($seats);

        return true;
    }
}
