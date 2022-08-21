<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\MovieSearchRequest;
use App\Services\Dashboard\TMDBService;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function __construct(private TMDBService $tmdbService){}

    /**
     *
     * @OA\Get (
     *     path="/api/dashboard/movies/search/{title}",
     *     summary="Get similar movies with provided title",
     *     tags={"Dashboard Seances"},
     *
     *     @OA\Parameter (
     *          in="path",
     *          name="title",
     *          example="Мстители",
     *          required=true
     *     ),
     *
     *     @OA\Response (
     *          response=200,
     *          description="Success (OK) [Response](https://api.chiptaol.uz/api/example-responses/dashboard-movies-search)"
     *     ),
     *
     *     @OA\Response (
     *          response=400,
     *          description="Failure (Bad Request)",
     *          @OA\JsonContent (
     *              @OA\Property (property="message", type="string", default="TMDB вернул неуспешный ответ, повторите еще раз.")
     *          )
     *     )
     * )
     *
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     */
    public function search($title)
    {
        $similarMovies = $this->tmdbService->searchMovieByTitle($title);

        return response()->json(only($similarMovies, 'id', 'title', 'release_date'));
    }
}
