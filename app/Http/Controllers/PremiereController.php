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
use Illuminate\Http\JsonResponse;
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
     *     parameter="seance-date-query"
     * )
     *
     *
     * @OA\Get (
     *     path="/api/premieres",
     *     summary="Get a list of all premieres for specific day",
     *     tags={"Premieres"},
     *
     *     @OA\Parameter (ref="#/components/parameters/seance-date-query"),
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

        $schedule = Seance::query()
            ->where('start_date', '>=', now()->format('Y-m-d'))
            ->select('start_date')
            ->groupBy('start_date')
            ->get()->pluck('start_date');

        $premieres = Movie::with(['premieres' => fn($q) => $q->orderBy('release_date')])
            ->whereHas('premieres.seances', function ($query) use ($validator, $schedule) {
                return $query->where('start_date', '=', $validator->valid()['date'] ?? ($schedule[0] ?? now()->format('Y-m-d')))
                    ->upcoming();
            })->get();

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
        $premieres = Movie::with(['premieres' => fn($q) => $q->orderBy('release_date')])
            ->whereHas('premieres', function (Builder $builder) {
                return $builder->actual()
                    ->whereHas('seances', fn($q) => $q->upcoming());
            })->select('id', 'title', 'backdrop_path')
            ->limit(5)
            ->get();

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
     *     @OA\Parameter (ref="#/components/parameters/premiere-id-path"),
     *
     *     @OA\Response (
     *          response=200,
     *          description="Success (OK) [Response](https://api.chiptaol.uz/api/example-responses/premieres-movie)"
     *     )
     * )
     *
     * @param $movieId
     * @return MovieExtendedResource
     */
    public function movie($movieId)
    {
        $movie = Movie::with(['premieres' => fn($q) => $q->orderBy('release_date')])->findOrFail($movieId);

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
     *     @OA\Parameter (ref="#/components/parameters/seance-date-query"),
     *
     *     @OA\Response (
     *          response=200,
     *          description="Success (OK) [Response](https://api.chiptaol.uz/api/example-responses/premieres-seances)"
     *     )
     * )
     *
     * @param Request $request
     * @param $movieId
     * @return JsonResponse
     */
    public function seances(Request $request, $movieId)
    {
        $validator = Validator::make([
            'date' => $request->query('date')
        ], [
            'date' => ['filled', 'date', 'date_format:Y-m-d', 'after_or_equal:today']
        ]);

        $schedule = Seance::without('format')
            ->whereRelation('premiere', 'movie_id', '=', $movieId)
            ->where('start_date', '>=', now()->format('Y-m-d'))
            ->upcoming()
            ->select('start_date')
            ->groupBy('start_date')
            ->get()
            ->pluck('start_date');

        $seances = Cinema::query()
            ->select('id', 'title')
            ->whereHas('premieres', function ($query) use ($movieId) {
                return $query->where('movie_id', '=', $movieId);
            })->with(['halls' => function ($query) use ($movieId, $validator, $schedule) {
                return $query->select('id', 'title', 'is_vip', 'cinema_id')
                    ->whereHas('seances', function ($query) use ($movieId, $validator, $schedule) {
                        return $query->where('start_date', '=', $validator->valid()['date'] ?? ($schedule[0] ?? now()->format('Y-m-d')))
                            ->upcoming()
                            ->whereIn('premiere_id', function ($query) use ($movieId) {
                                return $query->select('id')
                                    ->from('premieres')
                                    ->where('movie_id', '=', $movieId);
                            });
                    })->with(['seances' => function ($query) use ($validator, $schedule, $movieId) {
                        return $query->select('id', 'prices', 'format_id', 'start_date_time', 'hall_id')
                            ->where('start_date', '=', $validator->valid()['date'] ?? ($schedule[0] ?? now()->format('Y-m-d')))
                            ->upcoming()
                            ->whereIn('premiere_id', function ($query) use ($movieId) {
                                return $query->select('id')
                                    ->from('premieres')
                                    ->where('movie_id', '=', $movieId);
                            })
                            ->orderBy('start_date_time');
                    }])->orderBy('created_at');
            }])->without('logo')
            ->orderBy('title')
            ->get()
            ->filter(fn($item) => $item->halls->isNotEmpty());



        return new JsonResponse([
            'schedule' => $schedule,
            'data' => CinemaResource::collection($seances)
        ]);
    }
}
