<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FlightDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'flight_number' => $this->flight_number,
            'carrier' => [
                'name' => $this->carrier->carrier_name,
                'code' => $this->carrier->code
            ],
            'aircraft' => [
                'model' => $this->aircraft->model,
                'total_seats' => $this->aircraft->total_seats
            ],
            'flight_legs' => $this->flightLegs->map(function ($leg) {
                return [
                    'leg_number' => $leg->leg_number,
                    'origin' => $leg->originAirport->city . ' (' . $leg->originAirport->airport_code . ')',
                    'destination' => $leg->destinationAirport->city . ' (' . $leg->destinationAirport->airport_code . ')',
                    'departure_time' => $leg->departure_time?->format('H:i:s'),
                    'arrival_time' => $leg->arrival_time?->format('H:i:s'),
                    'duration_minutes' => $leg->duration_minutes
                ];
            }),
            'available_classes' => $this->flightClasses->map(function ($flightClass) {
                return [
                    'class_id' => $flightClass->class_id,
                    'class_name' => $flightClass->class->class_name,
                    'price_per_seat' => $flightClass->price_per_seat,
                    'seats_available' => $flightClass->seats_available,
                    'baggage_rules' => $flightClass->baggage_rules,
                    'fare_conditions' => $flightClass->fare_conditions,
                    'refundable' => $flightClass->refundable
                ];
            }),
            // 
            'seats_summary' => [
                'total_seats' => $this->flightSeats->count(),
                'available_seats' => $this->flightSeats->where('status', 'available')->count(),
                'reserved_seats' => $this->flightSeats->where('status', 'reserved')->count(),
                'classes' => $this->flightSeats->groupBy('class.class_name')->map(function ($seats, $className) {
                    $classSeats = $seats->map(function ($seat) {
                        return [
                            'id' => $seat->id,
                            'seat_number' => $seat->seat_number,
                            'status' => $seat->status,
                            'price' => $seat->price,
                            'is_available' => $seat->status === 'available'
                        ];
                    })->sortBy('seat_number')->values();

                    return [
                        'class_name' => $className,
                        'total_seats' => $classSeats->count(),
                        'available_seats' => $classSeats->where('is_available', true)->count(),
                        'seats' => $classSeats,
                        
                    ];
                })
            ],
        ];
    }
}
