<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\CinemaStoreFormRequest;
use App\Http\Requests\Dashboard\CinemaUpdateFormRequest;
use App\Http\Resources\Dashboard\CinemaResource;
use App\Models\Cinema;
use App\Services\Dashboard\CinemaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CinemaController extends Controller
{
    public function __construct(private CinemaService $service){}

    /**
     *
     * @OA\Get (
     *     path="/api/dashboard/cinemas",
     *     summary="Get a list of cinemas",
     *     tags={"Dashboard Cinemas"},
     *
     *     @OA\Response (
     *          response=200,
     *          description="Success (OK) [Response](https://api.chiptaol.uz/api/example-responses/dashboard-cinemas)"
     *     )
     * )
     *
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return CinemaResource::collection(Cinema::orderBy('created_at', 'DESC')->get());
    }

    /**
     *
     * @OA\Post (
     *     path="/api/dashboard/cinemas",
     *     summary="Add a new cinema",
     *     tags={"Dashboard Cinemas"},
     *
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent (ref="#/components/schemas/CinemaStoreFormRequest")
     *     ),
     *
     *     @OA\Response (
     *          response=200,
     *          description="Success (OK)",
     *          @OA\JsonContent (
     *              @OA\Property (property="id", type="integer", example=7)
     *          )
     *     ),
     *
     *     @OA\Response (
     *          response=422,
     *          description="Invalid coordinates (Unproccessable content)",
     *          @OA\JsonContent (
     *              @OA\Property (property="message", type="string", default="Указаны неверные координаты.")
     *          )
     *     )
     * )
     *
     *
     * @param CinemaStoreFormRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CinemaStoreFormRequest $request)
    {
        $cinema = $this->service->store($request->validated());

        return response()->json([
            'id' => $cinema->id
        ]);
    }

    /**
     *
     * @OA\Put (
     *     path="/api/dashboard/cinemas/{id}",
     *     summary="Update an existing cinema",
     *     tags={"Dashboard Cinemas"},
     *
     *     @OA\Parameter (
     *          in="path",
     *          name="id",
     *          description="Cinema ID",
     *          required=true
     *     ),
     *
     *     @OA\RequestBody (
     *          required=true,
     *          @OA\JsonContent (ref="#/components/schemas/CinemaStoreFormRequest")
     *     ),
     *
     *     @OA\Response (
     *          response=204,
     *          description="Success (No Content)"
     *     )
     * )
     *
     *
     * @param CinemaUpdateFormRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(CinemaUpdateFormRequest $request, int $id)
    {
        $cinema = Cinema::findOrFail($id);

        return $this->service->update($request->validated(), $cinema)
            ? response()->noContent()
            : response()->json([
                'message' => trans('Updating is not possible, something went wrong.')
            ], 400);
    }

    /**
     *
     * @OA\Delete (
     *     path="/api/dashboard/cinemas/{id}",
     *     summary="Delete an existing cinema",
     *     tags={"Dashboard Cinemas"},
     *
     *     @OA\Parameter (
     *          in="path",
     *          name="id",
     *          description="Cinema ID",
     *          required=true
     *     ),
     *
     *     @OA\Response (
     *          response=204,
     *          description="Success (No Content)"
     *     )
     * )
     *
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function delete(int $id)
    {
        $cinema = Cinema::findOrFail($id);

        return $this->service->delete($cinema)
            ? response()->noContent()
            : response()->json([
                'message' => trans('Deleting is not possible, something went wrong.')
            ]);
    }



}
