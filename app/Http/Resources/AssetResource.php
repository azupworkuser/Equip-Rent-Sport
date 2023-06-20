<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AssetResource extends JsonResource
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
            'created_at' => $this->created_at,
            'name' => $this->name,
            'description' => $this->description,
            'quantity' => $this->quantity,
            'capacity_per_quantity' => $this->capacity_per_quantity,
            'total_capacity' => $this->total_capacity,
            'shared_between_products' => $this->shared_between_products,
            'shared_between_bookings' => $this->shared_between_bookings,
            'status' => $this->status->name(),
            'categories' => CategoryResource::collection($this->categories),
        ];
    }
}
