<?php

namespace App\Http\Resources\Api\V1\Compare;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarCompareResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Get price (from price tiers or default)
        $pricePerDay = 0;
        $cheapestTier = $this->priceTiers->sortBy('price_per_day')->first();
        if ($cheapestTier) {
            $pricePerDay = (float) $cheapestTier->price_per_day;
        }

        // Get car details
        $brandName = $this->brand ? ($this->brand->name ?? 'Unknown') : 'Unknown';
        $carName = "{$brandName} {$this->model}";

        // Get location info
        $pickupLocation = $this->pickupLocation->name ?? 'Not specified';
        $dropoffLocation = $this->dropoffLocation->name ?? 'Same as pickup';

        // Format features
        $features = is_array($this->features) ? $this->features : [];
        $mainFeature = !empty($features) ? $features[0] : 'Car rental';

        // Availability (assuming available if exists)
        $isAvailable = true; // Cars don't have availability calendar in current structure
        $availabilityStatus = $isAvailable ? 'Available' : 'Sold Out';

        return [
            // Basic Info
            'id' => $this->id,
            'name' => $carName,
            'slug' => strtolower(str_replace(' ', '-', $carName)), // Generate slug from name
            'main_image' => $this->getMainImageUrl('thumb'),
            'main_image_full' => $this->getMainImageUrl(),
            
            // Time & Duration
            'time_start' => '00:00', // Cars are available 24/7
            'time_end' => '23:59',
            'duration_hours' => 24,
            
            // Pricing
            'price_per_person' => (float) $pricePerDay,
            'currency' => config('app.currency', 'USD'),
            'price_per_hour' => $cheapestTier ? (float) ($cheapestTier->price_per_hour ?? 0) : null,
            'price_per_day' => (float) $pricePerDay,
            
            // Description & Highlights
            'short_description' => $mainFeature,
            'highlight' => $mainFeature,
            'highlights' => array_slice($features, 0, 5),
            
            // Availability
            'availability' => $availabilityStatus,
            'available_slots' => 1,
            'is_available' => $isAvailable,
            
            // Guide & Transportation
            'guide_type' => 'Self-drive',
            'transportation' => 'Car',
            'transport_included' => true,
            
            // Car Specific
            'brand' => [
                'id' => $this->brand->id ?? null,
                'name' => $brandName,
            ],
            'model' => $this->model,
            'make' => $this->make ?? null,
            'seats' => $this->seats_count ?? 4,
            'doors' => $this->doors ?? null,
            'fuel_type' => $this->fuel_type ?? 'Unknown',
            'transmission' => $this->transmission ?? 'Unknown',
            'luggage_capacity' => $this->luggage_capacity ?? null,
            'air_conditioning' => $this->air_conditioning ?? false,
            'features' => $features,
            'pickup_location' => [
                'id' => $this->pickupLocation->id ?? null,
                'name' => $pickupLocation,
            ],
            'dropoff_location' => [
                'id' => $this->dropoffLocation->id ?? null,
                'name' => $dropoffLocation,
            ],
        ];
    }

    /**
     * Get main image URL
     */
    protected function getMainImageUrl(?string $conversion = null): string
    {
        // Check if model uses HasMedia trait
        if (method_exists($this->resource, 'getFirstMedia')) {
            $media = $this->getFirstMedia('car_images');
            if ($media) {
                if ($conversion) {
                    return $media->getUrl($conversion);
                }
                return $media->getUrl();
            }
        }

        return $this->getDefaultImageUrl($conversion);
    }

    /**
     * Get default placeholder image URL
     */
    protected function getDefaultImageUrl(?string $conversion = null): string
    {
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

        $bgColor = '10B981';
        $textColor = 'FFFFFF';
        $text = urlencode('Car Image');

        return "https://dummyimage.com/{$width}x{$height}/{$bgColor}/{$textColor}&text={$text}";
    }
}

