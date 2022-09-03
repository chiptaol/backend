<?php

namespace App\Services\Dashboard;

use App\Exceptions\BusinessException;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\File;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\Response;

final class TMDBService
{
    private const BASE_PATH = 'https://api.themoviedb.org/3/';
    private const BASE_IMAGE_PATH = 'https://image.tmdb.org/t/p/original';

    private const US_AGE_CERTIFICATIONS = [
        'G' => '0+',
        'PG' => '6+',
        'PG-13' => '12+',
        'R' => '16+',
        'X' => '18+'
    ];

    /**
     * @param string $title
     * @param int $limit
     * @return Collection
     * @throws BusinessException
     */
    public function searchMovieByTitle(string $title, int $limit = 5)
    {
        $response = $this->sendRequest('search/movie', [
            'query' => $title
        ]);
        $results = collect($response['results'])
            ->sortByDesc('release_date')
            ->slice(0, 5)
            ->values();

        return $results;
     }

     public function movieDetailsById(int $id)
     {
         return $this->sendRequest('movie/' . $id, [
             'append_to_response' => 'credits,images,release_dates,videos'
         ]);

     }

     public function movieAgeCertificateById(int $id)
     {
         return $this->sendRequest('movie/' . $id . '/release_dates');
     }

     public function storeMovieFile($fileName, $movieName, $type = 'poster')
     {
         if (empty($fileName)) {
             return null;
         }

         $fileContents = file_get_contents(self::BASE_IMAGE_PATH . $fileName);
         $filePath = 'movies/' . Str::slug($movieName) . $fileName;

         if ($type === 'backdrop') {
             info('backdrop');
             $image = Image::make($fileContents)->fit(967, 384);
             $image->save(public_path('storage/' . $filePath));
         } else {
             info('not backdrop');
             Storage::disk('public')->put($filePath, $fileContents);
         }

         return 'storage/' . $filePath;
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
            info($path);
            throw new BusinessException(trans('TMDB return ERROR response.'), 400);
        }

        return $response->json();
    }

    private function getFullUrl(string $path)
    {
        return self::BASE_PATH . $path;
    }

    public function getAgeRating(array $releaseDates)
    {
        $certifications = collect($releaseDates['results']);

        $ruCertification = array_slice(array_filter($certifications->where('iso_3166_1', '=', 'RU')->pluck('release_dates.*.certification')->first() ?? [], fn($value) => !empty($value)), 0, 1)[0] ?? null;

        if (empty($ruCertification)) {
            $usCertifications = $certifications->where('iso_3166_1', '=', 'US')->pluck('release_dates.*.certification')->first();

            if (empty($usCertifications[0] ?? null)) {
                return null;
            }

            return self::US_AGE_CERTIFICATIONS[array_slice(array_filter($usCertifications, fn($value) => !empty($value)), 0,  1)[0] ?? null];
        }

        return $ruCertification;
    }


}
