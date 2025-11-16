<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'booking_number' => $this->booking_number,
            'tour' => new TourResource($this->whenLoaded('tour')),
            'tour_date' => $this->tour_date->format('Y-m-d'),
            'tour_time' => $this->tour_time ? (is_string($this->tour_time) ? $this->tour_time : $this->tour_time->format('H:i:s')) : null,
            'participants' => [
                'adults' => $this->adults_count,
                'children' => $this->children_count,
                'infants' => $this->infants_count,
            ],
            'pricing' => [
                'adult_price' => (float) $this->adult_price,
                'child_price' => $this->child_price ? (float) $this->child_price : null,
                'infant_price' => $this->infant_price ? (float) $this->infant_price : null,
                'discount_amount' => (float) $this->discount_amount,
                'total_amount' => (float) $this->total_amount,
                'paid_amount' => (float) $this->paid_amount,
                'currency' => config('app.currency', 'USD'),
            ],
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'payment_method' => $this->payment_method,
            'special_requests' => $this->special_requests,
            'cancellation_reason' => $this->cancellation_reason,
            'cancelled_at' => $this->cancelled_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
