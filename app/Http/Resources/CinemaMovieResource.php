<?php

namespace App\Http\Resources;

use App\Models\Hall;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use stdClass;

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
                $hall = (object) $item->hall->getAttributes();
                $result[$item->hall->id] = $hall;
                $result[$item->hall->id]->seances = [];
            }

            $result[$item->hall->id]->seances[] = $item;


            return $result;
        }, []);
    }

}
