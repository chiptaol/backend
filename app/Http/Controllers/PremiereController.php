<?php

namespace App\Http\Controllers;

use App\Http\Requests\SeanceFilterRequest;
use App\Http\Resources\MovieExtendedResource;
use App\Http\Resources\MoviePremiereResource;
use App\Http\Resources\MovieResource;
use App\Http\Resources\PremiereResource;
use App\Http\Resources\CinemaResource;
use App\Models\Cinema;
use App\Models\Movie;
use App\Models\Premiere;
use App\Models\Seance;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PremiereController extends Controller
{
    /**
     *
     * @OA\Parameter (
     *     name="date",
     *     in="query",
     *     description="Format: `Y-m-d`, if not specified today date will be taken.",
     *     example="2022-08-23",
     *     parameter="date-query"
     * )
     *
     *
     * @OA\Get (
     *     path="/api/premieres",
     *     summary="Get a list of all premieres for specific day",
     *     tags={"Premieres"},
     *
     *     @OA\Parameter (ref="#/components/parameters/date-query"),
     *
     *     @OA\Response (
     *          response=200,
     *          description="Success (OK) [Response](https://api.chiptaol.uz/api/example-responses/premieres-of-the-day)"
     *     )
     * )
     *
     * @param SeanceFilterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $validator = Validator::make([
            'date' => $request->query('date')
        ], [
            'date' => ['filled', 'date', 'date_format:Y-m-d', 'after_or_equal:today']
        ]);

        $premieres = Movie::with('premiere')
            ->whereHas('premiere.seances', function ($query) use ($validator) {
                return $query->where('start_date', '=', $validator->valid()['date'] ?? now()->format('Y-m-d'));
            })->get();

        $schedule = Seance::query()
            ->select('start_date')
            ->groupBy('start_date')
            ->get()->pluck('start_date');

        return response()->json([
            'schedule' => $schedule,
            'premieres' => MovieResource::collection($premieres),
        ]);
    }

    /**
     *
     * @OA\Get (
     *     path="/api/premieres/actual",
     *     summary="Get a list of actual premieres",
     *     tags={"Premieres"},
     *
     *     @OA\Response (
     *          response=200,
     *          description="Success (OK) [Response](https://api.chiptaol.uz/api/example-responses/premieres-actual)"
     *     )
     * )
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function indexActual()
    {
        $premieres = Movie::with('premiere')
            ->whereHas('premiere', function (Builder $builder) {
                return $builder->actual();
            })->get();

        return MoviePremiereResource::collection($premieres);

    }

    /**
     *
     * @OA\Parameter (
     *     in="path",
     *     required=true,
     *     name="id",
     *     description="Premiere ID",
     *     parameter="premiere-id-path"
     * )
     *
     *
     * @OA\Get (
     *     path="/api/premieres/{id}/movie",
     *     summary="Get an extended data for specific premiere's movie",
     *     tags={"Premieres"},
     *
     *     @OA\Parameter (ref="#/components/parameters/movie-id-path"),
     *
     *     @OA\Response (
     *          response=200,
     *          description="Success (OK) [Response](https://api.chiptaol.uz/api/example-responses/premieres-movie.json)"
     *     )
     * )
     *
     * @param $movieId
     * @return MovieExtendedResource
     */
    public function movie($movieId)
    {
        $movie = Movie::with('premiere')->findOrFail($movieId);

        return new MovieExtendedResource($movie);
    }

    /**
     *
     * @OA\Get (
     *     path="/api/premieres/{id}/seances",
     *     summary="Get a list of seances for specific premiere",
     *     tags={"Premieres"},
     *
     *     @OA\Parameter (ref="#/components/parameters/premiere-id-path"),
     *
     *     @OA\Response (
     *          response=200,
     *          description="Success (OK) [Response](https://api.chiptaol.uz/api/example-responses/premieres-seances)"
     *     )
     * )
     *
     * @param $movieId
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function seances($movieId)
    {
        $premieres = Premiere::with('cinema', 'seances.hall')
            ->where('movie_id', '=', $movieId)
            ->get();


        return CinemaResource::collection($premieres);
    }
}
