<?php

namespace App\Http\Controllers;

use App\Http\Requests\CinemaIndexRequest;
use App\Http\Requests\CinemaNearestRequest;
use App\Http\Resources\CinemaExtendedResource;
use App\Http\Resources\CinemaNearestResource;
use App\Http\Resources\Dashboard\CinemaResource;
use App\Models\Cinema;
use App\Rules\CoordinateRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use const http\Client\Curl\Features\HTTP2;

class CinemaController extends Controller
{
    /**
     *
     * @OA\Get (
     *     path="/api/cinemas",
     *     summary="Get a list of all cinemas",
     *     tags={"Cinemas"},
     *
     *     @OA\Response (
     *          response=200,
     *          description="Success (OK) [Response](https://api.chiptaol.uz/api/example-responses/cinemas)"
     *     )
     * )
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $cinemas = Cinema::query()
            ->orderBy('title')
            ->get();

        return CinemaExtendedResource::collection($cinemas);
    }

    /**
     *
     * @OA\Get (
     *     path="/api/cinemas/nearest",
     *     summary="Get a list of nearest cinemas",
     *     tags={"Cinemas"},
     *
     *     @OA\Parameter  (
     *          in="query",
     *          name="latitude",
     *          example=41.2963223
     *     ),
     *
     *     @OA\Parameter  (
     *          in="query",
     *          name="longitude",
     *          example=69.1933242
     *     ),
     *
     *     @OA\Response (
     *          response=200,
     *          description="Success (OK) [Response](https://api.chiptaol.uz/api/example-responses/cinemas-nearest)"
     *     )
     * )
     *
     *
     * @param CinemaNearestRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function indexNearest(CinemaNearestRequest $request)
    {
        $fields = ['id', 'title', 'logo_id', 'address'];

        if ($request->validated('longitude') && $request->validated('latitude')) {
            $longitude = $request->validated('longitude');
            $latitude = $request->validated('latitude');

            return CinemaExtendedResource::collection(
                Cinema::query()
                    ->select($fields)
                    ->addSelect(DB::raw("ROUND(ST_Distance_Sphere(POINT('$longitude', '$latitude'), POINT(longitude, latitude))) as distance"))
                    ->orderBy('distance')
                    ->limit(5)
                    ->get()
                    ->filter(fn($item) => $item->distance < 5000)
            );
        }

        return CinemaExtendedResource::collection(
            Cinema::select($fields)
                ->orderBy('created_at')->limit(5)->get()
        );

    }

    /**
     *
     * @OA\Get (
     *     path="/api/cinemas/{cinema-id}",
     *     summary="Get a specific cinema",
     *     tags={"Cinemas"},
     *
     *     @OA\Parameter (ref="#/components/parameters/cinema-id-path"),
     *
     *     @OA\Response (
     *          response=200,
     *          description="Success (OK) [Response](https://api.chiptaol.uz/api/example-responses/cinemas-specific)"
     *     )
     * )
     *
     * @param $cinemaId
     * @return CinemaResource
     */
    public function show($cinemaId)
    {
        $cinema = Cinema::findOrFail($cinemaId);

        return new CinemaResource($cinema);
    }
}
