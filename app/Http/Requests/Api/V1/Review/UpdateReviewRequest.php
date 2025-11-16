<?php

namespace App\Http\Requests\Api\V1\Review;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rating' => ['sometimes', 'integer', 'min:1', 'max:5'],
            'title' => ['nullable', 'string', 'max:255'],
            'comment' => ['sometimes', 'string', 'min:10', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'rating.integer' => __('validation.integer', ['attribute' => 'rating']),
            'rating.min' => __('validation.min.numeric', ['attribute' => 'rating', 'min' => 1]),
            'rating.max' => __('validation.max.numeric', ['attribute' => 'rating', 'max' => 5]),
            'title.max' => __('validation.max.string', ['attribute' => 'title', 'max' => 255]),
            'comment.min' => __('validation.min.string', ['attribute' => 'comment', 'min' => 10]),
            'comment.max' => __('validation.max.string', ['attribute' => 'comment', 'max' => 2000]),
        ];
    }
}
