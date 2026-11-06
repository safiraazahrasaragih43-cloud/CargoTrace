<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Register a new user with secure defaults.
     */
    public function register(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => strtolower(trim($data['email'])),
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? User::ROLE_CUSTOMER,
            'phone' => $data['phone'] ?? null,
            'company_name' => $data['company_name'] ?? null,
        ]);
    }

    /**
     * Authenticate user credentials and return the user instance if valid.
     */
    public function authenticate(string $email, string $password): User
    {
        $user = User::where('email', strtolower(trim($email)))->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials do not match our records.'],
            ]);
        }

        return $user;
    }

    /**
     * Issue an API token with Sanctum.
     */
    public function createApiToken(User $user, string $deviceName = 'auth-token'): string
    {
        // Add abilities based on user role
        $abilities = match ($user->role) {
            User::ROLE_ADMIN => ['*'],
            User::ROLE_OPERATOR => ['shipments:create', 'shipments:read', 'shipments:update', 'tracking:create', 'exceptions:manage'],
            default => ['shipments:read-own'],
        };

        return $user->createToken($deviceName, $abilities)->plainTextToken;
    }

    /**
     * Revoke current access token.
     */
    public function revokeCurrentToken(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }

    /**
     * Revoke all tokens for user.
     */
    public function revokeAllTokens(User $user): void
    {
        $user->tokens()->delete();
    }
}

