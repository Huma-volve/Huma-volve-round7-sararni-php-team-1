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
<<<<<<< HEAD
            'price_tiers.*.from_hours' => 'nullable|integer',
            'price_tiers.*.to_hours' => 'nullable|integer',
            'price_tiers.*.price_per_hour' => 'nullable|numeric',
            'price_tiers.*.price_per_day' => 'nullable|numeric',
=======
            'price_tiers.*.from_hours' => ['nullable','integer'],
            'price_tiers.*.to_hours' => ['nullable','integer'],
            'price_tiers.*.price_per_hour' => ['required','numeric'],
            'price_tiers.*.price_per_day' => ['required','numeric'],
            'image' => 'nullable|image|max:2048',
            'images.*' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',

>>>>>>> 6e876ba9d73195e746d0ed47df06f9269b0e177e
        ];
    }
}
