<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class CompareRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category' => ['required', 'string', 'in:tour,flight,car,hotel'],
            'item_ids' => ['required', 'array', 'min:1', 'max:10'],
            'item_ids.*' => ['required', 'integer'],
            'date' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'category.required' => __('validation.required', ['attribute' => 'category']),
            'category.in' => __('validation.in', ['attribute' => 'category', 'values' => 'tour, flight, car, hotel']),
            'item_ids.required' => __('validation.required', ['attribute' => 'item_ids']),
            'item_ids.array' => __('validation.array', ['attribute' => 'item_ids']),
            'item_ids.min' => __('validation.min.array', ['attribute' => 'item_ids', 'min' => 1]),
            'item_ids.max' => __('validation.max.array', ['attribute' => 'item_ids', 'max' => 10]),
            'item_ids.*.required' => __('validation.required', ['attribute' => 'item id']),
            'item_ids.*.integer' => __('validation.integer', ['attribute' => 'item id']),
            'date.date' => __('validation.date', ['attribute' => 'date']),
            'date.after_or_equal' => __('validation.after_or_equal', ['attribute' => 'date', 'date' => 'today']),
        ];
    }

    /**
     * Configure the validator instance to add dynamic exists rule
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $category = $this->input('category');
            $itemIds = $this->input('item_ids', []);

            if ($category && !empty($itemIds)) {
                $table = match ($category) {
                    'tour' => 'tours',
                    'flight' => 'flights',
                    'car' => 'cars',
                    'hotel' => 'hotels',
                    default => null,
                };

                if ($table) {
                    foreach ($itemIds as $index => $itemId) {
                        $exists = \Illuminate\Support\Facades\DB::table($table)->where('id', $itemId)->exists();
                        if (!$exists) {
                            $validator->errors()->add("item_ids.{$index}", __('validation.exists', ['attribute' => $category]));
                        }
                    }
                }
            }
        });
    }
}

