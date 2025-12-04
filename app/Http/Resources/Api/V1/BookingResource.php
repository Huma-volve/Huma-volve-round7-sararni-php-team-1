<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $details = $this->whenLoaded('details');
        $meta = $details?->meta ?? [];

        // Get item based on category (using accessor)
        $item = null;
        if ($this->item) {
            if ($this->category === 'tour') {
                $item = new TourResource($this->item);

            } elseif ($this->category === 'flight') {
                $item = [
                    'id' => $this->item->id,
                    'flight_number' => $this->item->flight_number,
                    'carrier' => $this->item->carrier->carrier_name ?? null,
                ];
            } else {
                // For other categories, return basic item info
                $item = [
                    'id' => $this->item->id,
                    'name' => $this->item->name ?? $this->item->slug ?? null,
                ];
            }
        }

        return [
            'id' => $this->id,
            'booking_reference' => $this->booking_reference,
            'category' => $this->category,
            'item' => $item,

            'flight_details' => $this->when($this->category === 'flight', function () use ($meta) {



                return [
                    'trip_type' => $this->trip_type,
                    'departure_time' => $this->departure_time?->format('H:i:s'),
                    'arrival_time' => $this->arrival_time?->format('H:i:s'),
                ];
            }),
            'booking_date' => $this->booking_date->format('Y-m-d'),
            'booking_time' => $this->booking_time ? (is_string($this->booking_time) ? $this->booking_time : $this->booking_time->format('H:i:s')) : null,
            'check_in_date' => $this->check_in_date?->format('Y-m-d'),
            'check_out_date' => $this->check_out_date?->format('Y-m-d'),
            'pickup_date' => $this->pickup_date?->format('Y-m-d'),
            'dropoff_date' => $this->dropoff_date?->format('Y-m-d'),
            'participants' => $this->when($this->relationLoaded('participants'), function () {
                return $this->participants->map(function ($participant) {
                    return [
                        'id' => $participant->id,
                        'title' => $participant->title,
                        'full_name' => $participant->full_name,
                        'first_name' => $participant->first_name,
                        'last_name' => $participant->last_name,
                        'email' => $participant->email,
                        'phone' => $participant->phone,
                        'seat_number' => $participant->seat_number,

                        'type' => $participant->type,
                        'passport_number' => $participant->passport_number,
                        'seats' => $participant->bookingFlights->map(function ($bf) {
                            return [
                                'direction' => $bf->direction,
                                'price' => $bf->price,
                                'flight_id' => $bf->flight_id,

                                'seat' => [
                                    'seat_id' => $bf->flightSeat->id,
                                    'seat_number' => $bf->flightSeat->seat_number,
                                    'class' => $bf->flightSeat->class->name ?? null,
                                ],
                            ];
                        }),
                    ];
                });
            }, function () use ($meta) {
                // Fallback to meta data for backward compatibility
                return [
                    'adults' => $meta['adults_count'] ?? 0,
                    'children' => $meta['children_count'] ?? 0,
                    'infants' => $meta['infants_count'] ?? 0,
                ];
            }),
            'pricing' => [
                'total_price' => (float) $this->total_price,
                'currency' => $this->currency,
                'payment_status' => $this->payment_status,
                'payment_method' => $this->payment_method,
            ],
            'details' => $this->when($this->relationLoaded('details'), function () use ($meta) {
                return $meta;
            }),

            'status' => $this->status,
            'special_requests' => $this->special_requests,
            'cancellation_reason' => $this->cancellation_reason,
            'cancelled_at' => $this->cancelled_at?->toIso8601String(),
            'cancelled_by' => $this->cancelled_by,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
