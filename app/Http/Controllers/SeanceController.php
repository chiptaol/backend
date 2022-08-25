<?php

namespace App\Http\Controllers;

use App\Http\Resources\SeanceExtendedResource;
use App\Models\Seance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SeanceController extends Controller
{
    public function show(Request $request, $seanceId)
    {
        $seance = Seance::with(['hall:id,title', 'cinema' => function ($query) {
            return $query->without('logo')
                ->select('id', 'title');
        }, 'premiere' => function ($query) {
            return $query->with('movie:id,title')
                ->select('id', 'movie_id');
        }, 'seats' => fn($q) => $q->orderBy('row')->orderBy('place')])
            ->upcoming()
            ->findOrFail($seanceId);

        $schedule = Seance::without('format')
            ->whereRelation('premiere', 'movie_id', '=', $seance->premiere->movie->id)
            ->where('cinema_id', '=', $seance->cinema->id)
            ->where('start_date', '=', $seance->start_date)
            ->upcoming()
            ->select('id', 'start_date_time')
            ->orderBy('start_date_time')
            ->get();

        return new JsonResponse([
            'schedule' => $schedule,
            'data' => [
                'movie_title' => $seance->premiere->movie->title,
                'cinema_title' => $seance->cinema->title,
                'hall_title' => $seance->hall->title,
                'seance' => new SeanceExtendedResource($seance)
            ]
        ]);
    }
}
