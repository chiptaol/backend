<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\HallStoreFormRequest;
use App\Http\Resources\Dashboard\HallResource;
use App\Models\Cinema;
use App\Services\Dashboard\HallService;
use Illuminate\Http\Request;

class HallController extends Controller
{
    public function __construct(private HallService $service){}

    /**
     *
     * @OA\Parameter (
     *      in="path",
     *      name="cinema-id",
     *      description="Cinema ID",
     *      required=true,
     *      parameter="cinema-id-path"
     * )
     *
     * @OA\Get (
     *     path="/api/dashboard/cinemas/{cinema-id}/halls",
     *     summary="Get a list of halls for specific cinema",
     *     tags={"Dashboard Halls"},
     *
     *     @OA\Parameter (ref="#/components/parameters/cinema-id-path"),
     *
     *     @OA\Response (
     *          response=200,
     *          description="Success (OK) [Response](https://api.chiptaol.uz/api/example-responses/dashboard-halls)"
     *     )
     * )
     *
     *
     * @param $cinemaId
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index($cinemaId)
    {
        $cinema = Cinema::findOrFail($cinemaId);

        return HallResource::collection($cinema->halls()->get());
    }

    /**
     *
     * @OA\Parameter (
     *       in="path",
     *       name="hall-id",
     *       description="Hall ID",
     *       required=true,
     *       parameter="hall-id-path"
     * )
     *
     *
     * @OA\Get (
     *     path="/api/dashboard/cinemas/{cinema-id}/halls/{hall-id}",
     *     summary="Get a specific hall for specific cinema",
     *     tags={"Dashboard Halls"},
     *
     *     @OA\Parameter (ref="#/components/parameters/cinema-id-path"),
     *     @OA\Parameter (ref="#/components/parameters/hall-id-path"),
     *
     *     @OA\Response (
     *          response=200,
     *          description="Success (OK) [Response](https://api.chiptaol.uz/api/example-responses/dashboard-halls-specific)"
     *     ),
     *
     *     @OA\Response (
     *          response=404,
     *          description="Non exists hall-id (Not Found)",
     *          @OA\JsonContent (
     *              @OA\Property (property="message", type="string", default="No query results for model [App\\Models\\Hall] 6")
     *          )
     *     )
     * )
     *
     *
     * @param $cinemaId
     * @param $id
     * @return HallResource
     */
    public function show($cinemaId, $id)
    {
        $hall = Cinema::findOrFail($cinemaId)->halls()->findOrFail($id);

        return new HallResource($hall);
    }

    /**
     * @OA\Post (
     *     path="/api/dashboard/cinemas/{cinema-id}/halls",
     *     summary="Add a new hall for specific cinema",
     *     tags={"Dashboard Halls"},
     *
     *     @OA\Parameter (ref="#/components/parameters/cinema-id-path"),
     *
     *     @OA\RequestBody (
     *          required=true,
     *          @OA\JsonContent (ref="#/components/schemas/HallStoreFormRequest")
     *     ),
     *
     *     @OA\Response (
     *          response=200,
     *          description="Success (OK) [Response](https://api.chiptaol.uz/api/example-responses/dashboard-halls-specific)"
     *     )
     * )
     *
     *
     * @param HallStoreFormRequest $request
     * @param $cinemaId
     * @return HallResource
     */
    public function store(HallStoreFormRequest $request, $cinemaId)
    {
        $cinema = Cinema::findOrFail($cinemaId);

        return new HallResource($this->service->store($request->validated(), $cinema));
    }

    /**
     *
     * @OA\Put (
     *     path="/api/dashboard/cinemas/{cinema-id}/halls/{hall-id}",
     *     summary="Update a specific hall for specific cinema",
     *     tags={"Dashboard Halls"},
     *
     *     @OA\Parameter (ref="#/components/parameters/cinema-id-path"),
     *     @OA\Parameter (ref="#/components/parameters/hall-id-path"),
     *
     *     @OA\RequestBody (
     *          required=true,
     *          @OA\JsonContent (ref="#/components/schemas/HallStoreFormRequest")
     *     ),
     *
     *     @OA\Response (
     *          response=200,
     *          description="Success (OK) [Response](https://api.chiptaol.uz/api/example-responses/dashboard-halls-specific)"
     *     )
     * )
     *
     *
     * @param HallStoreFormRequest $request
     * @param $cinemaId
     * @param $id
     * @return HallResource
     */
    public function update(HallStoreFormRequest $request, $cinemaId, $id)
    {
        $hall = Cinema::findOrFail($cinemaId)->halls()->findOrFail($id);

        return new HallResource($this->service->update($request->validated(), $hall));
    }

    /**
     *
     * @OA\Delete (
     *     path="/api/dashboard/cinemas/{cinema-id}/halls/{hall-id}",
     *     summary="Delete a specific hall for specific cinema",
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
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function delete($cinemaId, $id)
    {
        $hall = Cinema::findOrFail($cinemaId)->halls()->findOrFail($id);
        $hall->delete();

        return response()->noContent();
    }

}
