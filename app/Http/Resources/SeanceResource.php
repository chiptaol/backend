<?php

namespace App\Http\Resources;

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
            'format' => $this->format->title,
            'start_date_time' => $this->start_date_time,
            'cheapest_price' => min($this->prices),
            'hall_title' => $this->hall->title,
            'hall_is_vip' => $this->hall->is_vip,

        ];
    }
}
