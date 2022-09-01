<?php

namespace App\Http\Controllers;

use App\Http\Requests\CinemaNearestRequest;
use App\Http\Resources\CinemaExtendedResource;
use App\Http\Resources\Dashboard\CinemaResource;
use App\Http\Resources\CinemaMovieResource;
use App\Models\Cinema;
use App\Models\Movie;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
     * @return AnonymousResourceCollection
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
     * @return AnonymousResourceCollection
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

    /**
     *
     * @OA\Get (
     *     path="/api/cinemas/{cinema-id}/seances",
     *     summary="Get a list of seances for specific cinema",
     *     tags={"Cinemas"},
     *
     *     @OA\Parameter (ref="#/components/parameters/cinema-id-path"),
     *
     *     @OA\Response (
     *          response=200,
     *          description="Success (OK) [Response](https://api.chiptaol.uz/api/example-responses/cinemas-seances)"
     *     )
     * )
     *
     * @param Request $request
     * @param $cinemaId
     * @return JsonResponse
     */
    public function seances(Request $request, $cinemaId)
    {
        $validator = Validator::make([
            'date' => $request->query('date')
        ], [
            'date' => ['filled', 'string', 'date', 'date_format:Y-m-d', 'after_or_equal:today']
        ]);

        $cinema = Cinema::select('id')
            ->without('logo')
            ->findOrFail($cinemaId);

        $schedule = $cinema->seances()
            ->select('start_date')
            ->without('format')
            ->upcoming()
            ->groupBy('start_date')
            ->pluck('start_date');

        $seances = Movie::select(['id', 'title', 'original_title', 'rating', 'genres', 'duration', 'poster_path'])
            ->with(['premieres' => function (HasMany $query) use ($cinema, $validator, $schedule) {
                $query->select('id', 'movie_id', 'cinema_id')
                    ->where('cinema_id', '=', $cinema->id)
                    ->whereHas('seances', function (Builder $query) use ($validator, $schedule) {
                        return $query->upcoming()
                            ->where('start_date', '=', $validator->valid()['date'] ?? ($schedule[0] ?? now()->format('Y-m-d')));
                    })->with(['seances' => function (HasMany $q) use ($validator, $schedule) {
                        return $q->select('id', 'start_date_time', 'prices', 'premiere_id', 'format_id', 'hall_id')
                            ->upcoming()
                            ->where('start_date', '=', $validator->valid()['date'] ?? ($schedule[0] ?? now()->format('Y-m-d')))
                            ->with('hall:id,title,is_vip')
                            ->orderBy('start_date_time');
                    }]);
            }])->orderByDesc('id')
            ->get()
            ->filter(fn($item) => $item->premieres->isNotEmpty());


        return new JsonResponse([
            'schedule' => $schedule,
            'data' => CinemaMovieResource::collection($seances)
        ]);

    }
}
