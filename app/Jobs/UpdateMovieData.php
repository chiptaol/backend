<?php

namespace App\Jobs;

use App\Models\Movie;
use App\Services\Dashboard\TMDBService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateMovieData implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $movie;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Movie $movie)
    {
        $this->movie = $movie->withoutRelations();
    }

    public function uniqueId()
    {
        return $this->movie->id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(TMDBService $tmdb)
    {
        info('job is started.');

        $movieDetails = $tmdb->movieDetailsById($this->movie->tmdb_id);

        $data = [
            'title' => $movieDetails['title'],
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

        $this->movie->update($data);

        $posterPath = $tmdb->storeMovieFile(collect($movieDetails['images']['posters'])->sortByDesc('width')->first()['file_path'] ?? null, $movieDetails['original_title']);
        if (!empty($posterPath)) {
            $data['poster_path'] = $posterPath;
        }

        $backdropPath = $tmdb->storeMovieFile($movieDetails['backdrop_path'], $movieDetails['original_title']);
        if (!empty($backdropPath)) {
            $data['backdrop_path'] = $backdropPath;
        }

        $this->movie->update($data);

        info('job is processed');
    }
}
