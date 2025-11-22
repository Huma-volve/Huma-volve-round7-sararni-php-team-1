<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItineraryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'day_number' => $this->day_number,
            'title' => $this->title,
            'description' => $this->description,
            'location' => $this->location,
            'duration' => $this->duration,
            'images' => $this->getMedia('images')->map(function ($media) {
                return [
                    'id' => $media->id,
                    'url' => $media->getUrl(),
                    'thumb' => $media->getUrl('thumb'),
                    'preview' => $media->getUrl('preview'),
                ];
            }),
        ];
    }
}
