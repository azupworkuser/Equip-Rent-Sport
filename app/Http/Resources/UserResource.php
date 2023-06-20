<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'name' => $this->name,
            'email' => $this->email,
            'profile_url' => $this->profile_url,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'domainRoles' => $this->domainRoles(),
            'tenants' => TenantResource::collection($this->tenants()),
        ];
    }
}
