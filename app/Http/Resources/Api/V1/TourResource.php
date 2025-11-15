<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TourResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'short_description' => $this->short_description,
            'main_image' => $this->getFirstMediaUrl('main_image'),
            'main_image_thumb' => $this->getFirstMediaUrl('main_image', 'thumb'),
            'main_image_preview' => $this->getFirstMediaUrl('main_image', 'preview'),
            'gallery' => $this->getMedia('gallery')->map(function ($media) {
                return [
                    'id' => $media->id,
                    'url' => $media->getUrl(),
                    'thumb' => $media->getUrl('thumb'),
                    'preview' => $media->getUrl('preview'),
                ];
            }),
            'category' => [
                'id' => $this->category->id ?? null,
                'name' => $this->category->title ?? null,
            ],
            'duration_days' => $this->duration_days,
            'duration_nights' => $this->duration_nights,
            'location' => [
                'lat' => $this->location_lat,
                'lng' => $this->location_lng,
                'meeting_point' => $this->meeting_point,
            ],
            'pricing' => [
                'adult_price' => (float) $this->adult_price,
                'child_price' => $this->child_price ? (float) $this->child_price : null,
                'infant_price' => $this->infant_price ? (float) $this->infant_price : null,
                'currency' => config('app.currency', 'USD'),
            ],
            'rating' => (float) $this->rating,
            'total_reviews' => $this->total_reviews,
            'is_featured' => $this->is_featured,
            'difficulty' => $this->difficulty,
            'languages' => $this->languages ?? [],
            'tags' => $this->tags ?? [],
        ];
    }
}
