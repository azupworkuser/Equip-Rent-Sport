<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ApiKeyResource extends JsonResource
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
            'permissions' => collect($this->permissions)->pluck('name')->toArray(),
            'name' => $this->name,
            'key' => $this->key,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
