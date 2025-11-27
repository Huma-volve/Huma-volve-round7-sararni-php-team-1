<?php

namespace App\Http\Requests\Api\V1\Tour;

use Illuminate\Foundation\Http\FormRequest;

class RecommendTourRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'featured' => ['nullable', 'boolean'],
            'price_min' => ['nullable', 'numeric', 'min:0'],
            'price_max' => ['nullable', 'numeric', 'min:0', 'gte:price_min'],
            'difficulty' => ['nullable', 'in:easy,moderate,hard'],
            'languages' => ['nullable', 'array'],
            'languages.*' => ['string'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string'],
            'location_lat' => ['nullable', 'numeric', 'required_with:location_lng'],
            'location_lng' => ['nullable', 'numeric', 'required_with:location_lat'],
            'radius' => ['nullable', 'numeric', 'min:1', 'max:500'],
        ];
    }
}
