<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCarRequest extends FormRequest
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
            //
            'brand_id'=>['required', 'exists:brands,id'],
            'model' =>['nullable', 'string'],
            'category'=>['nullable', 'string'],
            'make' =>['nullable' , 'integer' ,'min:1900'],
            'seats_count'=>['required', 'integer'],
            'doors' =>['required', 'integer'],
            'fuel_type'=>['nullable', 'string'],
            'transmission' =>['required', 'in:Manual,Automatic'],
            'luggage_capacity'=>['nullable', 'integer'],
            'air_conditioning' =>['nullable', 'boolean'],
            'features'=>['nullable', 'json'],
            'pickup_location_id' =>['nullable', 'exists:locations,id'],
            'dropoff_location_id' =>['nullable', 'exists:locations,id'],
            'license_requirements'=>['nullable', 'string'],
            'availability_calendar' =>['nullable', 'string'],
            'cancellation_policy'=>['nullable', 'string'],
            'price_tiers' => 'nullable|array',
            'price_tiers.*.from_hours' => 'nullable|integer',
            'price_tiers.*.to_hours' => 'nullable|integer',
            'price_tiers.*.price_per_hour' => 'nullable|numeric',
            'price_tiers.*.price_per_day' => 'nullable|numeric',
        ];
    }
}
