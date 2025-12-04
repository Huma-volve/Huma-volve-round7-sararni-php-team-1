<?php

namespace App\Services;

use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    protected int $expirationMinutes = 10;

    protected int $codeLength = 4;

    public function generateCode(int $length = 4): string
    {
        // Use default OTP code in development/local environment
        if (app()->environment(['local', 'development']) || config('app.debug', false)) {
            return '1234';
        }

        return str_pad((string) random_int(0, 9999), $length, '0', STR_PAD_LEFT);
    }

    public function createOtp(?User $user, string $email, string $purpose): OtpCode
    {
        // Invalidate previous unused OTPs for the same purpose
        if ($user) {
            OtpCode::where('user_id', $user->id)
                ->where('purpose', $purpose)
                ->where('used', false)
                ->update(['used' => true]);
        } else {
            OtpCode::where('email', $email)
                ->where('purpose', $purpose)
                ->where('used', false)
                ->update(['used' => true]);
        }

        $code = $this->generateCode($this->codeLength);

        return OtpCode::create([
            'user_id' => $user?->id,
            'email' => $email,
            'code' => $code,
            'purpose' => $purpose,
            'expires_at' => now()->addMinutes($this->expirationMinutes),
            'used' => false,
        ]);
    }

    public function verifyOtp(?User $user, string $email, string $code, string $purpose): bool
    {
        $query = OtpCode::where('code', $code)
            ->where('purpose', $purpose)
            ->where('used', false)
            ->where('expires_at', '>', now());

        if ($user) {
            $query->where('user_id', $user->id);
        } else {
            $query->where('email', $email);
        }

        $otp = $query->first();

        if (! $otp || ! $otp->isValid()) {
            return false;
        }

        $otp->markAsUsed();

        return true;
    }

    public function sendOtpEmail(string $email, string $code, string $purpose): void
    {
        try {
            $subject = match ($purpose) {
                'verification' => 'Verify Your Email Address',
                'password_reset' => 'Reset Your Password',
                default => 'Your Verification Code',
            };

            Mail::raw("Your verification code is: {$code}\n\nThis code will expire in {$this->expirationMinutes} minutes.", function ($message) use ($email, $subject) {
                $message->to($email)
                    ->subject($subject);
            });
        } catch (\Exception $e) {
            Log::error('Failed to send OTP email', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function resendOtp(?User $user, string $email, string $purpose): OtpCode
    {
        return $this->createOtp($user, $email, $purpose);
    }
}
