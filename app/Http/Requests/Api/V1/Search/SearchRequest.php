<?php

namespace App\Http\Requests\Api\V1\Search;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q' => ['required', 'string', 'min:2', 'max:255'],
            'types' => ['nullable', 'array'],
            'types.*' => ['string', 'in:tours,hotels,cars,flights'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'price_min' => ['nullable', 'numeric', 'min:0'],
            'price_max' => ['nullable', 'numeric', 'min:0', 'gt:price_min'],
            'rating_min' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'date' => ['nullable', 'date', 'after_or_equal:today'],
            'location_lat' => ['nullable', 'numeric', 'required_with:location_lng'],
            'location_lng' => ['nullable', 'numeric', 'required_with:location_lat'],
            'radius' => ['nullable', 'numeric', 'min:1', 'max:500'],
            'difficulty' => ['nullable', 'in:easy,moderate,hard'],
            'languages' => ['nullable', 'array'],
            'languages.*' => ['string', 'in:en,ar'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:255'],
            'featured' => ['nullable', 'boolean'],
            'stars' => ['nullable', 'integer', 'min:1', 'max:5'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'page' => ['nullable', 'integer', 'min:1'],
            'page_size' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'q.required' => __('validation.required', ['attribute' => 'search query']),
            'q.min' => __('validation.min.string', ['attribute' => 'search query', 'min' => 2]),
            'q.max' => __('validation.max.string', ['attribute' => 'search query', 'max' => 255]),
            'types.*.in' => __('validation.in', ['attribute' => 'search type']),
            'location_lat.required_with' => __('validation.required_with', ['attribute' => 'location_lat', 'values' => 'location_lng']),
            'location_lng.required_with' => __('validation.required_with', ['attribute' => 'location_lng', 'values' => 'location_lat']),
        ];
    }
}
