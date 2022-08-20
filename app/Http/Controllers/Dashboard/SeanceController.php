<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\SeanceStoreFormRequest;
use App\Models\Cinema;
use App\Models\Movie;
use App\Services\Dashboard\SeanceService;
use App\Services\Dashboard\TMDBService;
use Illuminate\Http\Request;

class SeanceController extends Controller
{
    public function __construct(private SeanceService $service){}

    public function store(SeanceStoreFormRequest $request, TMDBService $tmdbService ,$cinemaId, $hallId)
    {
        $hall = Cinema::findOrFail($cinemaId)
            ->halls()->findOrFail($hallId);

        $serviceResponse = $this->service->store($request->validated(), $tmdbService, $hall);
    }
}
