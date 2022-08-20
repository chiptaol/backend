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

    /**
     *
     * @OA\Get (
     *     path="/api/dashboard/cinemas/{cinema-id}/halls/{hall-id}/seats",
     *     summary="Get a list of seats for specific hall",
     *     tags={"Dashboard Halls"},
     *
     *     @OA\Parameter (ref="#/components/parameters/cinema-id-path"),
     *     @OA\Parameter (ref="#/components/parameters/hall-id-path"),
     *
     *     @OA\Response (
     *          response=200,
     *          description="Success (OK) [Response](https://api.chiptaol.uz/api/example-responses/dashboard-seats)"
     *     )
     * )
     *
     *
     * @param $cinemaId
     * @param $hallId
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index($cinemaId, $hallId)
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

    /**
     *
     * @OA\Post (
     *     path="/api/dashboard/cinemas/{cinema-id}/halls/{hall-id}/seats",
     *     summary="Add new seats for specific hall",
     *     tags={"Dashboard Halls"},
     *
     *     @OA\Parameter (ref="#/components/parameters/cinema-id-path"),
     *     @OA\Parameter (ref="#/components/parameters/hall-id-path"),
     *
     *     @OA\RequestBody (
     *          required=true,
     *          @OA\JsonContent (ref="#/components/schemas/SeatStoreFormRequest")
     *     ),
     *
     *     @OA\Response (
     *          response=201,
     *          description="Success (Created)"
     *     )
     *
     * )
     *
     *
     * @param SeatStoreFormRequest $request
     * @param $cinemaId
     * @param $hallId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(SeatStoreFormRequest $request, $cinemaId, $hallId)
    {
        $hall = Cinema::without('logo')
            ->findOrFail($cinemaId)
            ->halls()
            ->findOrFail($hallId);

        return $this->service->store($request->validated('seats'), $hall)
            ? response()->noContent()
            : response()->json([
                'message' => trans('Something went wrong.')
            ], 400);
    }

    /**
     *
     * @OA\Delete (
     *     path="/api/dashboard/cinemas/{cinema-id}/halls/{hall-id}/seats",
     *     summary="Delete all seats for specific hall",
     *     tags={"Dashboard Halls"},
     *
     *     @OA\Parameter (ref="#/components/parameters/cinema-id-path"),
     *     @OA\Parameter (ref="#/components/parameters/hall-id-path"),
     *
     *     @OA\Response (
     *          response=204,
     *          description="Success (No Content)"
     *     )
     * )
     *
     *
     * @param $cinemaId
     * @param $hallId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function delete($cinemaId, $hallId)
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
