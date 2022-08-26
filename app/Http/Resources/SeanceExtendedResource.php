<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SeanceExtendedResource extends JsonResource
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
            'movie_id' => $this->premiere->movie_id,
            'format' => $this->format->title,
            'prices' => $this->prices,
            'start_date_time' => $this->start_date_time,
            'seats_left' => $this->seats->where('pivot.is_available', true)->count(),
            'seats' => SeatResource::collection($this->seats)
        ];
    }
}
