<?php

namespace App\Http\Controllers;

use App\Http\Requests\PremiereFilterRequest;
use App\Http\Resources\MovieResource;
use App\Http\Resources\PremiereResource;
use App\Models\Movie;
use App\Models\Premiere;
use App\Models\Seance;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PremiereController extends Controller
{
    public function index(PremiereFilterRequest $request)
    {
        $premieres = Movie::with('premiere')
            ->whereHas('premiere.seances', function ($query) use ($request) {
                return $query->where('start_date', '=', $request->validated('date') ?? now()->format('Y-m-d'));
            })->get()
            ->each(function ($item) {
                $item->is_premiere = $item->premiere->release_end_date >= now()->format('Y-m-d');

                return $item;
            });

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
        $premieres = Movie::query()
            ->whereHas('premiere', function (Builder $builder) {
                return $builder->actual();
            })->get()
            ->each(function ($item) {
                $item->is_premiere = $item->premiere->release_end_date >= now()->format('Y-m-d');

                return $item;
            });

        return MovieResource::collection($premieres);

    }
}
