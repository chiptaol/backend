<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CinemaMovieResource extends JsonResource
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
            'rating' => $this->rating,
            'duration' => $this->duration,
            'poster_path' => $this->poster_path,
            'genres' => $this->genres,
            'halls' => SeanceHallResource::collection($this->halls())
        ];
    }

    public function halls()
    {
        return $this->premieres->first()->seances->reduce(function ($result, $item) {
            if (!array_key_exists($item->hall->id, $result)) {
                $result[$item->hall->id] = $item->hall;
                $result[$item->hall->id]->seances = collect([]);
            }

            $result[$item->hall->id]->seances->push($item);

            return $result;
        }, []);
    }

}
