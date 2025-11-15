<?php

return [
    'name.required' => 'The name field is required.',
    'email.required' => 'The email field is required.',
    'email.email' => 'The email must be a valid email address.',
    'email.unique' => 'The email has already been taken.',
    'email.exists' => 'The selected email is invalid.',
    'password.required' => 'The password field is required.',
    'password.min' => 'The password must be at least :min characters.',
    'password.confirmed' => 'The password confirmation does not match.',
    'otp.required' => 'The OTP field is required.',
    'otp.size' => 'The OTP must be :size digits.',
    'current_password.required' => 'The current password field is required.',
    'phone.max' => 'The phone number must not exceed :max characters.',
    'location.max' => 'The location must not exceed :max characters.',

    // Common validation messages
    'required' => 'The :attribute field is required.',
    'exists' => 'The selected :attribute is invalid.',
    'numeric' => 'The :attribute must be a number.',
    'date' => 'The :attribute is not a valid date.',
    'after_or_equal' => 'The :attribute must be a date after or equal to :date.',
    'required_with' => 'The :attribute field is required when :values is present.',
    'in' => 'The selected :attribute is invalid.',
    'array' => 'The :attribute must be an array.',
    'integer' => 'The :attribute must be an integer.',
    'min.numeric' => 'The :attribute must be at least :min.',
    'max.numeric' => 'The :attribute must not be greater than :max.',
    'max.string' => 'The :attribute must not be greater than :max characters.',
    'min.string' => 'The :attribute must be at least :min characters.',
    'date_format' => 'The :attribute does not match the format :format.',
    'gt.numeric' => 'The :attribute must be greater than :value.',
];
