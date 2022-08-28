<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CinemaExtendedResource extends JsonResource
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
            'reference_point' => $this->reference_point,
            'distance' => $this->whenNotNull($this->distance),
            'logo' => $this->whenLoaded('logo'),
        ];
    }
}
