<?php

namespace App\Http\Requests\Api\V1\Booking;

use Illuminate\Foundation\Http\FormRequest;

class CreateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tour_id' => ['required', 'exists:tours,id'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'time' => ['nullable', 'date_format:H:i'],
            'adults' => ['required', 'integer', 'min:1'],
            'children' => ['nullable', 'integer', 'min:0'],
            'infants' => ['nullable', 'integer', 'min:0'],
            'special_requests' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'tour_id.required' => __('validation.required', ['attribute' => 'tour']),
            'tour_id.exists' => __('validation.exists', ['attribute' => 'tour']),
            'date.required' => __('validation.required', ['attribute' => 'date']),
            'date.date' => __('validation.date', ['attribute' => 'date']),
            'date.after_or_equal' => __('validation.after_or_equal', ['attribute' => 'date', 'date' => 'today']),
            'time.date_format' => __('validation.date_format', ['attribute' => 'time', 'format' => 'H:i']),
            'adults.required' => __('validation.required', ['attribute' => 'adults']),
            'adults.integer' => __('validation.integer', ['attribute' => 'adults']),
            'adults.min' => __('validation.min.numeric', ['attribute' => 'adults', 'min' => 1]),
            'children.integer' => __('validation.integer', ['attribute' => 'children']),
            'children.min' => __('validation.min.numeric', ['attribute' => 'children', 'min' => 0]),
            'infants.integer' => __('validation.integer', ['attribute' => 'infants']),
            'infants.min' => __('validation.min.numeric', ['attribute' => 'infants', 'min' => 0]),
            'special_requests.max' => __('validation.max.string', ['attribute' => 'special requests', 'max' => 1000]),
        ];
    }
}
