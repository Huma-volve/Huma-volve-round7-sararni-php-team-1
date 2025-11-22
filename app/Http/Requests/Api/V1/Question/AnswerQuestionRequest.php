<?php

namespace App\Http\Requests\Api\V1\Question;

use Illuminate\Foundation\Http\FormRequest;

class AnswerQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user) {
            return false;
        }

        return $user->hasAnyRole(['admin', 'super_admin', 'support_agent', 'support_manager']);
    }

    protected function failedAuthorization(): void
    {
        throw new \Illuminate\Http\Exceptions\HttpResponseException(
            response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => __('messages.error.unauthorized') ?? 'This action is unauthorized. Only admins and support staff can answer questions.',
                ],
            ], 403)
        );
    }

    public function rules(): array
    {
        return [
            'answer' => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'answer.required' => __('validation.required', ['attribute' => 'answer']),
            'answer.min' => __('validation.min.string', ['attribute' => 'answer', 'min' => 10]),
            'answer.max' => __('validation.max.string', ['attribute' => 'answer', 'max' => 2000]),
        ];
    }
}
