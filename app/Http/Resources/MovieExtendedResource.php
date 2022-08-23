<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MovieExtendedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'original_title' => $this->original_title,
            'description' => $this->description,
            'tagline' => $this->tagline,
            'duration' => $this->duration,
            'rating' => $this->rating,
            'age_rating' => $this->age_rating,
            'is_premiere' => $this->premiere->release_end_date >= now()->format('Y-m-d'),
            'poster_path' => $this->poster_path,
            'trailer_path' => $this->trailer_path,
            'backdrop_path' => $this->backdrop_path,
            'actors' => $this->actors,
            'directors' => $this->directors,
            'countries' => $this->countries,
            'genres' => $this->genres,
        ];
    }
}
