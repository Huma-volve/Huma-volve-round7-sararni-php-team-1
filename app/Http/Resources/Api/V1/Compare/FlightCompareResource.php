<?php

namespace App\Http\Resources\Api\V1\Compare;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FlightCompareResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Get cheapest class price
        $cheapestPrice = $this->flightClasses->min('price_per_seat') ?? 0;
        $cheapestClass = $this->flightClasses->where('price_per_seat', $cheapestPrice)->first();

        // Get duration from first leg
        $firstLeg = $this->flightLegs->first();
        $durationMinutes = $firstLeg->duration_minutes ?? 0;
        $durationHours = round($durationMinutes / 60, 1);

        // Get origin and destination
        $origin = $firstLeg->originAirport->city ?? 'Unknown';
        $destination = $firstLeg->destinationAirport->city ?? 'Unknown';
        $route = "{$origin} → {$destination}";

        // Check availability (seats available)
        $totalAvailableSeats = $this->flightClasses->sum('seats_available');
        $isAvailable = $totalAvailableSeats > 0;
        
        // Determine availability status
        $availabilityStatus = 'Sold Out';
        if ($totalAvailableSeats > 10) {
            $availabilityStatus = 'Available';
        } elseif ($totalAvailableSeats > 0) {
            $availabilityStatus = 'Limited';
        }
        
        // Get time_start and time_end from flight leg
        $timeStart = $firstLeg->departure_time ? $firstLeg->departure_time->format('H:i') : null;
        $timeEnd = $firstLeg->arrival_time ? $firstLeg->arrival_time->format('H:i') : null;

        // Get carrier info
        $carrierName = $this->carrier->carrier_name ?? 'Unknown';
        $aircraftModel = $this->aircraft->model ?? 'Unknown';

        // Get available classes
        $availableClasses = $this->flightClasses->map(function ($flightClass) {
            return [
                'name' => $flightClass->class->class_name ?? 'Unknown',
                'price' => (float) $flightClass->price_per_seat,
                'seats_available' => $flightClass->seats_available,
            ];
        })->sortBy('price')->values();

        return [
            // Basic Info
            'id' => $this->id,
            'name' => $this->flight_number,
            'slug' => $this->flight_number, // Use flight number as slug
            'main_image' => $this->getDefaultImageUrl('thumb'),
            'main_image_full' => $this->getDefaultImageUrl(),
            
            // Time & Duration
            'time_start' => $timeStart ?? '00:00',
            'time_end' => $timeEnd ?? '00:00',
            'duration_hours' => $durationHours,
            'duration_minutes' => $durationMinutes,
            'departure_time' => $firstLeg->departure_time?->format('Y-m-d H:i:s'),
            'arrival_time' => $firstLeg->arrival_time?->format('Y-m-d H:i:s'),
            
            // Pricing
            'price_per_person' => (float) $cheapestPrice,
            'currency' => config('app.currency', 'USD'),
            'price_range' => [
                'min' => (float) ($this->flightClasses->min('price_per_seat') ?? 0),
                'max' => (float) ($this->flightClasses->max('price_per_seat') ?? 0),
            ],
            
            // Description & Highlights
            'short_description' => $route,
            'highlight' => $route,
            'highlights' => [
                $route,
                "Carrier: {$carrierName}",
                "Aircraft: {$aircraftModel}",
            ],
            
            // Availability
            'availability' => $availabilityStatus,
            'available_slots' => $totalAvailableSeats,
            'is_available' => $isAvailable,
            
            // Guide & Transportation
            'guide_type' => 'Flight crew',
            'transportation' => 'Aircraft',
            'transport_included' => true,
            
            // Flight Specific
            'carrier' => [
                'id' => $this->carrier->id ?? null,
                'name' => $carrierName,
                'code' => $this->carrier->code ?? null,
            ],
            'aircraft' => [
                'id' => $this->aircraft->id ?? null,
                'model' => $aircraftModel,
                'total_seats' => $this->aircraft->total_seats ?? null,
            ],
            'route' => $route,
            'origin' => [
                'city' => $firstLeg->originAirport->city ?? null,
                'airport_name' => $firstLeg->originAirport->airport_name ?? null,
                'airport_code' => $firstLeg->originAirport->airport_code ?? null,
            ],
            'destination' => [
                'city' => $firstLeg->destinationAirport->city ?? null,
                'airport_name' => $firstLeg->destinationAirport->airport_name ?? null,
                'airport_code' => $firstLeg->destinationAirport->airport_code ?? null,
            ],
            'classes' => $availableClasses,
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

        $bgColor = '3B82F6';
        $textColor = 'FFFFFF';
        $text = urlencode('Flight Image');

        return "https://dummyimage.com/{$width}x{$height}/{$bgColor}/{$textColor}&text={$text}";
    }
}

