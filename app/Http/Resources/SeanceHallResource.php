<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SeanceHallResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $additionalData = $this->additionalData();

        return [
            'id' => $this->id,
            'title' => $this->title,
            'is_vip' => $this->is_vip,
            'formats' => $additionalData['formats'] ?? null,
            'cheapest_price' => $additionalData['cheapest_price'] ?? null,
            'seances' => SeanceResource::collection($this->seances)
        ];
    }

    public function additionalData()
    {
        return $this->seances->reduce(function ($result, $item) {
            $result['formats'][] = $item->format->title;
            $result['prices'][] = min(array_filter($item->prices));

            return [
                'formats' => array_unique($result['formats']),
                'cheapest_price' => min($result['prices'])
            ];
        }, []);
    }
}
