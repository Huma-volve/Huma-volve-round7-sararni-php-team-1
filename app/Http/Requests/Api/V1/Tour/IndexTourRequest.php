<?php

namespace App\Http\Requests\Api\V1\Tour;

use Illuminate\Foundation\Http\FormRequest;

class IndexTourRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['nullable', 'exists:categories,id'],
            'price_min' => ['nullable', 'numeric', 'min:0'],
            'price_max' => ['nullable', 'numeric', 'min:0', 'gt:price_min'],
            'rating_min' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'date' => ['nullable', 'date', 'after_or_equal:today'],
            'location_lat' => ['nullable', 'numeric', 'required_with:location_lng'],
            'location_lng' => ['nullable', 'numeric', 'required_with:location_lat'],
            'difficulty' => ['nullable', 'in:easy,moderate,hard'],
            'languages' => ['nullable', 'array'],
            'languages.*' => ['string', 'in:en,ar'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:255'],
            'featured' => ['nullable', 'boolean'],
            'page' => ['nullable', 'integer', 'min:1'],
            'page_size' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.exists' => __('validation.exists', ['attribute' => 'category']),
            'price_min.numeric' => __('validation.numeric', ['attribute' => 'price_min']),
            'price_max.numeric' => __('validation.numeric', ['attribute' => 'price_max']),
            'price_max.gt' => __('validation.gt.numeric', ['attribute' => 'price_max', 'value' => 'price_min']),
            'rating_min.numeric' => __('validation.numeric', ['attribute' => 'rating_min']),
            'date.date' => __('validation.date', ['attribute' => 'date']),
            'date.after_or_equal' => __('validation.after_or_equal', ['attribute' => 'date', 'date' => 'today']),
            'location_lat.numeric' => __('validation.numeric', ['attribute' => 'location_lat']),
            'location_lat.required_with' => __('validation.required_with', ['attribute' => 'location_lat', 'values' => 'location_lng']),
            'location_lng.numeric' => __('validation.numeric', ['attribute' => 'location_lng']),
            'location_lng.required_with' => __('validation.required_with', ['attribute' => 'location_lng', 'values' => 'location_lat']),
            'difficulty.in' => __('validation.in', ['attribute' => 'difficulty']),
            'languages.array' => __('validation.array', ['attribute' => 'languages']),
            'tags.array' => __('validation.array', ['attribute' => 'tags']),
        ];
    }
}
