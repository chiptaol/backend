<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MovieResource extends JsonResource
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
            'is_premiere' => $this->premieres->first()->release_end_date >= now()->format('Y-m-d'),
            'poster_path' => $this->poster_path,
            'genres' => $this->genres
        ];
    }
}
