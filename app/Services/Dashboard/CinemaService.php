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
        $cinema->load('logo:id,path');

        return $cinema;
    }

    public function update(array $validatedData, Cinema $cinema)
    {
        if ($validatedData['logo_id'] !== $cinema->logo_id) {
            Storage::disk('public')->delete(str_replace('storage/', '', $cinema->logo->path));
            $cinema->logo->delete();
        }

        return $cinema->update($validatedData);
    }

    public function delete(Cinema $cinema)
    {
        return $cinema->delete();
    }
}
