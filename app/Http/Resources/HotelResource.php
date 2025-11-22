<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HotelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'slug'         => $this->slug,
            'amenities'    => explode(',', $this->amenities), // تحويل النص إلى مصفوفة
            'contact_info' => $this->contact_info,
            'policies'     => $this->policies, true,
            'location'     =>$this->whenLoaded('location' ,function () {
                            return [
                                    'id' => $this->location->id ?? null,
                                    'name' => $this->location->country ?? null,
                                    'city' => $this->location->city ?? null,
                                      ];
                                 }) ,
            'stars'        => $this->stars,
            'rooms_count'  => $this->rooms_count,
            'recommended'  => json_decode($this->recommended, true),
            'description'  => $this->description,
            'created_at'   => $this->created_at->format('Y-m-d H:i'),
        ];
    }
}
