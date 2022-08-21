<?php

namespace App\Services\Dashboard;

use App\Exceptions\BusinessException;
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
                $movieDetails = $tmdb->movieDetailsById($validatedData['movie_id']);

                $data = [
                    'title' => $movieDetails['title'],
                    'tmdb_id' => $movieDetails['id'],
                    'original_title' => $movieDetails['original_title'],
                    'genres' => array_map(fn($genre) => mb_convert_case($genre, MB_CASE_TITLE), collect($movieDetails['genres'])->pluck('name')->toArray()),
                    'description' => $movieDetails['overview'],
                    'duration' => $movieDetails['runtime'],
                    'tagline' => $movieDetails['tagline'],
                    'release_date' => $movieDetails['release_date'],
                    'actors' => collect($movieDetails['credits']['cast'])->pluck('original_name')->slice(0, 5)->toArray(),
                    'directors' => collect($movieDetails['credits']['crew'])->where('job', '=', 'Director')->pluck('original_name')->toArray(),
                    'countries' => $movieDetails['production_countries'],
                    'age_rating' => $tmdb->getAgeRating($movieDetails['release_dates']),
                    'rating' => $movieDetails['vote_average']
                ];

                    $trailerKey = collect($movieDetails['videos']['results'])->where('site', '=', 'YouTube')->first()['key'] ?? null;
                if (!empty($trailerKey)) {
                    $data['trailer_path'] = 'https://www.youtube.com/watch?v=' . $trailerKey;
                }


                $posterPath = $tmdb->storeMovieFile(collect($movieDetails['images']['posters'])->sortByDesc('width')->first()['file_path'] ?? null, $movieDetails['original_title']);

                if (!empty($posterPath)) {
                    $data['poster_path'] = $posterPath;
                }

                $backdropPath = $tmdb->storeMovieFile($movieDetails['backdrop_path'], $movieDetails['original_title']);

                if (!empty($backdropPath)) {
                    $data['backdrop_path'] = $backdropPath;
                }

                $movie = Movie::create($data);
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

                $newSeance = $hall->seances()->create([
                    'start_date' => $currentSeanceStart,
                    'start_date_time' => $currentSeanceStart,
                    'end_date_time' => $currentSeanceEnd,
                    'premiere_id' => $premiere->id,
                    'format' => $seance['movie_format'],
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

                    $result[$newSeance->id] = compact('price');

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
