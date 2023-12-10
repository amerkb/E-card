<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LinkResource extends JsonResource
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
            'name_link' => $this->name_link,
            'link' => $this->link,
            'views' => $this->when($request->bearerToken(), isset($this->views) ? $this->views : 0        ),
            'logo' => isset($this->logo) ? url($this->logo) : '',
            'PrimaryLink' => PrimaryLinkResource::collection($this->whenLoaded('PrimaryLink')),
            'available' => $this->when($request->bearerToken(), isset($this->available) ? $this->available : 0        ),
        ];
    }
}
