<?php

namespace App\Http\Requests\Api\V1\Question;

use Illuminate\Foundation\Http\FormRequest;

class AskQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tour_id' => ['required', 'exists:tours,id'],
            'question' => ['required', 'string', 'min:10', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'tour_id.required' => __('validation.required', ['attribute' => 'tour']),
            'tour_id.exists' => __('validation.exists', ['attribute' => 'tour']),
            'question.required' => __('validation.required', ['attribute' => 'question']),
            'question.min' => __('validation.min.string', ['attribute' => 'question', 'min' => 10]),
            'question.max' => __('validation.max.string', ['attribute' => 'question', 'max' => 1000]),
        ];
    }
}
