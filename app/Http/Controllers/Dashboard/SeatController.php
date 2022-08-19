<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\SeatStoreFormRequest;
use App\Http\Resources\Dashboard\SeatResource;
use App\Models\Cinema;
use App\Models\Hall;
use App\Services\Dashboard\SeatService;
use Illuminate\Http\Request;

class SeatController extends Controller
{
    public function __construct(private SeatService $service){}

    public function index(int $cinemaId, int $hallId)
    {
        $seats = Cinema::without('logo')
            ->findOrFail($cinemaId)
            ->halls()
            ->findOrFail($hallId)
            ->seats()
            ->orderBy('row')
            ->orderBy('place')
            ->get();

        return SeatResource::collection($seats);
    }

    public function store(SeatStoreFormRequest $request, int $cinemaId, int $hallId)
    {
        $hall = Cinema::without('logo')
            ->findOrFail($cinemaId)
            ->halls()
            ->findOrFail($hallId);

        return SeatResource::collection($this->service->store($request->validated('seats'), $hall));
    }

    public function delete(int $cinemaId, int $hallId)
    {
        $hall = Cinema::without('logo')
            ->findOrFail($cinemaId)
            ->halls()
            ->findOrFail($hallId);

        return $hall->seats()->delete()
            ? response()->noContent()
            : response()->json([
                'message' => trans('Something went wrong.')
            ]);
    }
}
