<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductOptionAvailabilityEventResource extends JsonResource
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
            'id' => $this->getKey(),
            'name' => $this->name,
            'local_start_date' => $this->local_start_date,
            'local_end_date' => $this->local_end_date,
            'status' => $this->status->name(),
            'option' => new ProductOptionResource($this->productOption),
            'pricings' => $this->pricings,
            'sessions' => AvailabilitySessionResource::collection($this->sessions),
        ];
    }
}
