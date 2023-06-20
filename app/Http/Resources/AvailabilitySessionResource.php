<?php

namespace App\Http\Resources;

use App\Models\ProductOption;
use Illuminate\Http\Resources\Json\JsonResource;

class AvailabilitySessionResource extends JsonResource
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
            'local_start_date' => $this->local_start_date,
            'local_end_date' => $this->local_end_date,
            'local_start_time' => $this->local_start_time,
            'local_end_time' => $this->local_end_time,
            'duration_unit' => $this->duration_unit,
            'duration_type' => $this->duration_type,
            'status' => $this->status->name(),
            'option' => $this->whenLoaded('productOption', function () {
                return new ProductOptionResource($this->productOption);
            }),
        ];
    }
}
