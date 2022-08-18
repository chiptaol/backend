<?php

namespace App\Http\Controllers\Dashboard;

use App\Actions\StoreFileAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\CinemaStoreFormRequest;
use App\Http\Requests\Dashboard\CinemaStoreLogoFormRequest;
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
        $cinemas = Cinema::orderByDesc('created_at')->get();

        return CinemaResource::collection($cinemas);
    }

    /**
     *
     * @OA\Get (
     *     path="/api/dashboard/cinemas/{id}",
     *     summary="Get a specific cinema",
     *     tags={"Dashboard Cinemas"},
     *
     *     @OA\Response (
     *          response=200,
     *          description="Success (OK) [Response](https://api.chiptaol.uz/api/example-responses/dashboard-cinemas-specific)",
     *     )
     * )
     *
     *
     * @param int $id
     * @return CinemaResource
     */
    public function show(int $id)
    {
        $cinema = Cinema::findOrFail($id);

        return new CinemaResource($cinema);
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
     *          response=201,
     *          description="Success (Created) [Response](https://api.chiptaol.uz/api/example-responses/dashboard-cinemas-specific)",
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
     * @return CinemaResource
     */
    public function store(CinemaStoreFormRequest $request)
    {
        $cinema = $this->service->store($request->validated());

        return new CinemaResource($cinema);
    }

    /**
     *
     * @OA\Post (
     *     path="/api/dashboard/cinemas/logo",
     *     summary="Store a logo for cinema",
     *     tags={"Dashboard Cinemas"},
     *
     *     @OA\RequestBody (
     *          required=true,
     *          @OA\JsonContent (
     *              @OA\Property (property="file", type="file", example="file")
     *          )
     *     ),
     *
     *     @OA\Response (
     *          response=200,
     *          description="Success (OK)",
     *          @OA\JsonContent (
     *              @OA\Property (property="id", type="string", example="929e45b4-084c-4160-b314-13cf7e0407d8")
     *          )
     *     )
     * )
     *
     *
     *
     * @param CinemaStoreLogoFormRequest $request
     * @param StoreFileAction $action
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeLogo(CinemaStoreLogoFormRequest $request, StoreFileAction $action)
    {
        return response()->json([
            'id' => $action($request->validated('file'), 'cinema-logo')
        ], 201);
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
