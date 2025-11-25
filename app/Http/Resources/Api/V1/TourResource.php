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
            'main_image' => $this->getMainImageUrl(),
            'main_image_thumb' => $this->getMainImageUrl('thumb'),
            'main_image_preview' => $this->getMainImageUrl('preview'),
            'gallery' => $this->getGalleryImages(),
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
            'is_favorited' => $this->isFavorited($request),
            'difficulty' => $this->difficulty,
            'languages' => $this->languages ?? [],
            'tags' => $this->tags ?? [],
        ];
    }

    /**
     * Get main image URL with fallback to default image
     */
    protected function getMainImageUrl(?string $conversion = null): string
    {
        // getFirstMediaUrl doesn't accept null, so we need to handle it differently
        if ($conversion === null) {
            $url = $this->getFirstMediaUrl('main_image');
        } else {
            $url = $this->getFirstMediaUrl('main_image', $conversion);
        }

        // If no image exists, return default placeholder
        if (empty($url)) {
            return $this->getDefaultImageUrl($conversion);
        }

        return $url;
    }

    /**
     * Get gallery images with fallback
     */
    protected function getGalleryImages(): array
    {
        $gallery = $this->getMedia('gallery');

        // If no gallery images, return array with one default image
        if ($gallery->isEmpty()) {
            return [
                [
                    'id' => null,
                    'url' => $this->getDefaultImageUrl(),
                    'thumb' => $this->getDefaultImageUrl('thumb'),
                    'preview' => $this->getDefaultImageUrl('preview'),
                ],
            ];
        }

        return $gallery->map(function ($media) {
            return [
                'id' => $media->id,
                'url' => $media->getUrl(),
                'thumb' => $media->getUrl('thumb'),
                'preview' => $media->getUrl('preview'),
            ];
        })->toArray();
    }

    /**
     * Get default placeholder image URL
     */
    protected function getDefaultImageUrl(?string $conversion = null): string
    {
        // Check if custom default image is configured
        $defaultImagePath = config('app.default_tour_image', 'images/default-tour.jpg');

        // If file exists in public directory, use it
        if (file_exists(public_path($defaultImagePath))) {
            $baseUrl = config('app.url', url('/'));
            $url = rtrim($baseUrl, '/').'/'.ltrim($defaultImagePath, '/');

            // For conversions, you might want to use a service or return the same URL
            // For now, return the same URL for all conversions
            return $url;
        }

        // Fallback to placeholder service
        $width = match ($conversion) {
            'thumb' => 300,
            'preview' => 800,
            default => 1200,
        };

        $height = match ($conversion) {
            'thumb' => 300,
            'preview' => 600,
            default => 800,
        };

        // Using dummyimage.com service (more reliable and works well)
        // Format: https://dummyimage.com/{width}x{height}/{bg-color}/{text-color}&text={text}
        $bgColor = '4F46E5'; // Indigo
        $textColor = 'FFFFFF'; // White
        $text = urlencode('Tour Image');

        return "https://dummyimage.com/{$width}x{$height}/{$bgColor}/{$textColor}&text={$text}";
    }

    /**
     * Check if the tour is favorited by the authenticated user
     */
    protected function isFavorited(Request $request): bool
    {
        $user = $request->user();

        // If user is not authenticated, return false
        if (! $user) {
            return false;
        }

        // Check if is_user_favorited count is already loaded (from withCount)
        if (isset($this->resource->is_user_favorited)) {
            return (bool) $this->resource->is_user_favorited;
        }

        // Fallback: check if tour is in user's favorites (single query)
        return $this->resource->favorites()
            ->where('user_id', $user->id)
            ->exists();
    }
}
