<?php

namespace App\Http\Resources\Api\V1\Compare;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HotelCompareResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Hotels typically don't have price in the model, might need to add it
        // For now, using a placeholder
        $pricePerNight = 0; // This should come from hotel pricing if available

        // Get location
        $location = $this->location->name ?? 'Unknown location';

        // Get stars
        $stars = $this->stars ?? 0;
        $starsText = $stars > 0 ? "{$stars} stars" : 'Hotel';

        // Format description
        $description = $this->description ?? '';
        $mainHighlight = !empty($description) ? substr($description, 0, 50).'...' : 'Hotel accommodation';

        // Availability (assuming available if exists)
        $isAvailable = true;
        $availableRooms = $this->rooms_count ?? 0;
        
        // Determine availability status
        $availabilityStatus = 'Sold Out';
        if ($availableRooms > 5) {
            $availabilityStatus = 'Available';
        } elseif ($availableRooms > 0) {
            $availabilityStatus = 'Limited';
        }

        return [
            // Basic Info
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug ?? strtolower(str_replace(' ', '-', $this->name)),
            'main_image' => $this->getDefaultImageUrl('thumb'),
            'main_image_full' => $this->getDefaultImageUrl(),
            
            // Time & Duration
            'time_start' => '14:00', // Standard check-in time
            'time_end' => '12:00', // Standard check-out time
            'check_in_time' => '14:00',
            'check_out_time' => '12:00',
            'duration_hours' => 24,
            
            // Pricing
            'price_per_person' => (float) $pricePerNight,
            'currency' => config('app.currency', 'USD'),
            'price_per_night' => (float) $pricePerNight,
            
            // Description & Highlights
            'short_description' => $mainHighlight,
            'description' => $description,
            'highlight' => $mainHighlight,
            'highlights' => [
                $mainHighlight,
                $starsText,
                $location,
            ],
            
            // Availability
            'availability' => $availabilityStatus,
            'available_slots' => $availableRooms,
            'is_available' => $isAvailable,
            
            // Guide & Transportation
            'guide_type' => 'Hotel staff',
            'transportation' => 'Not included',
            'transport_included' => false,
            
            // Hotel Specific
            'stars' => $stars,
            'rooms_count' => $availableRooms,
            'location' => [
                'id' => $this->location->id ?? null,
                'name' => $location,
            ],
            'recommended' => $this->recommended ?? [],
        ];
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

        $bgColor = 'F59E0B';
        $textColor = 'FFFFFF';
        $text = urlencode('Hotel Image');

        return "https://dummyimage.com/{$width}x{$height}/{$bgColor}/{$textColor}&text={$text}";
    }
}

