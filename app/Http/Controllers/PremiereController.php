<?php

namespace App\Http\Controllers;

use App\Http\Requests\SeanceFilterRequest;
use App\Http\Resources\MoviePremiereResource;
use App\Http\Resources\MovieResource;
use App\Http\Resources\PremiereResource;
use App\Models\Movie;
use App\Models\Premiere;
use App\Models\Seance;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PremiereController extends Controller
{
    public function index(SeanceFilterRequest $request)
    {
        $premieres = Movie::with('premiere')
            ->whereHas('premiere.seances', function ($query) use ($request) {
                return $query->where('start_date', '=', $request->validated('date') ?? now()->format('Y-m-d'));
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

    public function indexActual()
    {
        $premieres = Movie::with('premiere')
            ->whereHas('premiere', function (Builder $builder) {
                return $builder->actual();
            })->get();

        return MoviePremiereResource::collection($premieres);

    }

    public function show(SeanceFilterRequest $request, $movieId)
    {
        $movie = Movie::with('premiere')->findOrFail($movieId);

        $seances = Premiere::with('seances.hall')
            ->get();

        return $seances;
    }
}
