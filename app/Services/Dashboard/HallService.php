<?php

namespace App\Services\Dashboard;

use App\Models\Cinema;
use App\Models\Hall;

final class HallService
{
    public function store(array $validatedData, Cinema $cinema)
    {
        $hall = $cinema->halls()->create($validatedData);

        return $hall->fresh();
    }

    public function update(array $validatedData, Hall $hall)
    {
        $hall->update($validatedData);

        return $hall->fresh();
    }
}
