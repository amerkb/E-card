<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowLinksResource extends JsonResource
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
            'primary_links' => ProfilePrimaryLinkResource::collection($this->whenLoaded('primary')),
            'second_links' => LinkResource::collection($this->whenLoaded('links')),
        ];
    }
}
