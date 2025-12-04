<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FlightResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'flight_number' => $this->flight_number,
            'carrier' => $this->carrier->carrier_name,
            'aircraft' => $this->aircraft->model,
            'total_seats' => $this->aircraft->total_seats,
            'classes' => $this->flightClasses->map(function ($flightClass) {
                return [
                    'class_name' => $flightClass->class->class_name,
                    'price_per_seat' => $flightClass->price_per_seat,
                    'seats_available' => $flightClass->seats_available,
                    'refundable' => $flightClass->refundable
                ];
            }),
            'created_at' => $this->created_at?->format('Y-m-d'),
            'updated_at' => $this->updated_at?->format('Y-m-d'),
        ];
    }
}