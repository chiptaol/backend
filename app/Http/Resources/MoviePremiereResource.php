<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class MoviePremiereResource extends JsonResource
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
            'release_date' => $this->releaseDate(),
            'backdrop_path' => $this->backdrop_path,
        ];
    }

    protected function releaseDate()
    {
        return $this->premieres->first()->release_date;
    }
}
