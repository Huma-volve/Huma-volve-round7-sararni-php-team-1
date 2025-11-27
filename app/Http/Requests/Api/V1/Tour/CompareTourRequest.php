<?php

namespace App\Http\Requests\Api\V1\Tour;

use Illuminate\Foundation\Http\FormRequest;

class CompareTourRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tour_ids' => ['required', 'array', 'min:1', 'max:10'],
            'tour_ids.*' => ['required', 'integer', 'exists:tours,id'],
            'date' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'tour_ids.required' => __('validation.required', ['attribute' => 'tour_ids']),
            'tour_ids.array' => __('validation.array', ['attribute' => 'tour_ids']),
            'tour_ids.min' => __('validation.min.array', ['attribute' => 'tour_ids', 'min' => 1]),
            'tour_ids.max' => __('validation.max.array', ['attribute' => 'tour_ids', 'max' => 10]),
            'tour_ids.*.required' => __('validation.required', ['attribute' => 'tour id']),
            'tour_ids.*.integer' => __('validation.integer', ['attribute' => 'tour id']),
            'tour_ids.*.exists' => __('validation.exists', ['attribute' => 'tour']),
            'date.date' => __('validation.date', ['attribute' => 'date']),
            'date.after_or_equal' => __('validation.after_or_equal', ['attribute' => 'date', 'date' => 'today']),
        ];
    }
}

