<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;

class UserInvitationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'created_by' => $this->created_by,
            'accepted_at' => $this->accepted_at,
            'email' => $this->email,
            'status' => $this->status,
            'token' => $this->token,
            'domainRole' => collect($this->domainRole),
            'data' => $this->data,
        ];
    }
}
