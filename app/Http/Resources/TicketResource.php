<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
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
            'cinema_title' => $this->cinema_title,
            'hall_title' => $this->hall_title,
            'movie' => [
                'title' => $this->movie->title,
                'original_title' => $this->movie->original_title,
                'poster_path' => $this->movie->poster_path,
                'genres' => $this->movie->genres
            ],
            'start_date_time' => $this->seance->start_date_time,
            'seats' => TicketSeatResource::collection($this->seats)
        ];
    }
}
