<?php

namespace App\Http\Controllers;

use App\Http\Requests\CinemaIndexRequest;
use App\Http\Resources\CinemaExtendedResource;
use App\Models\Cinema;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CinemaController extends Controller
{
    public function index(CinemaIndexRequest $request)
    {
        if ($request->has('longitude') && $request->has('latitude')) {
            $longitude = $request->validated('longitude');
            $latitude = $request->validated('latitude');

            $nearestCinemas = Cinema::query()
                ->select('*')
                ->addSelect(DB::raw("ROUND(ST_Distance_Sphere(POINT('$longitude', '$latitude'), POINT(longitude, latitude))) as distance"))
                ->orderBy('distance')
                ->get()
                ->filter(fn($item) => $item->distance < 2000);
        }

        $allCinemas = Cinema::query()
            ->orderBy('title')
            ->get();

        return new JsonResponse([
            'nearest' => CinemaExtendedResource::collection($nearestCinemas ?? []),
            'all' => CinemaExtendedResource::collection($allCinemas)
        ]);

    }

}
