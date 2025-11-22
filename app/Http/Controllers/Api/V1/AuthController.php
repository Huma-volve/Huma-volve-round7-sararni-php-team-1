<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\ChangePasswordRequest;
use App\Http\Requests\Api\V1\Auth\ForgotPasswordRequest;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;
use App\Http\Requests\Api\V1\Auth\ResetPasswordRequest;
use App\Http\Requests\Api\V1\Auth\VerifyOtpRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use App\Services\GoogleOAuthService;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(
        protected OtpService $otpService,
        protected GoogleOAuthService $googleOAuthService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_verified' => false,
        ]);

        // Assign customer role
        $user->assignRole('customer');

        // Create and send OTP
        $otp = $this->otpService->createOtp($user, $user->email, 'verification');
        $this->otpService->sendOtpEmail($user->email, $otp->code, 'verification');

        return response()->json([
            'success' => true,
            'message' => __('messages.register.success'),
            'data' => [
                'user' => new UserResource($user),
            ],
        ], 201);
    }

    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'USER_NOT_FOUND',
                    'message' => __('messages.error.user_not_found'),
                ],
            ], 404);
        }

        $isValid = $this->otpService->verifyOtp($user, $request->email, $request->otp, 'verification');

        if (! $isValid) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'INVALID_OTP',
                    'message' => __('messages.error.invalid_otp'),
                ],
            ], 422);
        }

        $user->update([
            'is_verified' => true,
            'email_verified_at' => now(),
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => __('messages.verify_otp.success'),
            'data' => [
                'user' => new UserResource($user),
                'token' => $token,
            ],
        ]);
    }

    public function resendOtp(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'exists:users,email'],
        ]);

        $key = 'resend-otp:'.$request->email;

        if (RateLimiter::tooManyAttempts($key, 3)) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'TOO_MANY_ATTEMPTS',
                    'message' => __('messages.error.too_many_attempts'),
                ],
            ], 429);
        }

        RateLimiter::hit($key, 60); // 1 minute

        $user = User::where('email', $request->email)->first();

        $otp = $this->otpService->resendOtp($user, $user->email, 'verification');
        $this->otpService->sendOtpEmail($user->email, $otp->code, 'verification');

        return response()->json([
            'success' => true,
            'message' => __('messages.resend_otp.success'),
        ]);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => [__('messages.login.invalid_credentials')],
            ]);
        }

        if (! $user->is_verified) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'EMAIL_NOT_VERIFIED',
                    'message' => __('messages.error.email_not_verified'),
                ],
            ], 403);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => __('messages.login.success'),
            'data' => [
                'user' => new UserResource($user),
                'token' => $token,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => __('messages.logout.success'),
        ]);
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $key = 'forgot-password:'.$request->email;

        if (RateLimiter::tooManyAttempts($key, 3)) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'TOO_MANY_ATTEMPTS',
                    'message' => __('messages.error.too_many_attempts'),
                ],
            ], 429);
        }

        RateLimiter::hit($key, 60); // 1 minute

        $user = User::where('email', $request->email)->first();

        $otp = $this->otpService->createOtp($user, $user->email, 'password_reset');
        $this->otpService->sendOtpEmail($user->email, $otp->code, 'password_reset');

        return response()->json([
            'success' => true,
            'message' => __('messages.forgot_password.success'),
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'USER_NOT_FOUND',
                    'message' => __('messages.error.user_not_found'),
                ],
            ], 404);
        }

        $isValid = $this->otpService->verifyOtp($user, $request->email, $request->otp, 'password_reset');

        if (! $isValid) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'INVALID_OTP',
                    'message' => __('messages.error.invalid_otp'),
                ],
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => __('messages.reset_password.success'),
        ]);
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'INVALID_PASSWORD',
                    'message' => __('messages.error.invalid_password'),
                ],
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => __('messages.change_password.success'),
        ]);
    }

    public function getGoogleAuthUrl(Request $request): JsonResponse
    {
        $state = $request->input('state', bin2hex(random_bytes(16)));

        $url = $this->googleOAuthService->getAuthUrl($state);

        return response()->json([
            'success' => true,
            'data' => [
                'url' => $url,
                'state' => $state,
            ],
        ]);
    }

    public function exchangeGoogleCode(Request $request): JsonResponse
    {
        $request->validate([
            'code' => ['required', 'string'],
            'redirect_uri' => ['sometimes', 'string', 'url'],
            'code_verifier' => ['sometimes', 'string'],
        ]);

        try {
            $tokenData = $this->googleOAuthService->exchangeCodeForToken(
                $request->code,
                $request->code_verifier
            );

            $googleUser = $this->googleOAuthService->getUserInfo($tokenData['access_token']);

            $user = $this->googleOAuthService->findOrCreateUser($googleUser);

            $isNewUser = $user->wasRecentlyCreated;

            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => __('messages.google.auth_success'),
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $token,
                    'is_new_user' => $isNewUser,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'OAUTH_ERROR',
                    'message' => __('messages.error.oauth_error'),
                ],
            ], 400);
        }
    }

    public function linkGoogleAccount(Request $request): JsonResponse
    {
        $request->validate([
            'code' => ['required', 'string'],
            'code_verifier' => ['sometimes', 'string'],
        ]);

        try {
            $tokenData = $this->googleOAuthService->exchangeCodeForToken(
                $request->code,
                $request->code_verifier
            );

            $googleUser = $this->googleOAuthService->getUserInfo($tokenData['access_token']);

            $this->googleOAuthService->linkAccount($request->user(), $googleUser);

            return response()->json([
                'success' => true,
                'message' => __('messages.google.link_success'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'LINK_ERROR',
                    'message' => __('messages.error.link_error'),
                ],
            ], 400);
        }
    }

    public function unlinkGoogleAccount(Request $request): JsonResponse
    {
        try {
            $this->googleOAuthService->unlinkAccount($request->user(), 'google');

            return response()->json([
                'success' => true,
                'message' => __('messages.google.unlink_success'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNLINK_ERROR',
                    'message' => __('messages.error.unlink_error'),
                ],
            ], 400);
        }
    }

    public function getProviders(Request $request): JsonResponse
    {
        $providers = $request->user()->socialIdentities->pluck('provider');

        return response()->json([
            'success' => true,
            'data' => [
                'providers' => $providers,
            ],
        ]);
    }
}
