<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'username' => $this->userName,
            'email' => isset($this->email) ? $this->email : null,
            'uuid' => $this->uuid,
            // 'is_admin' => isset($this->is_admin) ? $this->is_admin == true : false,
            'profile' => isset( $this->profile->id) ? $this->profile->id : null
        ];
    }
}
