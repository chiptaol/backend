<?php

namespace App\Services\Dashboard;

use App\Exceptions\BusinessException;
use App\Jobs\UpdateMovieData;
use App\Models\Cinema;
use App\Models\Hall;
use App\Models\Movie;
use App\Models\Premiere;
use App\Models\Seance;
use App\Models\Seat;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use function Psy\debug;

final class SeanceService
{
    public function store(array $validatedData, Cinema $cinema, TMDBService $tmdb)
    {

        DB::transaction(function () use ($validatedData, $tmdb, $cinema) {
            $movie = Movie::where('tmdb_id', '=', $validatedData['movie_id'])->first();

            if (is_null($movie)) {
                $movie = Movie::create([
                    'tmdb_id' => $validatedData['movie_id']
                ]);

                UpdateMovieData::dispatch($movie);
            }

            foreach ($validatedData['seances'] as $seance) {
                $hall = $cinema->halls()->findOrFail($seance['hall_id']);

                $currentSeanceStart = $seance['start_date_time'];
                $currentSeanceEnd = Carbon::createFromFormat('Y-m-d H:i', $currentSeanceStart)->addMinutes($movie->duration)->addMinutes(30);

                $existsSeance = Seance::query()
                    ->where(function (Builder $builder) use ($currentSeanceStart, $hall) {
                        return $builder->where('start_date_time', '<=', $currentSeanceStart)
                            ->where('end_date_time', '>=', $currentSeanceStart)
                            ->where('hall_id', '=', $hall->id);
                    })->orWhere(function (Builder $builder) use ($currentSeanceEnd, $hall) {
                        return $builder->where('start_date_time', '<=', $currentSeanceEnd)
                            ->where('end_date_time', '>=', $currentSeanceEnd)
                            ->where('hall_id', '=', $hall->id);
                    })->first();

                if (!empty($existsSeance)) {
                    throw  new BusinessException(trans('This time range is occupied by another seance of this hall.'), 422);
                }
                $releaseEndDate = Carbon::createFromFormat('Y-m-d H:i', $currentSeanceStart)->addDays(7);
                $premiere = $cinema->premieres()->firstOrCreate([
                    'movie_id' => $movie->id,
                ], [
                    'release_date' => $currentSeanceStart,
                    'release_end_date' => $releaseEndDate
                ]);

                if ($seance['start_date_time'] < $premiere->release_date) {
                    $premiere->update([
                        'release_date' => $currentSeanceStart,
                        'release_end_date' => $releaseEndDate
                    ]);
                }

                if ($hall->formats->doesntContain('id', '=', $seance['movie_format_id'])) {
                    throw new BusinessException(trans('This hall not support provided movie format.'), 422);
                }

                $newSeance = $hall->seances()->create([
                    'start_date' => $currentSeanceStart,
                    'start_date_time' => $currentSeanceStart,
                    'end_date_time' => $currentSeanceEnd,
                    'premiere_id' => $premiere->id,
                    'format_id' => $seance['movie_format_id'],
                    'cinema_id' => $cinema->id
                ]);

                if (empty($seance['standard_seat_price']) && empty($seance['vip_seat_price'])) {
                    throw new BusinessException(trans('The vip_seat_price field is required when the standard_seat_price is null.'), 422);
                }

                $seatPrices = $hall->seats->reduce(function ($result, $seat) use ($newSeance, $seance) {
                    $price = $seat->is_vip ? ($seance['vip_seat_price'] ?? null) : ($seance['standard_seat_price'] ?? null);

                    if (is_null($price)) {
                        throw new BusinessException(trans('Something went wrong when checking the type of seat and choosing the price, make sure you send valid data.'), 422);
                    }

                    $result[$seat->id] = compact('price');

                    return $result;
                }, []);

                $prices = [
                    'standard' => $seance['standard_seat_price'] ?? null,
                    'vip' => $seance['vip_seat_price'] ?? null
                ];

                $newSeance->prices = $prices;
                $newSeance->save();

                $newSeance->seats()->attach($seatPrices);

            }

        });

        return true;

    }
}
