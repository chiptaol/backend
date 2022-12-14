<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class CinemaResource extends JsonResource
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
            'address' => $this->address,
            'logo' => $this->logo,
            'reference_point' => $this->reference_point,
            'longitude' => floatval($this->longitude),
            'latitude' => floatval($this->latitude),
            'phone' => $this->phone
        ];
    }
}
