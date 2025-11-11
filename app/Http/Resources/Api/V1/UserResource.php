<?php

namespace App\Http\Resources\Api\V1;

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
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'location' => $this->location,
            'is_verified' => $this->is_verified,
            'email_verified_at' => $this->email_verified_at?->toIso8601String(),
            'profile_photo_url' => $this->profile_photo_url,
            'photo' => $this->getFirstMediaUrl('photos'),
            'photo_thumb' => $this->getFirstMediaUrl('photos', 'thumb'),
            'photo_preview' => $this->getFirstMediaUrl('photos', 'preview'),
            'documents' => $this->getMedia('documents')->map(function ($media) {
                return [
                    'id' => $media->id,
                    'name' => $media->name,
                    'url' => $media->getUrl(),
                    'mime_type' => $media->mime_type,
                    'size' => $media->size,
                ];
            }),
            'roles' => $this->roles->pluck('name'),
            'social_providers' => $this->socialIdentities->pluck('provider'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
