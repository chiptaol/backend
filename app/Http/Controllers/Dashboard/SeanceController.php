<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\SeanceStoreFormRequest;
use App\Models\Cinema;
use Illuminate\Http\Request;

class SeanceController extends Controller
{
    public function store(SeanceStoreFormRequest $request, $cinemaId, $hallId)
    {
        $hall = Cinema::findOrFail($cinemaId)
            ->halls()->findOrFail($hallId);

        //
    }
}
