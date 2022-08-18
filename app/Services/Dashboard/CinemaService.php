<?php

namespace App\Services\Dashboard;

use App\Http\Requests\Dashboard\CinemaStoreFormRequest;
use App\Models\Cinema;
use Illuminate\Support\Facades\Storage;

final class CinemaService
{
    public function store(array $validatedData)
    {
        return Cinema::create($validatedData);
    }

    public function update(array $validatedData, Cinema $cinema)
    {
//        if ($validatedData['logo_id'] !== $cinema->logo_id) {
//            Storage::disk('public')->delete()
//        }

        return $cinema->update($validatedData);
    }

    public function delete(Cinema $cinema)
    {
        return $cinema->delete();
    }
}
