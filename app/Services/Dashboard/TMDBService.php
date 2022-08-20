<?php

namespace App\Services\Dashboard;

use App\Exceptions\BusinessException;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

final class TMDBService
{
    private const BASE_PATH = 'https://api.themoviedb.org/3/';

    /**
     * @param string $title
     * @param int $limit
     * @return array
     * @throws BusinessException
     */
    public function searchByTitle(string $title, int $limit = 5)
    {
        $response = $this->sendRequest('search/movie', [
            'query' => $title
        ]);
        $results = collect($response['results'])
            ->sortByDesc('vote_average')
            ->slice(0, 5)
            ->values();

        return $results;
     }

    /**
     * @param string $path
     * @param array $data
     * @return array|mixed
     * @throws BusinessException
     */
    private function sendRequest(string $path, array $data = [])
    {
        $response = Http::get($this->getFullUrl($path), array_merge([
            'api_key' => config('tmdb.api_key'),
            'language' => 'ru'
        ], $data));

        if ($response->status() !== Response::HTTP_OK) {
            throw new BusinessException(trans('TMDB return ERROR response.'), 400);
        }

        return $response->json();
    }

    private function getFullUrl(string $path)
    {
        return self::BASE_PATH . $path;
    }
}
