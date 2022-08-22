<?php

namespace App\Services\Dashboard;

use App\Models\Cinema;
use App\Models\Hall;
use Illuminate\Support\Arr;

final class HallService
{
    public function store(array $validatedData, Cinema $cinema)
    {
        $hall = $cinema->halls()->create(Arr::except($validatedData, 'format_ids'));
        $hall->formats()->sync($validatedData['format_ids']);

        return $hall->fresh();
    }

    public function update(array $validatedData, Hall $hall)
    {
        $hall->update(Arr::except($validatedData, 'format_ids'));
        $hall->formats()->sync($validatedData['format_ids']);

        return $hall->fresh();
    }
}
