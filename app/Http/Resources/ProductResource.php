<?php

namespace App\Http\Resources;

use App\Http\Resources\ProductLocationResource;
use App\Http\Resources\ProductMediaResource;
use App\Http\Resources\ProductTypeResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'created_at' => $this->created_at,
            'id' => $this->getKey(),
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'advertised_price' => $this->advertised_price,
            'terms_and_conditions' => $this->terms_and_conditions,
            'visibility' => $this->visibility,
            'status' => $this->status->name(),
            'location' => $this->whenLoaded('location', fn() => ProductLocationResource::make($this->location)),
            'media' => $this->whenLoaded('media', fn() => ProductMediaResource::make($this->media)),
            'type' => $this->whenLoaded('type', fn() => ProductTypeResource::make($this->make))
        ];
    }
}
