<?php

namespace App\Services\Dashboard;

use App\Http\Requests\Dashboard\CinemaStoreFormRequest;
use App\Models\Cinema;

final class CinemaService
{
    public function store(array $validatedData)
    {
        return Cinema::create($validatedData);
    }

    public function update(array $validatedData, Cinema $cinema)
    {
        return $cinema->update($validatedData);
    }

    public function delete(Cinema $cinema)
    {
        return $cinema->delete();
    }
}
