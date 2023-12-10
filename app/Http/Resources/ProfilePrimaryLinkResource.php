<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfilePrimaryLinkResource extends JsonResource
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
            'name' => $this->name,
            'logo' => url($this->logo),
            'value' => $this->pivot->value,
            'available' => $this->when($request->bearerToken(), isset($this->pivot->available) ? $this->pivot->available : 0        ),
            'views' => $this->when($request->bearerToken(), isset($this->pivot->views) ? $this->pivot->views : 0        ),
            ];
    }
}
