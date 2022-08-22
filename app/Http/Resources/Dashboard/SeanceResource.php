<?php

namespace App\Http\Resources\Dashboard;

use App\Http\Resources\MovieResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SeanceResource extends JsonResource
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
            'format' => new FormatResource($this->format),
            'start_date' => $this->start_date,
            'start_date_time' => $this->start_date_time,
            'prices' => $this->prices,
            'cinema' => $this->whenLoaded('cinema'),
            'hall' => new HallResource($this->whenLoaded('hall')),
            'movie' => new MovieResource($this->premiere->movie),
            'seats' => $this->whenLoaded('seats')
        ];
    }
}
