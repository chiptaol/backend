<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\SeanceStoreFormRequest;
use App\Http\Resources\Dashboard\SeanceResource;
use App\Models\Cinema;
use App\Models\Movie;
use App\Services\Dashboard\SeanceService;
use App\Services\Dashboard\TMDBService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SeanceController extends Controller
{
    public function __construct(private SeanceService $service){}

    /**
     *
     * @OA\Post (
     *     path="/api/dashboard/cinemas/{cinema-id}/seances",
     *     summary="Add a new seance for this cinema halls",
     *     tags={"Dashboard Seances"},
     *
     *     @OA\Parameter (ref="#/components/parameters/cinema-id-path"),
     *
     *     @OA\RequestBody (
     *          required=true,
     *          @OA\JsonContent (ref="#/components/schemas/SeanceStoreFormRequest")
     *     ),
     *
     *     @OA\Response (
     *          response=204,
     *          description="Success (No content)"
     *     ),
     *
     *     @OA\Response (
     *          response=422,
     *          description="Invalid prices (Unproccessable content)",
     *          @OA\JsonContent (
     *              @OA\Property (property="message", type="string", default="Что-то пошло не так при проверке типа кресла и выборе цены, обратите внимание на поля - vip_seat_price, standard_seat_price.")
     *          )
     *     )
     * )
     *
     * @param SeanceStoreFormRequest $request
     * @param TMDBService $tmdbService
     * @param $cinemaId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(SeanceStoreFormRequest $request, TMDBService $tmdbService ,$cinemaId)
    {
        $cinema = Cinema::findOrFail($cinemaId);

        return $this->service->store($request->validated(), $cinema, $tmdbService)
            ? response()->noContent()
            : response()->json([
                'message' => trans('Something went wrong.')
            ], 400);
    }

    /**
     *
     * @OA\Parameter (
     *     name="seance-id",
     *     in="path",
     *     required=true,
     *     parameter="seance-id-path",
     *     example=99
     * )
     *
     *
     * @OA\Get (
     *     path="/api/dashboard/cinemas/{cinema-id}/seances/{seance-id}",
     *     summary="Get a specific seance data",
     *     tags={"Dashboard Seances"},
     *
     *     @OA\Parameter (ref="#/components/parameters/cinema-id-path"),
     *     @OA\Parameter (ref="#/components/parameters/seance-id-path"),
     *
     *     @OA\Response (
     *          response=200,
     *          description="Success (OK) [Response](https://api.chiptaol.uz/api/example-responses/dashboard-seances-specific)"
     *     )
     * )
     *
     * @param $cinemaId
     * @param $seanceId
     * @return SeanceResource
     */
    public function show($cinemaId, $seanceId)
    {
        $seance = Cinema::findOrFail($cinemaId)
            ->seances()
            ->findOrFail($seanceId);

        return new SeanceResource($seance);
    }

    /**
     *
     * @OA\Delete (
     *     path="/api/dashboard/cinemas/{cinema-id}/seances/{seance-id}",
     *     summary="Delete specific seance",
     *     tags={"Dashboard Seances"},
     *
     *     @OA\Parameter (ref="#/components/parameters/cinema-id-path"),
     *     @OA\Parameter (ref="#/components/parameters/seance-id-path"),
     *
     *     @OA\Response (
     *          response=204,
     *          description="Success (No content)"
     *     )
     * )
     *
     *
     * @param $cinemaId
     * @param $seanceId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function delete($cinemaId, $seanceId)
    {
        $seance = Cinema::findOrFail($cinemaId)
            ->seances()
            ->findOrFail($seanceId);

        if (Carbon::parse($seance->start_date)->timestamp === strtotime('today')) {
            return response()->json([
                'message' => trans('Seance starts today and could not be deleted.')
            ], 403);
        }

        $seance->delete();

        return response()->noContent();
    }
}
