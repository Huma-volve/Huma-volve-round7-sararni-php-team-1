<?php

namespace App\Services;

use App\Models\SocialIdentity;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleOAuthService
{
    protected string $clientId;

    protected string $clientSecret;

    protected string $redirectUri;

    public function __construct()
    {

        $this->clientId = config('services.google.client_id');
        $this->clientSecret = config('services.google.client_secret');
        $this->redirectUri = config('services.google.redirect');
    }

    public function getAuthUrl(?string $state = null): string
    {
        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'access_type' => 'offline',
            'prompt' => 'consent',
        ];

        if ($state) {
            $params['state'] = $state;
        }

        return 'https://accounts.google.com/o/oauth2/v2/auth?'.http_build_query($params);
    }

    public function exchangeCodeForToken(string $code, ?string $codeVerifier = null): array
    {
        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->redirectUri,
            'code_verifier' => $codeVerifier,
        ]);

        if (! $response->successful()) {
            Log::error('Google OAuth token exchange failed', [
                'response' => $response->body(),
            ]);
            throw new \Exception('Failed to exchange code for token');
        }

        return $response->json();
    }

    public function getUserInfo(string $accessToken): array
    {
        $response = Http::withToken($accessToken)
            ->get('https://www.googleapis.com/oauth2/v2/userinfo');

        if (! $response->successful()) {
            Log::error('Failed to get Google user info', [
                'response' => $response->body(),
            ]);
            throw new \Exception('Failed to get user info from Google');
        }

        return $response->json();
    }

    public function findOrCreateUser(array $googleUser): User
    {
        // Check if social identity exists
        $socialIdentity = SocialIdentity::where('provider', 'google')
            ->where('provider_user_id', $googleUser['id'])
            ->first();

        if ($socialIdentity) {
            return $socialIdentity->user;
        }

        // Check if user with email exists
        $user = User::where('email', $googleUser['email'])->first();

        if ($user) {
            // Link Google account to existing user
            SocialIdentity::create([
                'user_id' => $user->id,
                'provider' => 'google',
                'provider_user_id' => $googleUser['id'],
                'email' => $googleUser['email'],
                'profile_json' => $googleUser,
            ]);

            return $user;
        }

        // Create new user
        $user = User::create([
            'name' => $googleUser['name'],
            'email' => $googleUser['email'],
            'password' => bcrypt(str()->random(32)), // Random password
            'is_verified' => $googleUser['verified_email'] ?? false,
            'email_verified_at' => $googleUser['verified_email'] ?? false ? now() : null,
        ]);

        // Assign customer role
        $user->assignRole('customer');

        // Create social identity
        SocialIdentity::create([
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_user_id' => $googleUser['id'],
            'email' => $googleUser['email'],
            'profile_json' => $googleUser,
        ]);

        return $user;
    }

    public function linkAccount(User $user, array $googleUser): SocialIdentity
    {
        // Check if already linked
        $existing = SocialIdentity::where('user_id', $user->id)
            ->where('provider', 'google')
            ->first();

        if ($existing) {
            throw new \Exception('Google account is already linked to this user');
        }

        // Check if Google account is linked to another user
        $otherUser = SocialIdentity::where('provider', 'google')
            ->where('provider_user_id', $googleUser['id'])
            ->where('user_id', '!=', $user->id)
            ->first();

        if ($otherUser) {
            throw new \Exception('This Google account is already linked to another user');
        }

        return SocialIdentity::create([
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_user_id' => $googleUser['id'],
            'email' => $googleUser['email'],
            'profile_json' => $googleUser,
        ]);
    }

    public function unlinkAccount(User $user, string $provider = 'google'): bool
    {
        $socialIdentity = SocialIdentity::where('user_id', $user->id)
            ->where('provider', $provider)
            ->first();

        if (! $socialIdentity) {
            return false;
        }

        // Check if this is the only login method
        $hasPassword = ! empty($user->password);
        $otherProviders = SocialIdentity::where('user_id', $user->id)
            ->where('provider', '!=', $provider)
            ->exists();

        if (! $hasPassword && ! $otherProviders) {
            throw new \Exception('Cannot unlink the only login method. Please set a password first.');
        }

        return $socialIdentity->delete();
    }
}
