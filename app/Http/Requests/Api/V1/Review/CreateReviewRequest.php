<?php

namespace App\Http\Requests\Api\V1\Review;

use Illuminate\Foundation\Http\FormRequest;

class CreateReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tour_id' => ['required', 'exists:tours,id'],
            'booking_id' => ['required', 'exists:bookings,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['nullable', 'string', 'max:255'],
            'comment' => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'tour_id.required' => __('validation.required', ['attribute' => 'tour']),
            'tour_id.exists' => __('validation.exists', ['attribute' => 'tour']),
            'booking_id.required' => __('validation.required', ['attribute' => 'booking']),
            'booking_id.exists' => __('validation.exists', ['attribute' => 'booking']),
            'rating.required' => __('validation.required', ['attribute' => 'rating']),
            'rating.integer' => __('validation.integer', ['attribute' => 'rating']),
            'rating.min' => __('validation.min.numeric', ['attribute' => 'rating', 'min' => 1]),
            'rating.max' => __('validation.max.numeric', ['attribute' => 'rating', 'max' => 5]),
            'title.max' => __('validation.max.string', ['attribute' => 'title', 'max' => 255]),
            'comment.required' => __('validation.required', ['attribute' => 'comment']),
            'comment.min' => __('validation.min.string', ['attribute' => 'comment', 'min' => 10]),
            'comment.max' => __('validation.max.string', ['attribute' => 'comment', 'max' => 2000]),
        ];
    }
}
