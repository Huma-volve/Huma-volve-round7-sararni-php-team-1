<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RatePlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
          return [
            'id'                  => $this->id,
            'name'                => $this->name,
            'base_price'          => $this->base_price,
            'currency'            => $this->currency,
            'refundable'          => $this->refundable,
            'cancellation_policy' => $this->cancellation_policy,
            'pricing_rules'       => json_decode($this->pricing_rules),
            'extras'              => json_decode($this->extras),
            'created_at'          => $this->created_at->format('Y-m-d H:i'),
        ];
    }
}
