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

class DownloadMovieImages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $movie;
    public $movieDetails;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Movie $movie, array $movieDetails)
    {
        $this->movie = $movie->withoutRelations();
        $this->movieDetails = $movieDetails;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(TMDBService $tmdb)
    {
        sleep(10);
        info('job is processed.');

        $data = [];
        $posterPath = $tmdb->storeMovieFile(collect($this->movieDetails['images']['posters'])->sortByDesc('width')->first()['file_path'] ?? null, $this->movieDetails['original_title']);
        if (!empty($posterPath)) {
            $data['poster_path'] = $posterPath;
        }

        $backdropPath = $tmdb->storeMovieFile($this->movieDetails['backdrop_path'], $this->movieDetails['original_title']);
        if (!empty($backdropPath)) {
            $data['backdrop_path'] = $backdropPath;
        }

        $this->movie->update($data);
    }
}
