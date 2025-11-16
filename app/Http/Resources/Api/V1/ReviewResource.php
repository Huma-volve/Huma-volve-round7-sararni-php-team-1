<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id ?? null,
                'name' => $this->user->name ?? null,
                'avatar' => $this->user->profile_photo_url ?? null,
            ],
            'rating' => $this->rating,
            'title' => $this->title,
            'comment' => $this->comment,
            'photos' => [], // TODO: Add photos support if needed
            'helpful_count' => 0, // TODO: Add helpful votes if needed
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
