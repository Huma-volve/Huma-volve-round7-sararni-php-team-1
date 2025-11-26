<?php

namespace App\Http\Resources\Api\V1\Compare;

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
        $availabilityStatus = 'Sold Out';
        $timeStart = '09:00'; // Default start time
        $timeEnd = '17:00'; // Default end time

        if ($request->filled('date')) {
            // Check loaded availability relationship
            $availability = null;
            if ($this->relationLoaded('availability')) {
                $availability = $this->availability->firstWhere('date', $request->date);
            }

            if ($availability) {
                $availableSlots = $availability->available_slots - $availability->booked_slots;
                $isAvailable = $availableSlots > 0;
                
                // Determine availability status
                if ($availableSlots <= 0) {
                    $availabilityStatus = 'Sold Out';
                } elseif ($availableSlots <= 5) {
                    $availabilityStatus = 'Limited';
                } else {
                    $availabilityStatus = 'Available';
                }
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
                $availabilityStatus = $isAvailable ? 'Available' : 'Sold Out';
            } else {
                // Fallback: check if has any available dates
                $hasAvailability = $this->availability()
                    ->where('is_active', true)
                    ->whereRaw('available_slots > booked_slots')
                    ->exists();
                $isAvailable = $hasAvailability;
                $availabilityStatus = $isAvailable ? 'Available' : 'Sold Out';
            }
        }

        // Get time_start and time_end from first itinerary or availability
        // Try to extract from itinerary duration or use defaults
        if ($this->relationLoaded('itineraries') && $this->itineraries->isNotEmpty()) {
            $firstItinerary = $this->itineraries->first();
            // Parse duration if available (e.g., "9:00 AM - 5:00 PM" or "8 hours")
            $duration = $firstItinerary->duration ?? null;
            if ($duration) {
                // Try to parse time range from duration string
                if (preg_match('/(\d{1,2}):(\d{2})\s*(AM|PM)?/i', $duration, $matches)) {
                    $timeStart = $matches[0];
                }
            }
        }
        
        // Calculate end time based on duration if not set
        if ($timeEnd === '17:00' && $timeStart) {
            $timeEnd = date('H:i', strtotime($timeStart.' +'.$durationHours.' hours'));
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
            // Basic Info
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'main_image' => $this->getMainImageUrl('thumb'),
            'main_image_full' => $this->getMainImageUrl(),
            
            // Time & Duration
            'time_start' => $timeStart,
            'time_end' => $timeEnd,
            'duration_hours' => $durationHours,
            'duration_days' => $this->duration_days,
            
            // Pricing
            'price_per_person' => (float) $this->adult_price,
            'currency' => config('app.currency', 'USD'),
            'child_price' => $this->child_price ? (float) $this->child_price : null,
            'infant_price' => $this->infant_price ? (float) $this->infant_price : null,
            
            // Description & Highlights
            'short_description' => $this->short_description ?? '',
            'highlight' => $mainHighlight,
            'highlights' => array_slice($highlights, 0, 5), // First 5 highlights
            
            // Availability
            'availability' => $availabilityStatus,
            'available_slots' => $availableSlots,
            'is_available' => $isAvailable,
            
            // Guide & Transportation
            'guide_type' => $guideType,
            'transportation' => $transportation,
            'transport_included' => $this->transport_included,
            
            // Location
            'location' => [
                'lat' => $this->location_lat,
                'lng' => $this->location_lng,
                'meeting_point' => $this->meeting_point,
            ],
            
            // Category
            'category' => [
                'id' => $this->category->id ?? null,
                'name' => $this->category->title ?? null,
            ],
            
            // Rating & Reviews
            'rating' => (float) $this->rating,
            'total_reviews' => $this->total_reviews,
            
            // Additional Info
            'difficulty' => $this->difficulty,
            'languages' => $this->languages ?? [],
            'tags' => $this->tags ?? [],
            'is_featured' => $this->is_featured,
            'max_participants' => $this->max_participants,
            'min_participants' => $this->min_participants,
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

