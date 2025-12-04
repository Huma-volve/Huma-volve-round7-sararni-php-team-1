<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
                'hotel_id'      => 'required|exists:hotels,id',
                'room_id'       => 'required|exists:rooms,id',
                'rate_plan_id'  => 'required|exists:rate_plans,id',
                'check_in_date'    => 'required|date',
                'check_out_date'      => 'required|date|after_or_equal:check_in_date',
                'adults' => 'required|integer|min:1',
                'children' => 'nullable|integer|min:0',
                'infants' => 'nullable|integer|min:0',
                 'special_requests'   => 'nullable|string|max:1000',
        ];


    }


        public function messages()
    {
        return [
            'guest_details.adults.required' => 'يرجى تحديد عدد البالغين',
            'check_out_date.after_or_equal'       => 'تاريخ المغادرة يجب أن يكون بعد الوصول',
        ];
    }
}
