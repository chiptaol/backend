<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PremiereResource extends JsonResource
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
            'release_date' => $this->release_date,
            'is_actual' => $this->is_actual,
            'movie' => [
                'id' => $this->movie->id,
                'title' => $this->movie->title,
                'poster_path' => $this->movie->poster_path,
                'backdrop_path' => $this->movie->backdrop_path,
                'genres' => $this->movie->genres,
            ]
        ];
    }
}
