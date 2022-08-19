<?php

namespace App\Services\Dashboard;

use App\Http\Requests\Dashboard\CinemaStoreFormRequest;
use App\Models\Cinema;
use Illuminate\Support\Facades\Storage;

final class CinemaService
{
    public function store(array $validatedData)
    {
        $cinema = Cinema::create($validatedData);

        return $cinema->fresh();
    }

    public function update(array $validatedData, Cinema $cinema)
    {
        if ($validatedData['logo_id'] !== $cinema->logo_id && isset($cinema->logo)) {
            Storage::disk('public')->delete(str_replace('storage/', '', $cinema->logo->path));
            $cinema->logo->delete();
        }

        $cinema->update($validatedData);

        return $cinema->fresh();
    }
}
