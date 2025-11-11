<?php

return [
    // Auth messages
    'register.success' => 'Registration successful. Please verify your email with the OTP sent to your email.',
    'verify_otp.success' => 'Email verified successfully.',
    'verify_otp.invalid' => 'Invalid or expired OTP.',
    'verify_otp.user_not_found' => 'User not found.',
    'resend_otp.success' => 'OTP resent successfully.',
    'resend_otp.too_many_attempts' => 'Too many attempts. Please try again later.',
    'login.success' => 'Login successful.',
    'login.invalid_credentials' => 'The provided credentials are incorrect.',
    'login.email_not_verified' => 'Please verify your email address first.',
    'logout.success' => 'Logged out successfully.',
    'forgot_password.success' => 'OTP sent to your email. Please check your inbox.',
    'forgot_password.too_many_attempts' => 'Too many attempts. Please try again later.',
    'reset_password.success' => 'Password reset successfully.',
    'reset_password.invalid_otp' => 'Invalid or expired OTP.',
    'reset_password.user_not_found' => 'User not found.',
    'change_password.success' => 'Password changed successfully.',
    'change_password.invalid' => 'Current password is incorrect.',

    // Google OAuth messages
    'google.auth_success' => 'Authentication successful.',
    'google.link_success' => 'Google account linked successfully.',
    'google.unlink_success' => 'Google account unlinked successfully.',
    'google.oauth_error' => 'OAuth authentication failed.',
    'google.link_error' => 'Failed to link Google account.',
    'google.unlink_error' => 'Failed to unlink Google account.',

    // User messages
    'profile.updated' => 'Profile updated successfully.',

    // Error codes
    'error.user_not_found' => 'User not found.',
    'error.invalid_otp' => 'Invalid or expired OTP.',
    'error.too_many_attempts' => 'Too many attempts. Please try again later.',
    'error.email_not_verified' => 'Please verify your email address first.',
    'error.invalid_password' => 'Current password is incorrect.',
    'error.oauth_error' => 'OAuth authentication failed.',
    'error.link_error' => 'Failed to link account.',
    'error.unlink_error' => 'Failed to unlink account.',
];
