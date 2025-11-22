<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'code'          => $this->code,
            'description'   => $this->description,

            'occupancy' =>json_decode( $this->occupancy),

            'area'          => (float) $this->area,
            'room_type'     => $this->room_type,

            'extras'        =>json_decode($this->extras), // JSON as array


            'hotel' => [
                'id'    => $this->hotel->id,
                'name'  => $this->hotel->name,
                'stars' => $this->hotel->stars,
            ],

            // لو عايز ترجع خطط التسعير
            'rate_plans' => RatePlanResource::collection($this->whenLoaded('ratePlans')),

            'created_at'    => $this->created_at?->format('Y-m-d H:i'),
        ];;
    }
}
