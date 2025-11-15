<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TourDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $request->user();
        $isFavorited = false;

        if ($user) {
            $isFavorited = $this->favorites()->where('user_id', $user->id)->exists();
        }

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'short_description' => $this->short_description,
            'description' => $this->description,
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
            'max_participants' => $this->max_participants,
            'min_participants' => $this->min_participants,
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
            'highlights' => $this->highlights ? (is_string($this->highlights) ? explode("\n", $this->highlights) : $this->highlights) : [],
            'included' => $this->included ?? [],
            'excluded' => $this->excluded ?? [],
            'cancellation_policy' => $this->cancellation_policy,
            'terms_conditions' => $this->terms_conditions,
            'provider_info' => $this->provider_info ?? [],
            'transport_included' => $this->transport_included,
            'pickup_zones' => $this->pickup_zones ?? [],
            'itinerary' => ItineraryResource::collection($this->whenLoaded('itineraries')),
            'activities' => ActivityResource::collection($this->whenLoaded('activities')),
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
            'similar_tours' => TourResource::collection($this->whenLoaded('similarTours')),
            'questions' => $this->when($this->relationLoaded('questions'), function () {
                return $this->questions->where('status', 'answered')->map(function ($question) {
                    return [
                        'id' => $question->id,
                        'question' => $question->question,
                        'answer' => $question->answer,
                        'user' => [
                            'id' => $question->user->id ?? null,
                            'name' => $question->user->name ?? null,
                        ],
                        'answered_by' => $question->answeredBy ? [
                            'id' => $question->answeredBy->id,
                            'name' => $question->answeredBy->name,
                        ] : null,
                        'answered_at' => $question->answered_at?->toIso8601String(),
                        'created_at' => $question->created_at?->toIso8601String(),
                    ];
                })->values();
            }),
            'is_favorited' => $isFavorited,
            'share_url' => $this->getShareUrl(),
            'availability' => $this->when($this->relationLoaded('availability'), function () {
                return $this->availability()
                    ->where('is_active', true)
                    ->whereRaw('available_slots > booked_slots')
                    ->orderBy('date')
                    ->limit(30)
                    ->get()
                    ->map(function ($availability) {
                        return [
                            'date' => $availability->date->format('Y-m-d'),
                            'available_slots' => $availability->getAvailableSlots(),
                            'price_override' => $availability->price_override ? (float) $availability->price_override : null,
                        ];
                    });
            }),
        ];
    }
}
