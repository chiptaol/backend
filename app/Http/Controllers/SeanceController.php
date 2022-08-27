<?php

namespace App\Http\Controllers;

use App\Http\Requests\SeanceBookFormRequest;
use App\Http\Resources\SeanceExtendedResource;
use App\Http\Resources\TicketResource;
use App\Models\Seance;
use App\Models\SeanceSeat;
use App\Services\SeanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SeanceController extends Controller
{
    public function __construct(private SeanceService $service)
    {
    }

    /**
     *
     *
     * @OA\Get (
     *     path="/api/seances/{seance-id}",
     *     summary="Get a specific seance data",
     *     tags={"Seances"},
     *
     *
     *     @OA\Parameter (ref="#/components/parameters/seance-id-path"),
     *
     *     @OA\Response (
     *          response=200,
     *          description="Success (OK) [Response](https://api.chiptaol.uz/api/example-responses/seances-specific)"
     *     )
     * )
     *
     * @param $seanceId
     * @return JsonResponse
     */
    public function show($seanceId)
    {
        $seance = Seance::with(['hall:id,title', 'cinema' => function ($query) {
            return $query->without('logo')
                ->select('id', 'title');
        }, 'premiere' => function ($query) {
            return $query->with('movie:id,title')
                ->select('id', 'movie_id');
        }, 'seats' => fn($q) => $q->orderBy('row')->orderBy('place'), 'premiere:id,movie_id'])
            ->upcoming()
            ->findOrFail($seanceId);

        $schedule = Seance::without('format')
            ->whereRelation('premiere', 'movie_id', '=', $seance->premiere->movie->id)
            ->where('cinema_id', '=', $seance->cinema->id)
            ->where('start_date', '=', $seance->start_date)
            ->upcoming()
            ->select('id', 'start_date_time', 'prices')
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

    /**
     *
     * @OA\Post (
     *     path="/api/seances/{seance-id}/book",
     *     summary="Booking multiple seats for specific seance",
     *     tags={"Seances"},
     *
     *     @OA\Parameter (ref="#/components/parameters/seance-id-path"),
     *
     *     @OA\RequestBody (
     *          required=true,
     *          @OA\JsonContent (ref="#/components/schemas/SeanceBookFormRequest")
     *     ),
     *
     *     @OA\Response (
     *          response=200,
     *          description="Success (OK) [Response](https://api.chiptaol.uz/api/example-responses/seances-book)"
     *     )
     * )
     *
     *
     * @param SeanceBookFormRequest $request
     * @param $seanceId
     * @return TicketResource|JsonResponse
     */
    public function book(SeanceBookFormRequest $request, $seanceId)
    {
        /** @var Seance $seance */
        $seance = Seance::upcoming()
            ->findOrFail($seanceId);

        $ticket = $this->service->book($request->validated(), $seance);

        return $ticket
            ? new JsonResponse(new TicketResource($ticket))
            : new JsonResponse([
                'message' => trans('Something went wrong when creating ticket.')
            ]);
    }
}
