<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->activity_type,
            'image' => $this->getFirstMediaUrl('image'),
            'image_thumb' => $this->getFirstMediaUrl('image', 'thumb'),
            'image_preview' => $this->getFirstMediaUrl('image', 'preview'),
        ];
    }
}
