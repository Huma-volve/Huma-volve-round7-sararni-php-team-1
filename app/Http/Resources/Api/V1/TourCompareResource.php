<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TourCompareResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Calculate duration in hours (assuming 8 hours per day as default)
        $durationHours = $this->duration_days * 8;

        // Get highlights (can be string or array)
        $highlights = $this->highlights;
        if (is_string($highlights)) {
            $highlights = explode("\n", $highlights);
            $highlights = array_filter(array_map('trim', $highlights));
        }
        $highlights = is_array($highlights) ? $highlights : [];

        // Get first highlight or default
        $mainHighlight = !empty($highlights) ? $highlights[0] : 'Tour highlights';

        // Check availability
        $isAvailable = false;
        $availableSlots = 0;
        
        if ($request->filled('date')) {
            // Check loaded availability relationship
            $availability = $this->whenLoaded('availability', function () use ($request) {
                return $this->availability->firstWhere('date', $request->date);
            });
            
            if (!$availability && $this->relationLoaded('availability')) {
                $availability = $this->availability->firstWhere('date', $request->date);
            }
            
            if ($availability) {
                $availableSlots = $availability->available_slots - $availability->booked_slots;
                $isAvailable = $availableSlots > 0;
            }
        } else {
            // Check if has any available dates from loaded relationship
            if ($this->relationLoaded('availability')) {
                $hasAvailability = $this->availability
                    ->where('is_active', true)
                    ->filter(function ($avail) {
                        return $avail->available_slots > $avail->booked_slots;
                    })
                    ->isNotEmpty();
                $isAvailable = $hasAvailability;
            } else {
                // Fallback: check if has any available dates
                $hasAvailability = $this->availability()
                    ->where('is_active', true)
                    ->whereRaw('available_slots > booked_slots')
                    ->exists();
                $isAvailable = $hasAvailability;
            }
        }

        // Get guide type from provider_info or default
        $guideType = 'Local guide';
        if ($this->provider_info && is_array($this->provider_info)) {
            if (isset($this->provider_info['guide_type'])) {
                $guideType = $this->provider_info['guide_type'];
            } elseif (isset($this->provider_info['guide'])) {
                $guideType = $this->provider_info['guide'];
            }
        }

        // Get transportation type
        $transportation = $this->transport_included ? 'Included' : 'Not included';
        if ($this->transport_included) {
            // Try to get more specific transportation info
            if ($this->provider_info && is_array($this->provider_info)) {
                if (isset($this->provider_info['transportation'])) {
                    $transportation = $this->provider_info['transportation'];
                } elseif (isset($this->provider_info['transport_type'])) {
                    $transportation = $this->provider_info['transport_type'];
                }
            }
        }

        // Format transportation based on transport_included
        if ($this->transport_included) {
            // Check if it's a specific type
            if (stripos($transportation, 'walking') !== false) {
                $transportation = 'Walking';
            } elseif (stripos($transportation, 'boat') !== false || stripos($transportation, 'cruise') !== false) {
                $transportation = 'Boat';
            } elseif (stripos($transportation, 'bus') !== false || stripos($transportation, 'coach') !== false) {
                $transportation = 'Bus';
            } elseif (stripos($transportation, 'car') !== false || stripos($transportation, 'vehicle') !== false) {
                $transportation = 'Car';
            } else {
                $transportation = 'Included';
            }
        } else {
            $transportation = 'Not included';
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'main_image' => $this->getMainImageUrl('thumb'),
            'price_per_person' => (float) $this->adult_price,
            'currency' => config('app.currency', 'USD'),
            'duration' => [
                'days' => $this->duration_days,
                'hours' => $durationHours,
                'formatted' => "{$durationHours} hours",
            ],
            'highlights' => [
                'main' => $mainHighlight,
                'all' => array_slice($highlights, 0, 3), // First 3 highlights
            ],
            'availability' => [
                'is_available' => $isAvailable,
                'status' => $isAvailable ? 'Available' : 'Not available',
                'available_slots' => $availableSlots,
            ],
            'guide' => [
                'type' => $guideType,
            ],
            'transportation' => [
                'included' => $this->transport_included,
                'type' => $transportation,
            ],
            'rating' => (float) $this->rating,
            'total_reviews' => $this->total_reviews,
        ];
    }

    /**
     * Get main image URL with fallback to default image
     */
    protected function getMainImageUrl(?string $conversion = null): string
    {
        if ($conversion === null) {
            $url = $this->getFirstMediaUrl('main_image');
        } else {
            $url = $this->getFirstMediaUrl('main_image', $conversion);
        }

        if (empty($url)) {
            return $this->getDefaultImageUrl($conversion);
        }

        return $url;
    }

    /**
     * Get default placeholder image URL
     */
    protected function getDefaultImageUrl(?string $conversion = null): string
    {
        $defaultImagePath = config('app.default_tour_image', 'images/default-tour.jpg');

        if (file_exists(public_path($defaultImagePath))) {
            $baseUrl = config('app.url', url('/'));
            return rtrim($baseUrl, '/').'/'.ltrim($defaultImagePath, '/');
        }

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

        $bgColor = '4F46E5';
        $textColor = 'FFFFFF';
        $text = urlencode('Tour Image');

        return "https://dummyimage.com/{$width}x{$height}/{$bgColor}/{$textColor}&text={$text}";
    }
}

