<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SeatResource extends JsonResource
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
            'is_available' => $this->pivot->is_available,
            'is_vip' => $this->is_vip,
            'row' => $this->row,
            'place' => $this->place,
            'x' => $this->x,
            'y' => $this->y,
            'price' => $this->pivot->price
        ];
    }
}
