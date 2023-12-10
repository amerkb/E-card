<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
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
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'jobTitle' => $this->jobTitle,
            'businessName' => $this->businessName,
            'location' => $this->location,
            'bio' => $this->bio,
            'theme' => url($this->theme->image),
            'cover' => url($this->cover),
            'photo' => url($this->photo),
            'bgColor' => $this->bgColor,
            'buttonColor' => $this->buttonColor,
            'phoneNum' => $this->phoneNum,
            'email' => $this->email,
            'views' => isset($this->views) ? $this->views : 0,
            'primary_links' => ProfilePrimaryLinkResource::collection($this->primary),
            'second_links' => LinkResource::collection($this->links),
            'section' => SectionResource::collection($this->sections),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
        ];
    }
}
