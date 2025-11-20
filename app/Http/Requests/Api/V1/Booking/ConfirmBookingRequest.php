<?php

namespace App\Http\Requests\Api\V1\Booking;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_method' => ['required', 'string', 'in:card,wallet,offline'],
            'payment_reference' => ['nullable', 'string', 'max:255'],
            'payment_data' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method.required' => __('validation.required', ['attribute' => 'payment method']),
            'payment_method.in' => __('validation.in', ['attribute' => 'payment method']),
            'payment_reference.max' => __('validation.max.string', ['attribute' => 'payment reference', 'max' => 255]),
            'payment_data.array' => __('validation.array', ['attribute' => 'payment data']),
        ];
    }
}
