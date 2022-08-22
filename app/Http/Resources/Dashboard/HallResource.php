<?php

namespace App\Http\Resources\Dashboard;

use App\Models\Format;
use Illuminate\Http\Resources\Json\JsonResource;

class HallResource extends JsonResource
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
            'description' => $this->description,
            'is_vip' => $this->is_vip,
            'formats' => $this->whenLoaded('formats', FormatResource::collection($this->formats)),
            'cinema' => $this->whenLoaded('cinema'),
            'seances' => $this->whenLoaded('seances'),
            'seats' => $this->whenLoaded('seats')
        ];
    }
}
