<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
          return [
            'id'             => $this->id,
            'user_id'        => $this->user_id,
            'category_id'    => $this->category_id,
            'item_id'        => $this->item_id,
            'rate_plan_id'   => $this->rate_plan_id,

            'total_price'    => number_format($this->total_price, 2),
            'status'         => $this->status,
            'payment_status' => $this->payment_status,

            'check_in_date'     => $this->check_in_date,
            'check_out_date'       => $this->check_out_date,

             'adults' => $this->adults,
             'children' => $this->children,
             'infants' => $this->infants,

            'created_at'     => $this->created_at->format('Y-m-d H:i'),
            'updated_at'     => $this->updated_at->format('Y-m-d H:i'),
      

            // 👉 Room
            'room' => [
                'id'              => $this->room->id,
                'hotel_id'        => $this->room->hotel_id,
                'name'            => $this->room->name,
                'occupancy'       => json_decode($this->room->occupancy, true),
                'price_per_night' => $this->room->price_per_night,
                'room_type'       => $this->room->room_type,
                'description'     => $this->room->description,
                'extras'          => json_decode($this->room->extras, true),

                // 👉 Hotel inside room
                'hotel' => [
                    'id'            => $this->room->hotel->id,
                    'location_id'   => $this->room->hotel->location_id,
                    'name'          => $this->room->hotel->name,
                    'amenities'     => $this->room->hotel->amenities,
                    'policies'      => json_decode($this->room->hotel->policies, true),
                    'stars'         => $this->room->hotel->stars,
                    'rooms_count'   => $this->room->hotel->rooms_count,
                    'description'   => $this->room->hotel->description,
                    'contact_info'  => $this->room->hotel->contact_info,

                    // 👉 Location
                    'location' => [
                             'id'      => $this->room->hotel->location->id,
                            'city'    => $this->room->hotel->location->city,
                            'country' => $this->room->hotel->location->country,
                    ]




                ],
            ],
        ];
    }
}
